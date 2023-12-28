<?php

namespace VulcanPhp\Core\Console;

use VulcanPhp\Core\Console\Exceptions\ViteException;

class Vite
{
    protected array $features = [
        'vue'           => false,
        'tailwind'      => false,
        'postcss'       => false,
        'autoprefixer'  => false,
        'vanilla'       => true,
    ];

    public function __construct()
    {
        $this->features['vue']      = $this->ask('do you want to generate (VUE JS) with vite? (y/n): ');
        $this->features['tailwind'] = $this->ask('would you like to use (Tailwind CSS) with vite? (y/n): ');

        if ($this->features['tailwind']) {
            $this->features['postcss']      = $this->ask('would you like to install (Post Css)? (y/n): ');
            $this->features['autoprefixer'] = $this->ask('would you like to install (Auto Prefixer)? (y/n): ');
        }

        $this->features['vanilla']  = $this->features['vue'] === false;
    }

    public function generate()
    {
        foreach (require __DIR__ . '/Schema/vite.php' as $schema) {

            // check root feature
            if (isset($schema['feature']) && !empty($schema['feature'])) {
                if (!$this->checkFeatures($schema['feature'])) {
                    continue;
                }
            }

            if (isset($schema['not_feature']) && !empty($schema['not_feature'])) {
                if ($this->checkFeatures($schema['not_feature'])) {
                    continue;
                }
            }

            $contents = [];

            foreach ($schema['contents'] ?? [] as $content) {

                // check child feature
                if (isset($content['feature']) && !empty($content['feature'])) {
                    if (!$this->checkFeatures($content['feature'])) {
                        continue;
                    }
                }

                if (isset($content['not_feature']) && !empty($content['not_feature'])) {
                    if ($this->checkFeatures($content['not_feature'])) {
                        continue;
                    }
                }

                if (isset($content['file'])) {
                    if (file_exists($content['file']) || filter_var($content['file'], FILTER_VALIDATE_URL)) {
                        $contents[] = file_get_contents($content['file']);
                    } else {
                        throw new ViteException('Base File: ' . $content['file'] . ' does not found');
                    }
                } else {
                    $contents[] = $content['text'] ?? '';
                }
            }

            $file = root_dir('/' . $schema['file']);

            if (!is_dir(dirname($file)) && !mkdir(dirname($file), 0777, true)) {
                echo "\n\033[31mcannot access to the file system\033[0m\n";
                exit;
            }

            if (!is_writable(dirname($file)) && !chmod(dirname($file), 0777)) {
                echo "\n\033[31mcannot change the mode of file system\033[0m\n";
                exit;
            }

            $forced = false;
            if (file_exists($file)) {
                $forced = $this->ask(basename($file) . ' is already exists, do you want to override it? (y/n): ');
            }

            if (!file_exists($file) || $forced) {
                if (file_put_contents($file, join('', $contents)) === false) {
                    echo "\n\033[31mfailed to generate vite\033[0m\n";
                    exit;
                }
            }
        }

        echo "\n\033[32mVite: has been created successfully\033[0m\n";
        echo "\033[36mNPM: installing\033[0m \033[33m(this can take few moments)\033\n\n";
        echo exec('npm install');
        echo "\n\033[32m(npm run dev) to start vite server \033[0m\n";
    }

    protected function hasFeature(string $name): bool
    {
        return $this->features[$name] ?? false;
    }

    protected function checkFeatures($features): bool
    {
        foreach ((array) $features as $feature) {
            if (!$this->hasFeature($feature)) {
                return false;
            }
        }

        return true;
    }

    protected function ask(string $message): bool
    {
        return in_array(
            strtolower(trim((string) readline($message))),
            ['y', 'yes', 'ya', 'yap', 'yas']
        );
    }
}
