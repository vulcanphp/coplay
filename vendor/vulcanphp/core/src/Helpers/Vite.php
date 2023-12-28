<?php

namespace VulcanPhp\Core\Helpers;

/**
 * Vite App Helper
 * @package Core
 */
class Vite
{
    // vite default config
    protected const DEFAULT_CONFIG = [
        'scheme' => 'http://',
        'host' => 'localhost',
        'port' => 5133,
        'running' => null,
        'entry' => 'main.js',
        'dist' => null,
        'manifest' => null
    ];

    protected array $config;

    /** 
     * @param string $entry 
     * @return string 
     */
    public function __construct($config)
    {
        if (is_string($config)) {
            $config = ['entry' => $config];
        }

        $this->config = array_merge(self::DEFAULT_CONFIG, $config);
    }

    public function config(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * @param mixed $args 
     * @return static 
     */
    public static function create(...$args): static
    {
        return new static(...$args);
    }

    public function __toString(): string
    {
        return "<!-- Dynamic Vite Script <START> -->\n\t"
            . $this->jsTag($this->config('entry')) . "\n\t"
            . $this->jsPreloadImports($this->config('entry'))
            . "\n\t" . $this->cssTag($this->config('entry'))
            . "\n\t<!-- Dynamic Vite Script <END> -->\n";
    }

    protected function server_url($path = ''): string
    {
        return $this->config('scheme')
            . $this->config('host')
            . ':'
            . $this->config('port')
            . $this->getBase()
            . '/' . trim($path, '/');
    }

    protected function dist_url($file = ''): string
    {
        return trim(
            str_replace(
                DIRECTORY_SEPARATOR,
                '/',
                str_ireplace(
                    root_dir(),
                    trim(home_url(), '/'),
                    $this->config('dist', root_dir($this->getBase() . '/dist/'))
                )
            ),
            '/'
        ) . '/' . trim($file, '/');
    }

    /**
     * Some dev/prod mechanism would exist in your project
     * @param string $entry 
     * @return bool 
     */
    public function isRunning(string $entry): bool
    {
        if ($this->config('running') !== null) {
            return $this->config('running');
        }
        if (!is_dev()) {
            return false;
        }

        // live check if vite development server is running or not
        $handle = curl_init($this->server_url($entry));

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_TIMEOUT, 1);
        curl_exec($handle);

        $error = curl_errno($handle);

        curl_close($handle);

        return $this->config['running'] = !$error;
    }

    public function jsTag(string $entry): string
    {
        $url = $this->isRunning($entry)
            ? $this->server_url($entry)
            : $this->assetUrl($entry);

        if (!$url) {
            return '';
        }

        return '<script type="module" crossorigin src="' . $url . '"></script>';
    }

    /**
     * @param string $entry 
     * @return string 
     */
    public function jsPreloadImports(string $entry): string
    {
        if ($this->isRunning($entry)) {
            return '';
        }

        $res = '';

        foreach ($this->importsUrls($entry) as $url) {
            $res .= '<link rel="modulepreload" href="' . $url . '">';
        }

        return $res;
    }

    /**
     * not needed on dev, it's inject by Vite
     * @param string $entry 
     * @return string 
     */
    public function cssTag(string $entry): string
    {
        if ($this->isRunning($entry)) {
            return '';
        }

        $tags = '';

        foreach ($this->cssUrls($entry) as $url) {
            $tags .= '<link rel="stylesheet" href="' . $url . '">';
        }

        return $tags;
    }

    /**
     * @return array 
     */
    public function getManifest(): array
    {
        $manifest = $this->config(
            'dist',
            root_dir($this->getBase() . '/dist/')
        ) . DIRECTORY_SEPARATOR . 'manifest.json';

        return $this->config['manifest'] ??= (file_exists($manifest) ?
            (array) json_decode(file_get_contents($manifest), true)
            : []
        );
    }

    /**
     * @param string $entry 
     * @return string 
     */
    public function assetUrl(string $entry): string
    {
        $manifest = $this->getManifest();

        return isset($manifest[$entry]) ?
            $this->dist_url($manifest[$entry]['file'])
            : '';
    }

    /**
     * @param string $entry 
     * @return array 
     */
    public function importsUrls(string $entry): array
    {
        $urls = [];
        $manifest = $this->getManifest();

        if (!empty($manifest[$entry]['imports'])) {
            foreach ($manifest[$entry]['imports'] as $imports) {
                $urls[] = $this->dist_url($manifest[$imports]['file']);
            }
        }

        return $urls;
    }

    /**
     * @param string $entry 
     * @return array 
     */
    public function cssUrls(string $entry): array
    {
        $urls = [];
        $manifest = $this->getManifest();

        if (!empty($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $file) {
                $urls[] = $this->dist_url($file);
            }
        }

        return $urls;
    }

    protected function getBase(): string
    {
        return '/' . trim(str_replace([root_dir(), DIRECTORY_SEPARATOR], ['', '/'], config('app.vite_dir')), '/');
    }
}
