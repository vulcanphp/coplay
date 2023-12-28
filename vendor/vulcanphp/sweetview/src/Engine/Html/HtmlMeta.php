<?php

namespace VulcanPhp\SweetView\Engine\Html;

trait HtmlMeta
{
    protected array $meta = [];

    public function meta(string $key, $default = '')
    {
        return $this->meta[$key] ?? $default;
    }

    public function setupMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function setMeta(string $key, $value): self
    {
        if (!isset($this->meta[$key])) {
            $this->meta[$key] = $value;
        }
        return $this;
    }

    public function schema($json): self
    {
        $this->meta['schema'][] = is_array($json) ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $json;
        return $this;
    }

    public function siteMeta(): string
    {
        ob_start();

        echo '<title>', $this->meta('title'), '</title>', "\n\t",
        $this->generateMeta(), "\n\t",
        $this->generateSchema(), "\n";

        return ob_get_clean();
    }

    protected function generateMeta(): string
    {
        return join("\n\t", $this->replace_meta([
            '<meta charset="[charset]">',
            '<meta name="viewport" content="[viewport]">',
            '<meta name="robots" content="[robots]"/>',
            '<meta name="description" content="[description]"/>',
            '<meta name="og:url" content="[url]"/>',
            '<meta name="og:locale" content="[language]"/>',
            '<meta name="og:title" content="[title]"/>',
            '<meta name="og:description" content="[description]"/>',
            '<meta name="og:type" content="website"/>',
            '<meta name="og:site_name" content="[sitename]"/>',
            '<meta name="og:image" content="[image]"/>',
            '<meta name="twitter:url" content="[url]"/>',
            '<meta name="twitter:title" content="[title]"/>',
            '<meta name="twitter:description" content="[description]"/>',
            '<meta name="twitter:card" content="summary"/>',
            '<meta name="twitter:image" content="[image]"/>',
            '<meta name="twitter:site" content="@[sitename:lower]"/>',
        ]));
    }

    protected function replace_meta(array $values): array
    {
        $regx = [
            '[charset]' => $this->meta('charset', 'utf-8'),
            '[viewport]' => $this->meta('viewport', 'width=device-width, initial-scale=1.0'),
            '[robots]' => $this->meta('robots', 'all'),
            '[language]' => $this->meta('language', 'en'),
            '[title]' => $this->meta('title'),
            '[description]' => $this->meta('description'),
            '[image]' => $this->meta('image', ''),
            '[url]' => $this->meta('url'),
            '[sitename]' => $this->meta('sitename'),
            '[sitename:lower]' => strtolower($this->meta('sitename')),
        ];

        foreach ($values as $key => $value) {
            $values[$key] = str_ireplace(array_keys($regx), array_values($regx), $value);
        }

        return $values;
    }

    protected function generateSchema(): string
    {
        if (isset($this->meta['schema']) && !empty($this->meta['schema'])) {
            return '<script type="application/ld+json">' . join("</script>\n\t<script type='application/ld+json'>", $this->meta['schema']) . '</script>';
        }

        return '';
    }
}
