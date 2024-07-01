<?php

namespace VulcanPhp\SweetView\Engine\Html;

use Exception;
use VulcanPhp\SweetView\Interfaces\IEngine;

class Html implements IEngine
{
    use HtmlMeta;

    protected $resource_dir, $extension, $minified, $params = [], $blocks = [];
    protected ?string $template, $layout;

    public function __construct($template = null, $layout = null)
    {
        $this->template = $template;
        $this->layout = $layout;
    }

    public static function load(...$args): Html
    {
        return new Html(...$args);
    }

    public function clean(): self
    {
        if (isset(ob_get_status()['buffer_size']) && ob_get_status()['buffer_size'] > 0) {
            ob_end_clean();
            flush();
        }

        return $this;
    }

    public function template(string $template): self
    {
        if (stripos($template, sweet_view_root_dir()) !== false) {
            $location = $this->directoryParse(str_replace('.', '/', $this->removeExtension($template)));
            $this->resource_dir = substr($location, 0, strrpos($location, DIRECTORY_SEPARATOR) + 1);
            $this->template = substr($location, strrpos($location, DIRECTORY_SEPARATOR) + 1);
        } else {
            $this->template = $template;
        }

        return $this;
    }

    public function layout(?string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function getBlock(string $key, $default = '')
    {
        return $this->blocks[$key] ?? $default;
    }

    public function hasBlock(string $key): bool
    {
        return isset($this->blocks[$key]);
    }

    public function block(string $key, $value): self
    {
        $this->blocks[$key] = $value;
        return $this;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function getLayout(): ?string
    {
        return $this->layout ?? null;
    }

    public function getTemplate(): ?string
    {
        return $this->template ?? null;
    }

    public function resourceDir(string $path): self
    {
        $this->resource_dir = $path;
        return $this;
    }

    public function with(array $params = []): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function minified($minified = true): self
    {
        $this->minified = $minified;
        return $this;
    }

    public function isMinified(): bool
    {
        return boolval($this->minified ?? false);
    }

    public function extension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension ?? '.php';
    }

    public function include(...$args)
    {
        return $this->selfLoad('include', ...$args);
    }

    public function require(...$args)
    {
        return $this->selfLoad('require', ...$args);
    }

    public function includeOnce(...$args)
    {
        return $this->selfLoad('include_once', ...$args);
    }

    public function requireOnce(...$args)
    {
        return $this->selfLoad('require_once', ...$args);
    }

    public function component(string $component, array $params = []): self
    {
        extract($params);
        unset($params);

        include $this->viewLocation($component);
        return $this;
    }

    protected function selfLoad(string $type, string $file, array $params = []): self
    {
        $params = array_merge($this->params, $params);
        extract($params);
        unset($params);

        switch ($type) {
            case 'include_once':
                include_once $this->viewLocation($file);
                break;
            case 'require_once':
                require_once $this->viewLocation($file);
                break;
            case 'require':
                require $this->viewLocation($file);
                break;
            default:
                include $this->viewLocation($file);
        }

        return $this;
    }

    protected function viewLocation(string $file): string
    {
        return $this->directoryParse(sprintf('%s/%s', stripos($file, sweet_view_root_dir()) === false ? ($this->resource_dir ?? '') : sweet_view_root_dir(), str_replace(['.'], '/', str_ireplace(sweet_view_root_dir(), '', $this->removeExtension($file))))) . $this->getExtension();
    }

    protected function directoryParse(string $path): string
    {
        return rtrim(str_replace(['///', '//', '/', (DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR)], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }

    protected function removeExtension(string $path): string
    {
        return str_ireplace($this->getExtension(), '', $path);
    }

    public function templateLocation(): string
    {
        return $this->viewLocation($this->getTemplate());
    }

    public function layoutLocation(): string
    {
        return $this->viewLocation($this->getLayout());
    }

    public function render(array $params = []): string
    {
        $content = $this->getContent($this->templateLocation(), $params);

        if ($this->getLayout() !== null) {
            $content = str_ireplace('{{content}}', $content, $this->getContent($this->layoutLocation()));
        }

        return $this->isMinified() ? minify_text($content) : $content;
    }

    public function getContent(string $path, array $params = []): string
    {
        if (!file_exists($path)) {
            throw new Exception('view file: ' . $path . ' does not exists');
        }

        $params  = array_merge($this->params, $params);

        extract($params);
        unset($params);
        ob_start();

        include $path;

        return ob_get_clean();
    }
}
