<?php

namespace VulcanPhp\SimpleDb;

use VulcanPhp\SimpleDb\Query;
use VulcanPhp\SimpleDb\Model\BaseModel;
use VulcanPhp\SimpleDb\Model\Object\ObjectFetch;
use VulcanPhp\SimpleDb\Model\Object\ObjectRemove;
use VulcanPhp\SimpleDb\Model\Object\ObjectSave;

abstract class Model extends BaseModel
{
    use ObjectSave, ObjectFetch, ObjectRemove;

    abstract public static function tableName(): string;
    abstract public static function primaryKey(): string;
    abstract public static function fillable(): array;

    public static function query(): Query
    {
        return Query::table(static::tableName(), static::primaryKey());
    }

    public function __call($name, $arguments)
    {
        if (
            $this->query()
            ->getHookHandler()
            ->hasFallback('model', $name)
        ) {
            return $this->query()
                ->getHookHandler()
                ->getFallback('model', $name, $this, ...$arguments);
        }

        return call_user_func(
            [
                $this->query()
                    ->fetch(static::class),
                $name
            ],
            ...$arguments
        );
    }

    public static function __callStatic($name, $arguments)
    {
        if (
            self::query()
            ->getHookHandler()
            ->hasFallback('model_static', $name)
        ) {
            return self::query()
                ->getHookHandler()
                ->getFallback('model_static', $name, static::class, ...$arguments);
        }

        return call_user_func(
            [
                self::query()
                    ->fetch(static::class),
                $name
            ],
            ...$arguments
        );
    }

    protected function getFillableData(): array
    {
        $data = [];

        foreach ($this->fillable() as $attr) {
            if (isset($this->{$attr})) {
                $data[$attr] = $this->{$attr};
            }
        }

        return $this->query()
            ->getHookHandler()
            ->applyFilters('fillable_data', $data);
    }

    protected function modelVar(string $key): ?string
    {
        return $this->{$key} ?? null;
    }

    protected function setModelVar(string $key, $value): self
    {
        $this->{$key} = $value;

        return $this;
    }
}
