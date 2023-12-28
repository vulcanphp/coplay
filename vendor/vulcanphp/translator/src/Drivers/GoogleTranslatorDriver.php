<?php

namespace VulcanPhp\Translator\Drivers;

use VulcanPhp\Translator\Engine\Google\GoogleTranslator;
use VulcanPhp\Translator\Interfaces\ITranslatorDriver;
use VulcanPhp\Translator\Interfaces\ITranslatorEngine;
use VulcanPhp\Translator\Interfaces\ITranslatorManager;

class GoogleTranslatorDriver implements ITranslatorDriver
{
    protected ITranslatorEngine $Engine;
    protected ITranslatorManager $Manager;

    protected array $lazyText = [];
    protected bool $isLazy = false, $changedLocal = false;

    public function __construct(ITranslatorManager $Manager)
    {
        $this->setEngine(new GoogleTranslator);

        $this->setManager($Manager);
    }

    public function translate($text = ''): ?string
    {
        if (
            $this->getManager()->getSourceLanguage() === $this->getManager()->getLanguage()
            || $this->isInvalidText($text)
        ) {
            return $text;
        }

        // check if translated text available on local
        if ($this->getManager()->hasLocal($this->getHash($text))) {
            return $this->getManager()
                ->getLocal($this->getHash($text));
        }

        // check if lazy feature is on
        if ($this->isLazy()) {
            $this->addToLazyText($text);
            return $this->getLazyHash($text);
        }

        // translate from engine and save it to local then serve it
        $translated = $this->getEngine()
            ->translateFromString(
                $text,
                $this->getManager()->getSourceLanguage(),
                $this->getManager()->getLanguage()
            );

        // update local translated file
        $this->getManager()
            ->updateLocal($this->getHash($text), $translated);

        if (!$this->changedLocal) {
            $this->changedLocal = true;
        }

        return $translated;
    }

    public function addToLazyText(string $text): void
    {
        $this->lazyText[$this->getLazyHash($text)] = $text;
    }

    public function isLazy(): bool
    {
        return $this->isLazy === true;
    }

    public function enableLazy(): void
    {
        $this->isLazy = true;

        // start buffering to replace lazy text output on later
        ob_start();
    }

    protected function getLazyHash(string $text): string
    {
        return '[lazy:' . $this->getHash($text) . ']';
    }

    protected function getHash(string $text): string
    {
        return trim(
            strtolower(
                preg_replace('/[^a-zA-Z0-9\<\>\#\*\_]+/i', '-', trim(html_entity_decode($text)))
            ),
            '-'
        );
    }

    protected function isInvalidText($text = ''): bool
    {
        return $text === null
            || empty(trim($text))
            || strlen($text) >= 5000
            || (intval($text) > 0 && intval($text) == trim($text));
    }

    public function getLazyTexts(): array
    {
        return $this->lazyText;
    }

    public function __destruct()
    {
        // save local translated file
        if ($this->changedLocal) {
            $this->getManager()->saveLocal();
        }

        // translate all lazy texts and render
        if ($this->isLazy()) {
            // translate all lazy text from Google
            if (!empty($this->getLazyTexts())) {
                $texts = $this->getEngine()
                    ->translateFromArray(
                        $this->getLazyTexts(),
                        $this->getManager()->getSourceLanguage(),
                        $this->getManager()->getLanguage()
                    );

                if (!empty($texts)) {
                    foreach ($texts as $original => $sentence) {
                        $this->lazyText[$this->getLazyHash($original)] = $sentence;
                        $this->getManager()
                            ->updateLocal($this->getHash($original), $sentence);
                    }

                    $this->getManager()->saveLocal();
                }
            }

            // get the output buffering
            $output = ob_get_clean();

            // update lazy text from output buffering
            if (!empty($this->getLazyTexts())) {
                if (is_array($output)) {
                    echo json_decode(
                        str_ireplace(
                            array_keys($this->lazyText),
                            array_values($this->lazyText),
                            json_encode($output)
                        ),
                        true,
                        JSON_UNESCAPED_UNICODE
                    );
                } else {
                    echo str_ireplace(
                        array_keys($this->lazyText),
                        array_values($this->lazyText),
                        $output
                    );
                }
            } else {
                echo $output;
            }
        }
    }

    public function setEngine(ITranslatorEngine $Engine): void
    {
        $this->Engine = $Engine;
    }

    public function getEngine(): ITranslatorEngine
    {
        return $this->Engine;
    }

    public function setManager(ITranslatorManager $Manager): void
    {
        $this->Manager = $Manager;
    }

    public function getManager(): ITranslatorManager
    {
        return $this->Manager;
    }
}
