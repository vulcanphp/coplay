<?php

namespace VulcanPhp\EasyCurl\Interfaces;

interface ICurlDriver
{
    public function send(string $url, array $params = []): ICurlResponse;

    public function setOption(int $key, mixed $value): ICurlDriver;

    public function setHeader(string $key, string $value): ICurlDriver;

    public function setCookieFile(string $filepath): ICurlDriver;

    public function setDownloadFile(string $filepath, bool $override = false): ICurlDriver;

    public function setPostFields(mixed $fields): ICurlDriver;

    public function setUseragent(string $useragent): ICurlDriver;

    public function setProxy(array $proxy): ICurlDriver;
}
