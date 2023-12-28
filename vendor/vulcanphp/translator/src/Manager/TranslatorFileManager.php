<?php

namespace VulcanPhp\Translator\Manager;

use VulcanPhp\Translator\Exceptions\TranslatorManagerException;
use VulcanPhp\Translator\Interfaces\ITranslatorManager;

class TranslatorFileManager implements ITranslatorManager
{
    protected array $config, $localData;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'source' => 'en',
            'convert' => 'en',
            'suffix' => null,
            'local_dir' => null,
        ], $config);

        $this->LoadLocal();
    }

    public function getLanguage(): string
    {
        return $this->config['convert'];
    }

    public function getSourceLanguage(): string
    {
        return $this->config['source'];
    }

    public function getLocal(string $key = null): ?string
    {
        return $this->localData[$key] ?? null;
    }

    public function hasLocal(string $key): bool
    {
        return isset($this->localData[$key]);
    }

    public function setLocal(array $LocalData): void
    {
        $this->localData = $LocalData;
        $this->saveLocal();
    }

    public function updateLocal(string $key, string $data): void
    {
        $this->localData[$key] = $data;
    }

    public function saveLocal(): void
    {
        $localfile = $this->getLocalFile();
        $directory = dirname($localfile);

        if ((!is_dir($directory) && !mkdir($directory, 0777, true))
            || (!is_writable($directory) && !chmod($directory, 0777))
        ) {
            throw new TranslatorManagerException('Invalid Translator Local Direcotry');
        }

        file_put_contents(
            $localfile,
            json_encode($this->localData, JSON_UNESCAPED_UNICODE)
        );
    }

    protected function LoadLocal(): void
    {
        if (!isset($this->localData)) {
            $localfile = $this->getLocalFile();
            if (file_exists($localfile)) {
                $this->localData = (array) json_decode(
                    file_get_contents($localfile),
                    true
                );
            } else {
                $this->localData = [];
            }
        }
    }

    protected function getLocalFile()
    {
        return ($this->config['local_dir'] ?? getcwd() . '/translate')
            . DIRECTORY_SEPARATOR
            . $this->getLanguage()
            . (isset($this->config['suffix']) ? '-' . $this->config['suffix'] : '')
            . '.json';
    }
}
