<?php

namespace VulcanPhp\Core\Helpers;

use Exception;
use VulcanPhp\EasyCurl\EasyCurl;

class Mixer
{
    public function enque(string $type, $value, int $piroty = 10, array $attrs = []): self
    {
        bucket()->push('mixer', ['piroty' => $piroty, 'attrs' => $attrs, 'value' => $value], $type);

        return $this;
    }

    public function unpkg(string $type, string $package, int $piroty = 10, array $attrs = []): self
    {
        if (!is_url($package)) {
            $package = cache()
                ->load(
                    $package . '.unpkg_cdn.' . $type,
                    fn () => $this->browse_cdn($type, $package, 'https://unpkg.com')
                );
        }

        return $this->enque($type, $package, $piroty, $attrs);
    }

    public function npm(string $type, string $package, int $piroty = 10, array $attrs = []): self
    {
        if (!is_url($package)) {
            $package = cache()
                ->load(
                    $package . '.cdn_npm.' . $type,
                    fn () => $this->browse_cdn($type, $package, 'https://cdn.jsdelivr.net/npm')
                );
        }

        return $this->enque($type, $package, $piroty, $attrs);
    }

    public static function create(): Mixer
    {
        return new Mixer;
    }

    protected function browse_cdn($type, $package, $host): string
    {
        $package = explode('@', $package);
        $version = isset($package[1]) ? '@' . $package[1] : '';
        $package = $package[0];

        // host/:package@:version/:file
        foreach ([
            sprintf('%s/%s%s/dist/%s/%s.min.%s', $host, $package, $version, $type, $package, $type),
            sprintf('%s/%s%s/dist/%s.min.%s', $host, $package, $version, $package, $type),
            sprintf('%s/%s%s/%s/%s.min.%s', $host, $package, $version, $type, $package, $type),
            sprintf('%s/%s%s/%s.min.%s', $host, $package, $version, $package, $type),
        ] as $unpkg) {
            $http = EasyCurl::get($unpkg);

            if ($http->getStatus() === 200) {
                return $http->getLastUrl();
            }
        }

        throw new Exception($host . ' Package: ' . $package . $version . ' does not found.');
    }

    public function deque(string $type, bool $include = false): string
    {
        $resource = '<!-- Start Deque Mixer(' . $type . ') -->' . "\n\t";

        // add_filer: deque_js | deque_css
        foreach (Arr::multisort((array) bucket('mixer', $type), 'piroty', true) as $script) {
            $resource .= $this->parse($type, $script['value'], $script['attrs'] ?? [], $include) . "\n\t";
        }

        $resource .= '<!-- End Deque Mixer(' . $type . ') -->' . "\n";

        return $resource;
    }

    public function bundle(string $type, string $path, bool $force = false)
    {
        $scripts    = '';
        $attrs      = [];
        $invalid    = $force || !file_exists($path);

        foreach (Arr::multisort((array) bucket('mixer', $type), 'piroty', true) as $script) {
            $attrs = array_merge($attrs, $script['attrs']);
            if ($invalid) {
                if (is_file($script['value']) || is_url($script['value'])) {
                    $scripts .= file_get_contents($script['value'], false, stream_context_create([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]));
                } else {
                    $scripts .= $script['value'];
                }
                $scripts .= "\n";
            }
        }

        if ($invalid) {
            file_put_contents($path, $scripts, LOCK_EX);
        }

        return $this->parse($type, $path, array_unique($attrs));
    }

    public function parse(string $type, string $script, array $attrs = [], bool $include = false): string
    {
        $type       = ['css' => 'style', 'js' => 'script'][$type];
        $use_file   = [
            'script' => '<script src="%s" type="text/javascript" %s></script>',
            'style'  => '<link href="%s" rel="stylesheet" type="text/css" %s>',
        ][$type];

        $attrs = join(' ', array_map(fn ($attr, $value) => sprintf('%s="%s"', $attr, $value), array_values($attrs), array_keys($attrs)));

        if (is_file($script) && file_exists($script)) {
            if ($include) {
                ob_start();
                echo '<' . $type . '>';
                include $script;
                echo '</' . $type . '>';
                return ob_get_clean();
            } else {
                return sprintf($use_file, $this->parseUrl($script), $attrs);
            }
        } elseif (is_url($script)) {
            return sprintf($use_file, trim($script, '/'), $attrs);
        }

        return '<' . $type . '>' . $script . '</' . $type . '>';
    }

    protected function parseUrl(string $script): string
    {
        return trim(home_url(str_replace(DIRECTORY_SEPARATOR, '/', str_ireplace(root_dir(), '', $script))), '/');
    }
}
