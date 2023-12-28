<?php

namespace VulcanPhp\SweetView\Engine\Html;

use Exception;
use VulcanPhp\SweetView\Interfaces\IEngine;

class Html extends BaseHtmlEngine implements IEngine
{
    use HtmlMeta;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
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

    public function render(array $params = []): string
    {
        $content = $this->getContent($this->viewLocation($this->getTemplate()), $params);

        if ($this->getLayout() !== null) {
            $content = str_ireplace('{{content}}', $content, $this->getContent($this->viewLocation($this->getLayout())));
        }

        return $this->isMinified() ? preg_replace(['/^ {2,}/m', '/^\t{2,}/m', '~[\r\n]+~'], '',  $content) : $content;
    }

    public function getContent(string $path, array $params = []): string
    {
        if (!file_exists($path))
            throw new Exception('view file: ' . $path . ' does not exists');

        $params  = array_merge($this->params, $params);

        extract($params);
        unset($params);
        ob_start();

        include $path;

        return ob_get_clean();
    }
}
