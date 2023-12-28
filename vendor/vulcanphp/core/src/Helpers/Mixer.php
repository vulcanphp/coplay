<?php

namespace VulcanPhp\Core\Helpers;

use Exception;
use VulcanPhp\EasyCurl\EasyCurl;

class Mixer
{
    public function enque(string $type, $value, int $piroty = 10): self
    {
        bucket()->push('mixer', ['piroty' => $piroty,    'value' => $value], $type);

        return $this;
    }

    public function unpkg(string $type, string $package, int $piroty = 10): self
    {
        if (!is_url($package)) {
            $package = cache()
                ->load(
                    $package . '.unpkg_cdn.' . $type,
                    fn () => $this->browse_cdn($type, $package, 'https://unpkg.com')
                );
        }

        return $this->enque($type, $package, $piroty);
    }

    public function npm(string $type, string $package, int $piroty = 10): self
    {
        if (!is_url($package)) {
            $package = cache()
                ->load(
                    $package . '.cdn_npm.' . $type,
                    fn () => $this->browse_cdn($type, $package, 'https://cdn.jsdelivr.net/npm')
                );
        }

        return $this->enque($type, $package, $piroty);
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

    public static function deque(string $type, bool $include = false): string
    {
        $resource = '<!-- Start Deque Mixer(' . $type . ') -->' . "\n\t";

        // add_filer: deque_js | deque_css
        foreach (array_column(Arr::multisort((array) bucket('mixer', $type), 'piroty', true), 'value') as $script) {
            $resource .= self::parse($type, $script, $include) . "\n\t";
        }

        $resource .= '<!-- End Deque Mixer(' . $type . ') -->' . "\n";

        return $resource;
    }

    public static function parse(string $type, string $script, bool $include): string
    {
        $type       = ['css' => 'style', 'js' => 'script'][$type];
        $use_file   = [
            'script' => '<script src="%s" type="text/javascript"></script>',
            'style'  => '<link rel="stylesheet" type="text/css" href="%s">',
        ];

        if (is_file($script) && file_exists($script)) {
            if ($include) {
                ob_start();
                echo '<' . $type . '>';
                include $script;
                echo '</' . $type . '>';
                return ob_get_clean();
            } else {
                return sprintf($use_file[$type], trim(home_url(str_replace(DIRECTORY_SEPARATOR, '/', str_ireplace(root_dir(), '', $script))), '/'));
            }
        } elseif (is_url($script)) {
            return sprintf($use_file[$type], trim($script, '/'));
        }

        return '<' . $type . '>' . $script . '</' . $type . '>';
    }
}
