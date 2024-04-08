<?php

namespace VulcanPhp\InputMaster\Input;

use VulcanPhp\InputMaster\Request;

class InputHandler
{
    protected $get = [], $post = [], $file = [], $originalPost = [], $originalGet = [], $originalFile = [], $isPostBack = false;

    public function __construct(bool $postBack)
    {
        $this->isPostBack = $postBack;
        /* Parse get requests */
        if (count($_GET) !== 0) {
            $this->originalGet = $_GET;
            $this->get         = $this->parseInputItem($this->originalGet);
        }

        /* Parse post requests */
        $this->originalPost = $_POST;
        if ($this->isPostBack) {
            $contents = file_get_contents('php://input');
            // Append any PHP-input json
            if (strpos(trim($contents), '{') === 0) {
                $post = json_decode($contents, true);

                if ($post !== false) {
                    $this->originalPost += $post;
                }
            }
        }

        if (count($this->originalPost) !== 0) {
            $this->post = $this->parseInputItem($this->originalPost);
        }

        /* Parse get requests */
        if (count($_FILES) !== 0) {
            $this->originalFile = $_FILES;
            $this->file         = $this->parseFiles($this->originalFile);
        }
    }

    protected function parseFiles(array $files,  ?string $parentKey = null): array
    {
        $list = [];
        foreach ($files as $key => $value) {
            // Parse multi dept file array
            if (isset($value['name']) === false && is_array($value) === true) {
                $list[$key] = $this->parseFiles($value, $key);
                continue;
            }

            // Handle array input
            if (is_array($value['name']) === false) {
                $values['index'] = $parentKey ?? $key;
                try {
                    $list[$key] = InputFile::createFromArray($values + $value);
                } catch (\Exception $e) {
                }
                continue;
            }

            $keys  = [$key];
            $files = $this->rearrangeFile($value['name'], $keys, $value);
            if (isset($list[$key]) === true) {
                $list[$key][] = $files;
            } else {
                $list[$key] = $files;
            }
        }
        return $list;
    }

    protected function rearrangeFile(array $values, array &$index,  ?array $original): array
    {
        $output = [];
        $originalIndex = $index[0];
        array_shift($index);

        foreach ($values as $key => $value) {
            if (is_array($original['name'][$key]) === false) {
                $file = InputFile::createFromArray([
                    'index'    => (empty($key) === true && empty($originalIndex) === false) ? $originalIndex : $key,
                    'name'     => $original['name'][$key],
                    'error'    => $original['error'][$key],
                    'tmp_name' => $original['tmp_name'][$key],
                    'type'     => $original['type'][$key],
                    'size'     => $original['size'][$key],
                ]);

                if (isset($output[$key]) === true) {
                    $output[$key][] = $file;
                    continue;
                }

                $output[$key] = $file;
                continue;
            }

            $index[] = $key;
            $files = $this->rearrangeFile($value, $index, $original);
            if (isset($output[$key]) === true) {
                $output[$key][] = $files;
            } else {
                $output[$key] = $files;
            }
        }
        return $output;
    }

    protected function parseInputItem(array $array): array
    {
        $list = [];
        foreach ($array as $key => $value) {
            // Handle array input
            if (is_array($value) === true) {
                $value = $this->parseInputItem($value);
            }
            $list[$key] = new InputItem($key, $value);
        }
        return $list;
    }

    public function find(string $index, ...$methods)
    {
        $element = null;
        if (count($methods) > 0) {
            $methods = is_array(...$methods) ? array_values(...$methods) : $methods;
        }

        if (count($methods) === 0 || in_array(Request::REQUEST_TYPE_GET, $methods, true) === true) {
            $element = $this->getGet($index);
        }

        if (
            $element === null && (count($methods) === 0 ||
                (count($methods) !== 0 && in_array(Request::REQUEST_TYPE_POST, $methods, true) === true))
        ) {
            $element = $this->getPost($index);
        }

        if (
            ($element === null && count($methods) === 0) ||
            (count($methods) !== 0 && in_array('file', $methods, true) === true)
        ) {
            $element = $this->getFile($index);
        }
        return $element;
    }

    protected function getValueFromArray(array $array): array
    {
        $output = [];
        /* @var $item InputItem */
        foreach ($array as $key => $item) {
            if ($item instanceof IInputItem) {
                $item = $item->getValue();
            }
            $output[$key] = is_array($item) ? $this->getValueFromArray($item) : $item;
        }
        return $output;
    }

    public function value(string $index, $defaultValue = null, ...$methods)
    {
        $input = $this->find($index, ...$methods);
        if ($input instanceof IInputItem) {
            $input = $input->getValue();
        }
        /* Handle collection */
        if (is_array($input) === true) {
            $output = $this->getValueFromArray($input);

            return (count($output) === 0) ? $defaultValue : $output;
        }
        return ($input === null || (is_string($input) && trim($input) === '')) ? $defaultValue : $input;
    }

    public function exists($index, ...$methods): bool
    {
        // Check array
        if (is_array($index) === true) {
            foreach ($index as $key) {
                if ($this->value($key, null, ...$methods) === null) {
                    return false;
                }
            }
            return true;
        }
        return $this->value($index, null, ...$methods) !== null;
    }

    public function getPost(string $index = '*')
    {
        if (in_array($index, ['*', 'all'])) {
            return $this->post;
        }
        return $this->post[$index] ?? null;
    }

    public function setPost(string $key, InputItem $item): void
    {
        $this->post[$key] = $item;
    }

    public function getOriginalPost(): array
    {
        return $this->originalPost;
    }

    public function setOriginalPost(array $post): self
    {
        $this->originalPost = $post;
        return $this;
    }

    public function getGet(string $index = '*')
    {
        if (in_array($index, ['*', 'all'])) {
            return $this->get;
        }
        return $this->get[$index] ?? null;
    }

    public function setGet(string $key, InputItem $item): void
    {
        $this->get[$key] = $item;
    }

    public function getOriginalGet(): array
    {
        return $this->originalGet;
    }

    public function setOriginalGet(array $params): self
    {
        $this->originalGet = $params;

        return $this;
    }

    public function getFile(string $index = '*')
    {
        if (in_array($index, ['*', 'all'])) {
            return $this->file;
        }
        return $this->file[$index] ?? null;
    }

    public function hasFile(string $index): bool
    {
        return isset($this->file[$index]) && $this->file[$index]->size > 0;
    }

    public function setFile(string $key, InputFile $item): void
    {
        $this->file[$key] = $item;
    }

    public function getOriginalFile(): array
    {
        return $this->originalFile;
    }

    public function setOriginalFile(array $file): self
    {
        $this->originalFile = $file;

        return $this;
    }

    public function all(array $filter = []): array
    {
        $output = $this->originalGet + $this->originalPost + $this->originalFile;
        $output = (count($filter) > 0) ? array_intersect_key($output, array_flip($filter)) : $output;

        foreach ($filter as $filterKey) {
            if (array_key_exists($filterKey, $output) === false) {
                $output[$filterKey] = null;
            }
        }

        return $output;
    }
}
