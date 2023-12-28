<?php

namespace VulcanPhp\SimpleDb\Query;

use PDOException;
use VulcanPhp\SimpleDb\Exceptions\QueryBuilderException;
use VulcanPhp\SimpleDb\Includes\Paginator\Paginator;

trait QBRead
{
    protected array $query = [
        'sql' => '',
        'joins' => '',
        'join_num' => 0,
    ];

    public function select($fields = '*'): self
    {
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        $this->query['sql'] = sprintf(
            "SELECT %s FROM `%s` AS p",
            $fields,
            $this->table
        );

        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function join(string $class, ?string $cond = null)
    {
        return $this->addJoin($class, '', $cond);
    }

    public function leftJoin(string $class, ?string $cond = null)
    {
        return $this->addJoin($class, 'LEFT', $cond);
    }

    public function rightJoin(string $class, ?string $cond = null)
    {
        return $this->addJoin($class, 'RIGHT', $cond);
    }

    public function crossJoin(string $class, ?string $cond = null)
    {
        return $this->addJoin($class, 'CROSS', $cond);
    }

    protected function addJoin(string $model, string $type, ?string $cond = null): self
    {
        $alias = sprintf('t%s', ++$this->query['join_num']);

        if (class_exists($model)) {
            $join = sprintf(
                " %s JOIN %s AS %s",
                $type,
                $model::tableName(),
                $alias
            );

            if ($cond === null) {
                $foreign = sprintf(
                    '%s_%s',
                    // singularize table name
                    substr($this->table, strlen($this->table) - 1) == 's'
                        ? substr($this->table, 0, strlen($this->table) - 1)
                        : $this->table,
                    $this->primary
                );

                if (!in_array($foreign, $model::fillable())) {
                    throw new QueryBuilderException(
                        sprintf("Foreign Key (%s) does exists on: %s", $foreign, $model::tableName())
                    );
                }

                $join .= sprintf(
                    " ON %s.%s = p.%s ",
                    $alias,
                    $foreign,
                    $this->primary
                );
            } else {
                $join .= sprintf(" ON %s ", $cond);
            }
        } else {
            $join = sprintf(
                " %s JOIN %s %s ON %s ",
                $type,
                $model,
                stripos($model, ' AS ') === false ? ' AS ' . $alias : '',
                $cond
            );
        }

        $this->query['joins'] .= $join;

        return $this;
    }

    public function order(?string $sort = null): self
    {
        if ($sort !== null) {
            $this->query['order'] = $sort;
        }

        return $this;
    }

    public function orderAsc(): self
    {
        $this->query['order'] = 'p.id ASC';
        return $this;
    }

    public function orderDesc(): self
    {
        $this->query['order'] = 'p.id DESC';
        return $this;
    }

    public function group(string $group): self
    {
        $this->query['group'] = $group;
        return $this;
    }

    public function having(string $having): self
    {
        $this->query['having'] = $having;
        return $this;
    }

    public function limit(?int $offset = null, ?int $limit = null): self
    {
        if ($offset !== null) {
            $this->query['limit'] = sprintf(
                " $offset%s",
                $limit !== null ? ", $limit" : ''
            );
        }

        return $this;
    }

    public function fetch($fetch = null): self
    {
        $this->query['fetch'] = $fetch;
        return $this;
    }

    protected function executeQuery()
    {
        $this->getHookHandler()
            ->doAction('select', $this->table);

        if (empty($this->query['sql'])) {
            $this->select();
        }

        $statement = $this->prepare(
            $this->query['sql']
                . $this->query['joins']
                . $this->getWhereSql()
                . (isset($this->query['group']) ? ' GROUP BY ' . trim($this->query['group']) : '')
                . (isset($this->query['having']) ? ' HAVING ' . trim($this->query['having']) : '')
                . (isset($this->query['order']) && $this->query['order'] != 'none' ? ' ORDER BY ' . trim($this->query['order']) : '')
                . (isset($this->query['limit']) ? ' LIMIT ' . trim($this->query['limit']) : '')
        );

        $this->bindWhere($statement);

        // call prepare events
        $statement = $this->getHookHandler()
            ->applyFilters('select', $statement);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === 2006) {
                // reconnect database server if connection failed
                if ($this->getDatabase()->ping()) {
                    // execute after connection database
                    return $this->executeQuery();
                }
            }
        }

        $this->getHookHandler()
            ->doAction('selected', $this->table);

        $this->query['sql'] = $statement;
    }

    public function first()
    {
        $this->limit(1)
            ->executeQuery();

        if (isset($this->query['fetch']) && class_exists($this->query['fetch'])) {
            $result = $this->query['sql']->fetchObject($this->query['fetch']);
        } else {
            $result = $this->query['sql']->fetch($this->query['fetch'] ?? \PDO::FETCH_OBJ);
        }

        $this->resetQuery();

        return $result;
    }

    public function last()
    {
        return $this->order('p.id DESC')
            ->first();
    }

    public function latest()
    {
        return $this->order('p.id DESC')
            ->result();
    }

    public function result()
    {
        $this->executeQuery();

        if (isset($this->query['fetch']) && class_exists($this->query['fetch'])) {
            $result = $this->query['sql']->fetchAll(\PDO::FETCH_CLASS, $this->query['fetch']);
        } else {
            $result = $this->query['sql']->fetchAll($this->query['fetch'] ?? \PDO::FETCH_OBJ);
        }

        $this->resetQuery();

        return $this->getHookHandler()
            ->applyFilters('result', $result);
    }

    public function paginate(int $limit = 10, string $keyword = 'page')
    {
        if (empty($this->query['sql'])) {
            $this->select();
        }

        $paginator = Paginator::create($limit, $limit, $keyword);

        if (!is_sqlite()) {
            $this->query['sql'] = preg_replace('/SELECT /', 'SELECT SQL_CALC_FOUND_ROWS ', $this->query['sql'], 1);
        }

        $this->limit(ceil($limit * ($paginator->getKeywordValue() - 1)), $limit)
            ->executeQuery();

        if (isset($this->query['fetch']) && class_exists($this->query['fetch'])) {
            $paginator->setData($this->query['sql']->fetchAll(\PDO::FETCH_CLASS, $this->query['fetch']));
        } else {
            $paginator->setData($this->query['sql']->fetchAll($this->query['fetch'] ?? \PDO::FETCH_OBJ));
        }

        // get total record
        if (is_sqlite()) {
            $total = $this->prepare(
                "SELECT COUNT() FROM {$this->table} as p "
                    . (!empty($this->query['joins']) ? $this->query['joins'] : '')
                    . $this->getWhereSql()
                    . (isset($this->query['group']) ? ' GROUP BY ' . trim($this->query['group']) : '')
                    . (isset($this->query['having']) ? ' HAVING ' . trim($this->query['having']) : '')
            );

            $this->bindWhere($total);

            $total->execute();
            $paginator->setTotal($total->fetch(\PDO::FETCH_COLUMN));
        } else {
            $total = $this->prepare('SELECT FOUND_ROWS()');
            $total->execute();
            $paginator->setTotal($total->fetch(\PDO::FETCH_COLUMN));
        }

        $paginator->reset();

        $this->resetQuery();

        return $this->getHookHandler()
            ->applyFilters('paginator', $paginator);
    }

    protected function resetQuery(): void
    {
        $this->query = [
            'sql' => '',
            'joins' => '',
            'join_num' => 0,
        ];

        $this->resetWhere();
    }
}
