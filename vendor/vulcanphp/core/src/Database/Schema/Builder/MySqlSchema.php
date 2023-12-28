<?php

namespace VulcanPhp\Core\Database\Schema\Builder;

class MySqlSchema
{
    private $table, $primaryKey, $schema, $foreigns, $keys;
    private $fnmodifires = [
        'current_timestamp',
        'current_timestamp on update current_timestamp',
    ];

    public function __construct(string $table, string $primaryKey, array $schema,  ?array $foreigns = null,  ?array $keys = null)
    {
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
        $this->schema     = $schema;
        $this->foreigns   = $foreigns;
        $this->keys       = $keys;
    }

    private function beforeSchema(): string
    {
        return "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
    }

    private function generateColumns(): string
    {
        return implode(',', array_map([$this, 'generateSingleColumn'], $this->schema));
    }

    public function generateSingleColumn($column)
    {
        $modifires = $column['modifires'];
        $entity    = ($column['nullable'] === false && $column['default'] !== null && !in_array(strtolower($column['default']), $this->fnmodifires)) ? "'" : "";

        #-- @set Column Name
        $__column = "`{$column['field']}`";
        #-- @set Column Type
        $__column .= " {$column['type']}";
        #-- @set column length
        $__column .= ($column['length'] !== null) ? "({$column['length']})" : "";
        #-- @set Column Unique Modifiew
        $__column .= (isset($modifires['unique']) && is_string($modifires['unique'])) ? " " . $modifires['unique'] : "";
        #-- @set Column Signed Modifiew
        $__column .= (isset($modifires['signed']) && is_string($modifires['signed'])) ? " " . $modifires['signed'] : "";
        #-- @set Column Nullable Modifire
        $__column .= ($column['nullable'] === false && $column['default'] === null) ? " NOT NULL" : "";
        #-- @set Column Autoincrement Modifiew
        $__column .= (isset($modifires['increment']) && is_string($modifires['increment'])) ? " " . $modifires['increment'] : "";
        #-- @set Column default Modifier
        $__column .= ($column['nullable'] === true && $column['default'] === null) ? " DEFAULT NULL" : "";
        $__column .= ($column['nullable'] === false && $column['default'] !== null) ? " DEFAULT {$entity}" . $column['default'] . "{$entity}" : "";

        return $__column;
    }

    private function generateForeignKeys(): string
    {
        return implode(',', array_map([$this, 'generateForeignSchema'], $this->foreigns));
    }

    private function generateKeys(): string
    {
        return implode(',', array_map([$this, 'generateKeySchema'], $this->keys));
    }

    public function generateForeignSchema($foreign): string
    {
        $delete_action  = (isset($foreign['ondelete']) && !empty($foreign['ondelete'])) ? 'ON DELETE ' . $foreign['ondelete'] : '';
        $update_action  = (isset($foreign['onupdate']) && !empty($foreign['onupdate'])) ? 'ON UPDATE ' . $foreign['onupdate'] : '';
        $constrainedkey = "`{$foreign['key']}_f" . rand(1, 999) . "`";

        return "KEY {$constrainedkey} (`{$foreign['key']}`),
                CONSTRAINT {$constrainedkey} FOREIGN KEY (`{$foreign['key']}`) REFERENCES `{$foreign['table']}` (`{$foreign['primary_key']}`) {$delete_action} {$update_action}";
    }

    public function generateKeySchema($key): string
    {
        return (isset($key['prefix']) ? $key['prefix'] . ' ' : '') . "KEY `{$key['name']}` (`{$key['field']}`)";
    }

    private function schemaPrimaryKey(): string
    {
        return ", PRIMARY KEY (`{$this->primaryKey}`)";
    }

    private function afterSchema(): string
    {
        return sprintf(")ENGINE=InnoDB DEFAULT CHARSET=%s COLLATE=%s;", config('database.charset'), config('database.collate'));
    }

    public function build(): string
    {
        $schema = $this->beforeSchema() . $this->generateColumns() . $this->schemaPrimaryKey();

        if ($this->keys !== null && !empty($this->keys)) {
            $schema .= ',' . $this->generateKeys();
        }

        if ($this->foreigns !== null && !empty($this->foreigns)) {
            $schema .= ',' . $this->generateForeignKeys();
        }

        $schema .= $this->afterSchema();

        return $schema;
    }
}
