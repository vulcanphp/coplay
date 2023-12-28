<?php

namespace VulcanPhp\Translator\Engine\Google;

use JsonException;
use VulcanPhp\Translator\Engine\Google\Tokens\GoogleTokenGenerator;
use VulcanPhp\Translator\Exceptions\TranslatorException;
use VulcanPhp\Translator\Interfaces\IGoogleTokenProvider;
use VulcanPhp\Translator\Interfaces\ITranslatorEngine;

class GoogleTranslator implements ITranslatorEngine
{
    protected IGoogleTokenProvider $tokenProvider;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new TranslatorException('Extension: Curl is required for GoogleTranslatorEngine');
        }

        $this->setTokenProvider(new GoogleTokenGenerator);
    }

    public static function create(): GoogleTranslator
    {
        return new GoogleTranslator;
    }

    public function getTokenProvider(): IGoogleTokenProvider
    {
        return $this->tokenProvider;
    }

    public function setTokenProvider(IGoogleTokenProvider $provider): self
    {
        $this->tokenProvider = $provider;
        return $this;
    }

    public function translateFromString(string $text, string $source, string $convert): string
    {
        return array_values($this->translateFromArray((array) $text, $source, $convert))[0];
    }

    public function translateFromArray(array $texts, string $source, string $convert): array
    {
        if ($source === $convert) {
            return array_combine(array_values($texts), array_values($texts));
        }

        $separator  = '(##)';
        $replace    = ['( # # )', '( ## )', '( ##)', '(## )'];
        $texts      = array_filter(array_map(fn ($text) => trim($text), $texts));
        $translate  = join("\n$separator", $texts);

        if (empty($translate)) {
            return [];
        }

        $translated = [];

        foreach ($this->translateFromGoogle($translate, $source, $convert)[0] as $sentence) {
            $translated[] = isset($sentence[0]) ? ' ' . $sentence[0] : '';
        }

        $translated = array_filter(
            array_map(
                fn ($sentence) => trim(str_ireplace("\n", '', $sentence)),
                explode($separator, str_ireplace($replace, $separator, join('', $translated)))
            )
        );

        return count($texts) === count($translated) ? array_filter(array_combine($texts, $translated)) : [];
    }

    public function translateFromGoogle(string $translate, string $source, string $convert): array
    {
        try {
            $queryArray = $this->queryParams([
                'sl' => $source,
                'tl' => $convert,
                'tk' => $this->getTokenProvider()->generateToken($translate),
                'q'  => $translate,
            ]);

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'https://translate.google.com/translate_a/single?' . preg_replace('/%5B\d+%5D=/', '=', http_build_query($queryArray)));
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 20);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_ENCODING, 'utf-8');
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Origin: https://translate.google.com',
                'Referer: https://translate.google.com/'
            ]);

            ob_start();
            echo curl_exec($curl);
            // Modify body to avoid json errors
            $bodyJson = preg_replace(['/,+/', '/\[,/'], [',', '['], ob_get_clean());
        } catch (\Throwable $e) {
            throw new TranslatorException('Google detected unusual traffic from your computer network, try again later (2 - 48 hours)');
        }

        // Decode JSON data
        try {
            $sentencesArray = json_decode($bodyJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TranslatorException('Data cannot be decoded or it is deeper than the recursion limit');
        }

        return $sentencesArray;
    }

    protected function queryParams(array $params): array
    {
        return array_merge([
            'client'   => 'gtx',
            'hl'       => 'en',
            'dt'       => [
                't', // Translate
                'bd', // Full translate with synonym ($bodyArray[1])
                'at', // Other translate ($bodyArray[5] - in google translate page this shows when click on translated word)
                'ex', // Example part ($bodyArray[13])
                'ld', // I don't know ($bodyArray[8])
                'md', // Definition part with example ($bodyArray[12])
                'qca', // I don't know ($bodyArray[8])
                'rw', // Read also part ($bodyArray[14])
                'rm', // I don't know
                'ss', // Full synonym ($bodyArray[11])
            ],
            'sl'       => null, // Source language
            'tl'       => null, // Target language
            'q'        => null, // String to translate
            'ie'       => 'utf-8', // Input encoding
            'oe'       => 'utf-8', // Output encoding
            'multires' => 1,
            'otf'      => 0,
            'pc'       => 1,
            'trs'      => 1,
            'ssel'     => 0,
            'tsel'     => 0,
            'kc'       => 1,
            'tk'       => null,
        ], $params);
    }
}
