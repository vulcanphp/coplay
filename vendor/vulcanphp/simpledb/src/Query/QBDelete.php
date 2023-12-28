<?php

namespace VulcanPhp\SimpleDb\Query;

use PDOException;
use VulcanPhp\SimpleDb\Exceptions\QueryBuilderException;

trait QBDelete
{
    public function delete($where = null): bool
    {
        $this->where($where);

        if (!$this->hasWhere()) {
            throw new QueryBuilderException('Invalid delete condition');
        }

        $this->getHookHandler()
            ->doAction('delete', $this->table);

        $statement = $this->prepare(
            "DELETE FROM `{$this->table}` " . $this->getWhereSql()
        );

        $this->bindWhere($statement);

        $statement = $this->getHookHandler()
            ->applyFilters('delete', $statement);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === 2006) {
                // reconnect database server if connection failed
                if ($this->getDatabase()->ping()) {
                    // delete data after connection database
                    return $this->delete();
                }
            }
        }

        $this->resetWhere();

        $status = $statement->rowCount();

        $this->getHookHandler()
            ->doAction('deleted', $this->table, $status);

        if ($status) {
            $this->changed('delete');
        }

        // return true if deleted row
        return $status;
    }
}
