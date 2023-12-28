<?php

namespace VulcanPhp\SimpleDb\Model;

trait Loader
{
    public function inputValidate(...$args): bool
    {
        return $this->input(...$args)->validate();
    }

    public function input(...$args): self
    {
        return $this->load($this->inputParse(...$args));
    }

    public function load($data): self
    {
		foreach ((array) $data as $key => $value) {
            if ((in_array($key, $this->fillable()) || property_exists($this, $key) || $key == $this->primaryKey())
                && isset($value) && (!empty($value) || $value == 0)
            ) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function empty(): self
    {
        foreach ($this->fillable() as $key) {
            unset($this->{$key});
        }

        return $this;
    }

    public function push(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function pushValidate(...$args): self
    {
        return $this->push(...$args)->validate();
    }

    protected function inputParse(...$filter): array
    {
        $output = $_GET + $_POST;
        $filter = array_merge(
            ...array_map(
                fn ($value) => is_array($value) ? $value : [$value],
                $filter
            )
        );

        // Append any PHP-input json
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contents = file_get_contents('php://input');
            if (strpos(trim($contents), '{') === 0) {
                $post = json_decode($contents, true);
                if ($post !== false) {
                    $output += $post;
                }
            }
        }

        $output = (count($filter) > 0) ? array_intersect_key($output, array_flip($filter)) : $output;

        foreach ($filter as $filterKey) {
            if (array_key_exists($filterKey, $output) === false) {
                $output[$filterKey] = null;
            }
        }

        return $output;
    }
}
