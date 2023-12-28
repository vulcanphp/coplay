<?php

namespace VulcanPhp\SimpleDb\Query;

use PDOException;
use VulcanPhp\SimpleDb\Exceptions\QueryBuilderException;

trait QBUpdate
{
    public function update(array $data, $where = null): bool
    {
        $this->where($where);

        if (!$this->hasWhere()) {
            throw new QueryBuilderException('Invalid update condition');
        }

        if (isset($data[0]) && is_array($data[0])) {
            throw new QueryBuilderException('Invalid data format to update');
        }

        $this->getHookHandler()
            ->doAction('update', $this->table);

        $data = $this->getHookHandler()
            ->applyFilters('update_data', $data);

        $statement = $this->prepare(
            sprintf(
                "UPDATE `{$this->table}` SET %s %s",
                implode(', ', array_map(
                    fn ($attr) => "$attr=:$attr",
                    array_keys($data)
                )),
                $this->getWhereSql()
            )
        );

        foreach ($data as $key => $val) {
            $statement->bindValue(":$key", $val);
        }

        $this->bindWhere($statement);

        $statement = $this->getHookHandler()
            ->applyFilters('update', $statement);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === 2006) {
                // reconnect database server if connection failed
                if ($this->getDatabase()->ping()) {
                    // update data after connection database
                    return $this->update($data);
                }
            }
        }

        $this->resetWhere();

        $status = $statement->rowCount();

        $this->getHookHandler()
            ->doAction('updated', $this->table, $status);

        if ($status) {
            $this->changed('update');
        }

        // return true if updated row
        return $status;
    }
}
