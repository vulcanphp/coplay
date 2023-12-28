<?php

namespace VulcanPhp\Core\Database\Schema;

class Schema
{
    public static function create(...$args): Blueprint
    {
        return new Blueprint(...$args);
    }

    public static function drop(string $tablename): string
    {
        return "DROP TABLE IF EXISTS `{$tablename}`";
    }

    public static function clean(string $tablename): string
    {
        return "TRUNCATE TABLE `{$tablename}`";
    }

    public static function delete(string $tablename,  ?string $where = null): string
    {
        return "DELETE FROM `{$tablename}` " . ($where !== null ? sprintf("WHERE %s", $where) : '');
    }
}
