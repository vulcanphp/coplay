<?php

namespace VulcanPhp\Core\Database\Schema;

use Exception;
use VulcanPhp\Core\Database\Schema\Builder\MySqlSchema;
use VulcanPhp\Core\Database\Schema\Builder\SQLiteSchema;

class Blueprint
{
    protected  ?string $table       = null;
    protected string $primaryKey    = 'id';
    protected array $schema         = array();
    protected  ?array $foreigns     = null;
    protected  ?array $keys         = null;

    const TYPE_INT          = 'int';
    const TYPE_TINYINT      = 'tinyint';
    const TYPE_BIGINT       = 'bigint';
    const TYPE_STRING       = 'varchar';
    const TYPE_CHAR         = 'char';
    const TYPE_ENUM         = 'enum';
    const TYPE_TEXT         = 'text';
    const TYPE_MEDIUMTEXT   = 'mediumtext';
    const TYPE_LONGTEXT     = 'longtext';
    const TYPE_FLOAT        = 'float';
    const TYPE_DECIMAL      = 'decimal';
    const TYPE_BOOLEAN      = 'boolean';
    const TYPE_DATE         = 'date';
    const TYPE_DATETIME     = 'datetime';
    const TYPE_TIMESTAMP    = 'timestamp';
    const TYPE_POINT        = 'point';
    const MODY_SIGNED       = 'signed';
    const MODY_INCREMENT    = 'increment';
    const MODY_UNIQUE       = 'unique';

    public function __construct(string $tablename)
    {
        $this->table = $tablename;
    }

    public function id(): self
    {
        $this->integer('id', 11, null, [self::MODY_SIGNED => 'unsigned', self::MODY_INCREMENT => 'AUTO_INCREMENT']);
        return $this;
    }

    private function generateField(string $field, string $type, $length = null,  ?string $default = null, array $modifires = array()): array
    {
        return array(
            'field'     => $field,
            'type'      => $type,
            'length'    => $length,
            'nullable'  => false,
            'default'   => $default,
            'modifires' => $modifires,
        );
    }

    public function integer(string $field, int $length = 11,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_INT, $length, $default, $modifires);
        return $this;
    }

    public function foreignId(string $field, int $length = 11): self
    {
        $this->integer($field, $length, null, [self::MODY_SIGNED => 'unsigned']);
        $this->foreigns[$field] = array(
            'key' => $field,
        );
        return $this;
    }

    public function tinyInteger(string $field, int $length = 1,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_TINYINT, $length, $default, $modifires);
        return $this;
    }

    public function bigInteger(string $field, int $length = 20,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_BIGINT, $length, $default, $modifires);
        return $this;
    }

    public function string(string $field, int $length = 255,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_STRING, $length, $default, $modifires);
        return $this;
    }

    public function char(string $field, int $length = 255,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_CHAR, $length, $default, $modifires);
        return $this;
    }

    public function point(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_POINT, null, null, $modifires);
        return $this;
    }

    public function enum(string $field, $length = null,  ?string $default = null, array $modifires = array()): self
    {
        if ($length === null) {
            $this->error('Column enum expect paramiter 2 must be array or string');
        }

        if (is_string($length)) {
            $length = array_filter(explode(',', $length));
        }

        $length = "'" . implode("','", $length) . "'";

        $this->schema[] = $this->generateField($field, self::TYPE_ENUM, $length, $default, $modifires);
        return $this;
    }

    public function text(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_TEXT, null, null, $modifires);
        return $this;
    }

    public function mediumText(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_MEDIUMTEXT, null, null, $modifires);
        return $this;
    }

    public function longText(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_LONGTEXT, null, null, $modifires);
        return $this;
    }

    public function float(string $field, string $length = '2,2',  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_FLOAT, $length, $default, $modifires);
        return $this;
    }

    public function decimal(string $field, string $length = '10,0',  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_DECIMAL, $length, $default, $modifires);
        return $this;
    }

    public function boolean(string $field, bool $length = false,  ?string $default = null, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_BOOLEAN, $length, $default, $modifires);
        return $this;
    }

    public function date(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_DATE, null, null, $modifires);
        return $this;
    }

    public function datetime(string $field, array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_DATETIME, null, null, $modifires);
        return $this;
    }

    public function timestamp(string $field,  ?string $default = 'CURRENT_TIMESTAMP', array $modifires = array()): self
    {
        $this->schema[] = $this->generateField($field, self::TYPE_TIMESTAMP, null, $default, $modifires);
        return $this;
    }

    public function onUpdate(string $modifire): self
    {
        $column = array_pop($this->schema);

        if (is_array($this->foreigns) && isset($this->foreigns[$column['field']])) {

            $foreign = array_pop($this->foreigns);
            if ($foreign['key'] === $column['field']) {
                $foreign['onupdate']             = $modifire;
                $this->foreigns[$foreign['key']] = $foreign;
            }
        } else {
            #-- @update modifier for columns
            $column['default'] = $column['default'] . " ON UPDATE " . $modifire;
        }

        $this->schema[] = $column;
        return $this;
    }

    public function onDelete(string $action): self
    {
        $foreign                         = array_pop($this->foreigns);
        $foreign['ondelete']             = $action;
        $this->foreigns[$foreign['key']] = $foreign;
        return $this;
    }

    public function unsigned(): self
    {
        $column                                 = array_pop($this->schema);
        $column['modifires'][self::MODY_SIGNED] = 'unsigned';
        $this->schema[]                         = $column;
        return $this;
    }

    public function unique(): self
    {
        $column                                 = array_pop($this->schema);
        $column['modifires'][self::MODY_UNIQUE] = 'unique';
        $this->schema[]                         = $column;
        return $this;
    }

    public function default(string $defaultvalue): self
    {
        $column            = array_pop($this->schema);
        $column['default'] = $defaultvalue;
        $this->schema[]    = $column;
        return $this;
    }

    public function nullable(): self
    {
        $column             = array_pop($this->schema);
        $column['nullable'] = true;
        $this->schema[]     = $column;
        return $this;
    }

    public function key(string $key, ?string $prefix = null): self
    {
        $column       = end($this->schema);
        $this->keys[] = [
            'field' => $column['field'],
            'name'  => $key,
            'prefix' => $prefix
        ];
        return $this;
    }

    public function autoIncrement(): self
    {
        $column                                    = array_pop($this->schema);
        $column['modifires'][self::MODY_INCREMENT] = 'AUTO_INCREMENT';
        $this->schema[]                            = $column;
        return $this;
    }

    public function constrained(string $table, string $primary_key = 'id'): self
    {
        $foreign                         = array_pop($this->foreigns);
        $foreign['table']                = $table;
        $foreign['primary_key']          = $primary_key;
        $this->foreigns[$foreign['key']] = $foreign;
        return $this;
    }

    public function primary(?string $field = null): self
    {
        if ($field === null) {
            $column           = end($this->schema);
            $this->primaryKey = $column['field'];
        } else {
            $this->primaryKey = $field;
        }
        return $this;
    }

    public function build(): string
    {
        $keys = [$this->table, $this->primaryKey, $this->schema, $this->foreigns, $this->keys];

        if (is_sqlite()) {
            $schema = new SQLiteSchema(...$keys);
        } elseif (is_mysql()) {
            $schema = new MySqlSchema(...$keys);
        } else {
            throw new Exception('Does not found schema generator for database driver');
        }

        return $schema->build();
    }

    private function error(string $message): void
    {
        echo 'Error - ' . $message . ' - [' . date('Y-m-d H:i:s') . ']' . PHP_EOL;
        exit;
    }
}
