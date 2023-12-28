<?php

namespace VulcanPhp\SimpleDb\Model\Object;

use VulcanPhp\SimpleDb\Exceptions\ObjectException;
use VulcanPhp\SimpleDb\Query;

trait ObjectFetch
{
    public static function select($fields = '*')
    {
        return static::query()
            ->select($fields)
            ->fetch(static::class);
    }

    public static function where(...$args)
    {
        return static::select()
            ->where(...$args);
    }

    public static function all($fields = '*', $where = null)
    {
        return static::select($fields)
            ->where($where)->get();
    }

    public static function find($condition)
    {
        if (is_int($condition) || (is_string($condition) && intval($condition) == $condition)) {
            $condition = [static::primaryKey() => $condition];
        }

        return static::select()
            ->where($condition)
            ->limit(1)
            ->first();
    }

    public static function findOrFail(...$args)
    {
        $data = static::find(...$args);

        if ($data === false) {
            throw new ObjectException('Record does not exist.');
        }

        return $data;
    }

    public static function total($where = null): int
    {
        return static::query()
            ->select('COUNT(1)')
            ->where($where)
            ->fetch(\PDO::FETCH_COLUMN)
            ->first();
    }

    public static function paginate(int $limit = 10)
    {
        return static::select()
            ->paginate($limit);
    }

    public function hasOne(string $class, ?string $foreignKey = null)
    {
        return $this->hasORM($class, $foreignKey)
            ->limit(1)
            ->first();
    }

    public function hasMany(string $class, ?string $foreign = null)
    {
        return $this->hasORM($class, $foreign)
            ->result();
    }

    public function belongsTo(string $class, ?string $foreign = null)
    {
        return $this->belongsORM($class, $foreign)
            ->limit(1)
            ->first();
    }

    public function belongsToMany(string $class, ?string $foreign = null)
    {
        return $this->belongsORM($class, $foreign)
            ->result();
    }

    protected function hasORM(string $model, ?string $foreign = null): Query
    {
        if (!class_exists($model)) {
            throw new ObjectException(
                sprintf("Model (%s) does not exist.", $model)
            );
        }

        if ($this->modelVar(static::primaryKey()) === null) {
            throw new ObjectException(
                sprintf("Model (%s) does initialized. ", static::tableName())
            );
        }

        if ($foreign === null) {
            $foreign = sprintf(
                '%s_%s',
                // singularize table name
                substr(static::tableName(), strlen(static::tableName()) - 1) == 's'
                    ? substr(static::tableName(), 0, strlen(static::tableName()) - 1)
                    : static::tableName(),
                static::primaryKey()
            );
        }

        if (!in_array($foreign, $model::fillable())) {
            throw new ObjectException(
                sprintf("Foreign Key (%s) does exists on: %s", $foreign, $model::tableName())
            );
        }

        return Query::table($model::tableName(), $model::primaryKey())
            ->select('*')
            ->where([$foreign => $this->modelVar(static::primaryKey())])
            ->fetch($model);
    }

    protected function belongsORM(string $model, ?string $foreign = null): Query
    {
        if (!class_exists($model)) {
            throw new ObjectException(
                sprintf("Model (%s) does not exist.", $model)
            );
        }

        if ($this->modelVar(static::primaryKey()) === null) {
            throw new ObjectException(
                sprintf("Model (%s) does initialized. ", static::tableName())
            );
        }

        if ($foreign === null) {
            $foreign = sprintf(
                '%s_%s',
                // singularize table name
                substr($model::tableName(), strlen($model::tableName()) - 1) == 's'
                    ? substr($model::tableName(), 0, strlen($model::tableName()) - 1)
                    : $model::tableName(),
                $model::primaryKey()
            );
        }

        if (!in_array($foreign, static::fillable())) {
            throw new ObjectException(
                sprintf("Foreign Key (%s) does exists on: %s", $foreign, static::tableName())
            );
        }

        return Query::table($model::tableName(), $model::primaryKey())
            ->select('*')
            ->where([$model::primaryKey() => $this->modelVar($foreign)])
            ->fetch($model);
    }
}
