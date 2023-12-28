<?php

namespace VulcanPhp\SimpleDb\Model\Object;

trait ObjectSave
{
    public function save(bool $force = false)
    {
        $data = $this->getFillableData();
        $result = false;

        if ($this->modelVar(static::primaryKey()) !== null) {

            $result = static::query()
                ->update(
                    $data,
                    [static::primaryKey() => $this->modelVar(static::primaryKey())]
                );

            if (!$result && $force) {
                $result = static::query()
                    ->insert($data);
            }
        } else {
            $result = static::query()
                ->insert($data);
        }

        if (!is_bool($result) && is_int($result)) {
            $this->setModelVar(static::primaryKey(), $result);
        }

        return $result;
    }

    public static function put(...$args): bool
    {
        return static::query()
            ->update(...$args);
    }

    public static function create(...$args): int
    {
        return static::query()
            ->insert(...$args);
    }
}
