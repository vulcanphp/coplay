<?php

namespace VulcanPhp\Core\Database\Schema\Builder;

class SQLiteSchema
{
    private $table, $primaryKey, $schema, $foreigns;
    private $fnmodifires = [
        'current_timestamp',
        'current_timestamp on update current_timestamp',
    ];

    public function __construct(string $table, string $primaryKey, array $schema,  ?array $foreigns = null)
    {
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
        $this->schema     = $schema;
        $this->foreigns   = $foreigns;
    }

    private function beforeSchema(): string
    {
        return "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
    }

    private function generateColumns(): string
    {
        return implode(',', array_map([$this, 'generateSingleColumn'], $this->schema));
    }

    public function generateSingleColumn($config)
    {
        $modifires = $config['modifires'];

        if ($config['field'] === 'id' && isset($modifires['increment']) && strtolower($modifires['increment']) === 'auto_increment') {
            return '`id` INTEGER PRIMARY KEY';
        }

        $entity = ($config['nullable'] === false && $config['default'] !== null && !in_array(strtolower($config['default']), $this->fnmodifires)) ? "'" : "";
        #-- @set Column Name
        $column = "`{$config['field']}`";
        #-- @set Column Type
        $column .= in_array($config['type'], ['enum']) ? " varchar" : " {$config['type']}";
        #-- @set column length
        $column .= ($config['length'] !== null) ? (in_array($config['type'], ['enum']) ? '(60)' : "({$config['length']})") : "";
        #-- @set Column Unique Modifiew
        $column .= (isset($modifires['unique']) && is_string($modifires['unique'])) ? " " . $modifires['unique'] : "";
        #-- @set Column Nullable Modifire
        $column .= ($config['nullable'] === false && $config['default'] === null) ? " NOT NULL" : "";
        #-- @set Column default Modifier
        $column .= ($config['nullable'] === true && $config['default'] === null) ? " DEFAULT NULL" : "";
        $column .= ($config['nullable'] === false && $config['default'] !== null) ? " DEFAULT {$entity}" . (in_array(strtolower($config['default']), $this->fnmodifires) ? "(Datetime('now','localtime'))" : $config['default']) . "{$entity}" : "";

        return $column;
    }

    private function generateForeignKeys(): string
    {
        return implode(',', array_map([$this, 'generateForeignSchema'], $this->foreigns));
    }

    public function generateForeignSchema($foreign): string
    {
        $delete_action = (isset($foreign['ondelete']) && !empty($foreign['ondelete'])) ? 'ON DELETE ' . $foreign['ondelete'] : '';
        $update_action = (isset($foreign['onupdate']) && !empty($foreign['onupdate'])) ? 'ON UPDATE ' . $foreign['onupdate'] : '';

        return sprintf("FOREIGN KEY (`%s`) REFERENCES `%s`(`%s`) %s", $foreign['key'], $foreign['table'], $foreign['primary_key'], $update_action, $delete_action);
    }

    public function build(): string
    {
        $schema = $this->beforeSchema();
        $schema .= $this->generateColumns();

        if (!empty($this->foreigns)) {
            $schema .= ',' . $this->generateForeignKeys();
        }

        $schema .= ')';

        return $schema;
    }
}
