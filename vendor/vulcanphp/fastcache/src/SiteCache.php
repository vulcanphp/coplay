<?php

namespace VulcanPhp\FastCache;

use VulcanPhp\FastCache\Exceptions\SimpleCacheException;
use VulcanPhp\FastCache\Interfaces\ISiteCache;

class SiteCache implements ISiteCache
{
    public static SiteCache $instance;

    protected array $config, $when;
    protected bool $valid, $ob_started;

    public function __construct(array $config = [], array $when = [])
    {
        self::$instance = $this;

        $this->when     = $when;
        $this->config   = array_merge([
            'except'    => [],
            'include'   => [],
            'methods'   => ['get'],
            'useragent' => [],
            'bots'      => false,
            'expire'    => '10 minutes',
            'tmp_dir'   => null,
            'extension' => '.html',
            'minify'    => false,
            'limit'     => 100,
            'metadata'  => true,
        ], $config);
    }

    public static function setup(...$args): SiteCache
    {
        return new SiteCache(...$args);
    }

    public function serve(): void
    {
        if ($this->isValid()) {
            // clean caches
            $this->clean();

            // serve cache if exists
            $filepath = $this->getCacheFile();

            // render from cache
            if (file_exists($filepath)) {

                // Cache Header
                $this->cacheHeaders($filepath);

                ob_start();

                if ($this->config['metadata']) {
                    echo $this->getCacheMetadata();
                }

                include $filepath;

                echo ob_get_clean();

                exit;
            }

            // start ob and cache output
            $this->ob_started = true;
            ob_start();
        }
    }

    public function __destruct()
    {
        if (($this->ob_started ?? false) === true && $this->isValid()) {
            $output = ob_get_clean();

            if ($this->config['minify']) {
                $output = preg_replace(['/^ {2,}/m', '/^\t{2,}/m', '~[\r\n]+~'], '',  $output);
            }

            // check direcotry
            $directory = dirname($this->getCacheFile());
            if ((!is_dir($directory) && !mkdir($directory, 0777, true))
                || (!is_writable($directory) && !chmod($directory, 0777))
            ) {
                throw new SimpleCacheException('Tmp Directory is not accessible');
            }

            // create cache file
            if (!empty($output)) {
                file_put_contents($this->getCacheFile(), $output);
            }

            echo $output;
        }
    }

    public function clean(): void
    {
        $ext = str_replace('.', '', $this->config['extension']);
        $caches = [];

        // clean expired caches
        foreach (scandir($this->getTmpDir()) as $cache) {
            if (pathinfo($cache, PATHINFO_EXTENSION) != $ext) {
                continue;
            }

            $cache = $this->getTmpDir($cache);
            $mtime = filemtime($cache);

            if ($mtime < strtotime("-{$this->config['expire']}")) {
                unlink($cache);
                continue;
            }

            $caches[] = ['time' => $mtime, 'path' => $cache];
        }

        // delete limited caches
        if (count($caches) > $this->config['limit']) {
            array_multisort(array_column($caches, 'time'), SORT_DESC, $caches, SORT_NUMERIC);
            array_splice($caches, 0, $this->config['limit']);

            foreach ($caches as $cache) {
                unlink($cache['path']);
            }
        }
    }

    public function flush(): void
    {
        $ext = str_replace('.', '', $this->config['extension']);

        foreach (scandir($this->getTmpDir()) as $cache) {
            if (pathinfo($cache, PATHINFO_EXTENSION) != $ext) {
                continue;
            }

            unlink($this->getTmpDir($cache));
        }
    }

    public function isValid(): bool
    {
        if (isset($this->valid)) {
            return $this->valid;
        }

        // check with method
        if (!in_array(strtolower($_SERVER['REQUEST_METHOD']), (array) $this->config['methods'])) {
            return $this->valid = false;
        }

        // bot checker...
        if (
            $this->config['bots'] !== null
            && $this->config['bots'] !== true
            && $this->isBot()
        ) {
            // when useragent is a bot
            if (
                $this->config['bots'] === false
                || empty($this->config['bots'])
                || preg_match($this->escapeRegex($this->config['bots']), $_SERVER['HTTP_USER_AGENT']) === 0
            ) {
                return $this->valid = false;
            }
        }

        // check useragent
        if (
            !empty($this->config['useragent'])
            && preg_match($this->escapeRegex($this->config['useragent']), $_SERVER['HTTP_USER_AGENT']) === 0
        ) {
            return $this->valid = false;
        }

        // check with accepted path
        foreach ((array) $this->config['except'] as $url) {

            $path   = $this->parsePath($_SERVER['PATH_INFO'] ?? '/');
            $url    = rtrim($url, '/');

            if ($url[strlen($url) - 1] === '*') {
                $skip = stripos($path, $this->parsePath($url)) !== false;
            } else {
                $skip = ($this->parsePath($url) === $path);
            }

            if ($skip === true) {
                foreach ((array) $this->config['include'] as $includeUrl) {
                    $includeUrl = rtrim($includeUrl, '/');
                    if ($includeUrl[strlen($includeUrl) - 1] === '*') {
                        $skip = stripos($path, $this->parsePath($includeUrl)) === false;
                        break;
                    }

                    $skip = !($this->parsePath($includeUrl) === $path);
                }

                if ($skip === false) {
                    continue;
                }

                return $this->valid = false;
            }
        }

        // check with condition
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $global = [
            'server'    => $_SERVER,
            'session'   => $_SESSION,
            'cookie'    => $_COOKIE,
            'get'       => $_GET,
            'post'      => $_POST,
            'request'   => $_REQUEST
        ];

        foreach ((array) $this->when as $rule => $accept) {
            $rule       = explode('.', $rule);
            $var        = array_shift($rule);
            $condition  = '=';

            if (substr($var, 0, 1) == '!') {
                $var = substr($var, 1);
                $condition = '!';
            }

            $value = (array) $global[$var] ?? [];

            foreach ($rule as $r) {
                if ($value === null) {
                    continue;
                }

                $value = $value[$r] ?? null;
            }

            if (($condition == '=' && $value != $accept)
                || ($condition == '!' && $value == $accept)
            ) {
                return $this->valid = false;
            }
        }

        return $this->valid = true;
    }

    protected function parsePath(string $path): string
    {
        return preg_replace('~/+~', '/', '/' . trim(str_replace('*', '', $path), '/') . '/');
    }

    protected function getCacheFile(): string
    {
        return $this->getTmpDir()
            . sha1($this->parsePath($_SERVER['REQUEST_URI'] ?? '/'))
            . $this->config['extension'];
    }

    protected function isBot(): bool
    {
        return preg_match(
            '/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosuserAgentcrawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i',
            $_SERVER['HTTP_USER_AGENT']
        ) === 1;
    }

    protected function escapeRegex($texts): string
    {
        return '/' . join(
            '|',
            array_map(
                fn ($text) => str_replace('/', '\/', preg_quote($text)),
                (array) $texts
            )
        ) . '/i';
    }

    protected function getTmpDir($path = ''): string
    {
        return ($this->config['tmp_dir'] ?? sys_get_temp_dir()) . '/' . $path;
    }

    protected function cacheHeaders(string $filepath): void
    {
        $timestamp  = filemtime($filepath);
        $maxAge     = $timestamp - strtotime("-{$this->config['expire']}");

        $IsModifiedSince  = true;
        $allHeaders       = getallheaders();

        // check last modified header..
        if (array_key_exists("If-Modified-Since", $allHeaders)) {
            $gmtSinceDate   = $allHeaders["If-Modified-Since"];
            $sinceTimestamp = strtotime($gmtSinceDate);

            // Can the browser get it from the cache?
            if ($sinceTimestamp != false && $timestamp <= $sinceTimestamp) {
                $IsModifiedSince = false;
            }
        }

        if ($IsModifiedSince) {
            header("HTTP/1.1 200 OK", true);
            header("Pragma: cache", true);
            header("Cache-Control: public, max-age=$maxAge", true);
            header("Last-Modified: " . gmdate("D, j M Y H:i:s", $timestamp) . " GMT", true);
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + $maxAge) . " GMT", true);
        } else {
            header("HTTP/1.1 304 Not Modified", true);
            header("Cache-Control: public, max-age=$maxAge", true);
            exit;
        }
    }

    protected function getCacheMetadata(): string
    {
        $timestamp  = filemtime($this->getCacheFile());
        $maxAge     = $timestamp - strtotime("-{$this->config['expire']}");

        return sprintf(
            <<<EOT
            <!-- Served From: Fast Cache -->
            <!-- Served At: %s -->
            <!-- Last-Modified: %s -->
            <!-- Expires: %s -->
            <!-- Learn More: https://github.com/vulcanphp/fastcache  -->
            
            EOT,
            gmdate("D, d M Y H:i:s", time()) . " GMT",
            gmdate("D, j M Y H:i:s", $timestamp) . " GMT",
            gmdate("D, d M Y H:i:s", time() + $maxAge) . " GMT",
        );
    }
}
