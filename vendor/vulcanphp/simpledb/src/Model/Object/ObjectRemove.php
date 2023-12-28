<?php

namespace VulcanPhp\SimpleDb\Model\Object;

trait ObjectRemove
{
    public function remove(): bool
    {
        return static::query()
            ->delete(
                [
                    static::primaryKey() => $this->modelVar(static::primaryKey())
                ]
            );
    }

    public static function erase($condition): bool
    {
        return static::query()
            ->delete($condition);
    }

    public static function clearData(): bool
    {
        return static::query()
            ->delete([1 => 1]);
    }
}
