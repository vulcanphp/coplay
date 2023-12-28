<?php

namespace VulcanPhp\SimpleDb\Query;

use PDOException;

trait QBCreate
{
    /**
     * Create, Replace and Update Single or Multiple Rows into SQL Database
     * @param array $data 
     * @param array $config ['ignore' = false, 'replace' => false, 'update' = []]
     * @return int 
     */
    public function insert(array $data, array $config = []): int
    {
        if (empty($data)) {
            return 0;
        }

        $this->getHookHandler()
            ->doAction('insert', $this->table);

        if (!(isset($data[0]) && is_array($data[0]))) {
            $data = [$data];
        }

        $data = $this->getHookHandler()
            ->applyFilters('insert_data', array_values($data));

        $fields     = $this->getFillable($data[0]);
        $statement  = $this->prepare(
            sprintf(
                // base sql schema
                "%s %s INTO `{$this->table}` (%s) VALUES %s %s;",

                // create or replace data into database 
                isset($config['replace']) && $config['replace'] === true
                    ? 'REPLACE'
                    : 'INSERT',

                // use ignore when failed
                isset($config['ignore']) && $config['ignore'] === true
                    ? (is_sqlite()
                        ? 'OR IGNORE'
                        : 'IGNORE'
                    ) : '',

                // join all the field using ,
                join(',', $fields),

                // use placeholder and bind value later
                $this->createPlaceholder($data),

                // bulk update data when conflict
                isset($config['update']) && !empty($config['update']) ?
                    (is_sqlite() ?

                        // bulk update sqlite driver method
                        ('ON CONFLICT(' . join(',', ($config['conflict'] ?? ['id'])) . ') DO UPDATE SET ' . (join(
                            ', ',
                            array_map(
                                fn ($key, $value) => sprintf(
                                    '%s = excluded.%s',
                                    $key,
                                    $value
                                ),
                                array_keys($config['update']),
                                array_values($config['update'])
                            )
                        )))

                        // else bulk update non-sqlite driver method
                        : ('ON DUPLICATE KEY UPDATE ' . (join(
                            ', ',
                            array_map(
                                fn ($key, $value) => sprintf(
                                    '%s = VALUES(%s)',
                                    $key,
                                    $value
                                ),
                                array_keys($config['update']),
                                array_values($config['update'])
                            )
                        )))

                    ) : ''
            )
        );

        foreach ($data as $serial => $row) {
            foreach ($fields as $column) {
                $statement->bindValue(
                    sprintf(
                        ':%s_%s',
                        $column,
                        $serial
                    ),
                    isset($row[$column]) && is_array($row[$column])
                        // bind from array value ex: ['text' => '2023']
                        ? ($row[$column]['text'] ?? null)
                        : ($row[$column] ?? null)
                );
            }
        }

        $statement = $this->getHookHandler()
            ->applyFilters('insert', $statement);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === 2006) {
                // reconnect database server if connection failed
                if ($this->getDatabase()->ping()) {
                    // insert data after connection database
                    return $this->insert($data, $config);
                }
            }
        }

        $last_id = $this->getDatabase()->last_id();

        $this->getHookHandler()
            ->doAction('inserted', $this->table, $last_id);

        if ($last_id > 0) {
            $this->changed('insert');
        }

        // return last inserted id
        return $last_id;
    }

    public function replace(array $data, array $config = []): int
    {
        $config['replace'] = true;

        return $this->insert($data, $config);
    }

    public function bulkUpdate(array $data, array $config = []): int
    {
        if (!(isset($data[0]) && is_array($data[0]))) {
            $data = [$data];
        }

        if (!isset($config['conflict'])) {
            $config['conflict'] = ['id'];
        }

        if (!isset($config['update'])) {
            $fields = array_filter(array_keys($data[0]), fn ($field) => !in_array($field, $config['conflict']));
            $config['update'] = array_map(fn ($field) => [$field => $field], $fields);
        }

        return $this->insert($data, $config);
    }

    protected function getFillable(array $row): array
    {
        return array_keys($row);
    }

    protected function createPlaceholder(array $data): string
    {
        $values = [];

        foreach ($data as $serial => $row) {

            $params = array_map(
                fn ($attr, $value) => sprintf(
                    '%s:%s_%s%s',
                    // create placeholder from array value ex: ['prefix' => 'DATE(']
                    is_array($value) && isset($value['prefix'])
                        ? $value['prefix']
                        : '',
                    // base placeholder
                    $attr,
                    $serial,
                    // create placeholder from array value ex: ['suffix' => ')']
                    is_array($value) && isset($value['suffix'])
                        ? $value['suffix']
                        : ''
                ),
                array_keys($row),
                array_values($row)
            );

            $values[] = join(',', $params);
        }

        return '(' . join('), (', $values) . ')';
    }
}
