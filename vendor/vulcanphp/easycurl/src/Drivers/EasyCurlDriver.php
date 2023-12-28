<?php

namespace VulcanPhp\EasyCurl\Drivers;

use VulcanPhp\EasyCurl\EasyCurlResponse;
use VulcanPhp\EasyCurl\Exceptions\EasyCurlException;
use VulcanPhp\EasyCurl\Exceptions\EasyCurlProxyException;
use VulcanPhp\EasyCurl\Interfaces\ICurlDriver;
use VulcanPhp\EasyCurl\Interfaces\ICurlResponse;

class EasyCurlDriver implements ICurlDriver
{
    protected array $config = [
        'headers'   => [],
        'options'   => [],
        'download'  => null,
        'useragent' => null,
    ];

    public function send(string $url, array $params = []): ICurlResponse
    {
        // initialize new curl
        $curl = curl_init();

        if ($curl === false) {
            throw new EasyCurlException('Failed to initialize cUrl.');
        }

        // curl default options
        $options = [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING       => 'utf-8',
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        ];

        foreach ($this->config['options'] as $option => $value) {
            $options[$option] = $value;
        }

        // set curl options
        foreach ($options as $option => $value) {
            curl_setopt($curl, $option, $value);
        }

        // set curl headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->config['headers']);

        // set useragent if exists
        if (isset($this->config['useragent'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $this->config['useragent']);
        }

        // apply curl url parameters
        if (!empty($params)) {
            $url = sprintf('%s%s%s', $url, strpos($url, '?') === false ? '?' : '&', http_build_query($params));
        }

        // set curl url
        curl_setopt($curl, CURLOPT_URL, $url);

        // if download file exists
        if (isset($this->config['download'])) {
            $download = fopen($this->config['download'], 'w+');
            curl_setopt($curl, CURLOPT_FILE, $download);
        }

        // exec curl
        ob_start();
        echo curl_exec($curl);

        $response = [
            'body'     => ob_get_clean(),
            'status'   => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            'last_url' => curl_getinfo($curl, CURLINFO_EFFECTIVE_URL),
            'length'   => curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD),
        ];

        if (isset($this->config['download'])) {
            fclose($download);
        }

        // Reset Curl
        curl_close($curl);

        $this->config = [
            'headers'   => [],
            'options'   => [],
            'download'  => null,
            'useragent' => null
        ];

        // return curl response
        return new EasyCurlResponse($response);
    }

    public function setOption(int $key, mixed $value): ICurlDriver
    {
        $this->config['options'][$key] = $value;
        return $this;
    }

    public function setOptions(array $options): ICurlDriver
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    public function setUseragent(string $useragent): ICurlDriver
    {
        $this->config['useragent'] = $useragent;
        return $this;
    }

    public function setHeader(string $key, string $value): ICurlDriver
    {
        $this->config['headers'][] = $key . ':' . $value;
        return $this;
    }

    public function setHeaders(array $headers): ICurlDriver
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    public function setCookieFile(string $filepath): ICurlDriver
    {
        return $this->setOptions([
            CURLOPT_COOKIEJAR   => $filepath,
            CURLOPT_COOKIEFILE  => $filepath
        ]);
    }

    public function setDownloadFile(string $filepath, bool $override = false): ICurlDriver
    {
        if (file_exists($filepath)) {
            if ($override === true) {
                unlink($filepath);
            } else {
                throw new EasyCurlException('Download File: ' . $filepath . ' already exists.');
            }
        }

        $this->config['download'] = $filepath;

        return $this;
    }

    public function setPostFields(mixed $fields): ICurlDriver
    {
        return $this->setOptions([
            CURLOPT_POST        => 1,
            CURLOPT_POSTFIELDS  => is_array($fields) ? http_build_query($fields) : $fields
        ]);
    }

    public function setProxy(array $proxy): ICurlDriver
    {
        if (empty($proxy) || !isset($proxy['ip']) || !isset($proxy['port'])) {
            throw new EasyCurlProxyException("Invalid Proxy Credentials");
        }

        $this->setOptions([
            CURLOPT_PROXY => $proxy['ip'],
            CURLOPT_PROXYPORT => $proxy['port']
        ]);

        if (isset($proxy['tunnel'])) {
            $this->setOption(CURLOPT_HTTPPROXYTUNNEL, $proxy['tunnel']);
        }

        if (isset($proxy['socket'])) {
            $this->setOption(CURLOPT_PROXYTYPE, $proxy['socket']);
        }

        // setup proxy credentials
        if (isset($proxy['auth']) && !empty($proxy['auth'])) {
            $this->setOptions([
                CURLOPT_HTTPAUTH => isset($proxy['auth_type'])
                    ? $proxy['auth_type']
                    : CURLAUTH_BASIC,
                isset($proxy['auth_key'])
                    ? $proxy['auth_key']
                    : CURLOPT_PROXYUSERPWD => $proxy['auth']
            ]);
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (in_array(strtolower($name), ['get', 'post', 'put', 'patch', 'delete'])) {
            $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($name));
            return call_user_func([$this, 'send'], ...$arguments);
        }

        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], ...$arguments);
        }

        throw new EasyCurlException('Undefined Method: ' . $name);
    }
}
