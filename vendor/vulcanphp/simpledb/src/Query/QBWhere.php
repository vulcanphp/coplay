<?php

namespace VulcanPhp\SimpleDb\Query;

trait QBWhere
{
    protected array $where = [
        'sql' => '',
        'bind' => []
    ];

    public function where($condition = null, string $type = 'AND'): self
    {
        if ($condition !== null) {
            return $this->addWhere($type, $condition);
        }

        return $this;
    }

    public function whereIn(string $field, array $values)
    {
        return $this->where(
            sprintf(
                "%s IN('%s')",
                $field,
                join(
                    "','",
                    array_map(
                        fn ($value) => addslashes($value),
                        $values
                    )
                )
            )
        );
    }

    public function match(string $match, string $condition, ?string $mode = null)
    {
        $mode = $mode !== null ? sprintf(' in %s mode', $mode) : '';

        return $this->where("MATCH ({$match}) AGAINST ('{$condition}'{$mode})");
    }

    public function andWhere($conditions)
    {
        return $this->addWhere('AND', $conditions);
    }

    public function orWhere($conditions)
    {
        return $this->addWhere('OR', $conditions);
    }

    protected function addWhere(string $method, $conditions): self
    {
        $command = '';

        if (is_array($conditions)) {
            $command = sprintf(
                "%s %s",
                $method,
                implode(
                    " {$method} ",
                    array_map(
                        fn ($attr, $value) => $attr . (is_array($value) ?
                            sprintf(
                                " IN (%s)",
                                join(
                                    ",",
                                    array_map(
                                        fn ($index) => ':' . str_replace('.', '', $attr) . '_' . $index,
                                        array_keys($value)
                                    )
                                )
                            )
                            : " = :" . str_replace('.', '', $attr)
                        ),
                        array_keys($conditions),
                        array_values($conditions)
                    )
                )
            );

            $this->where['bind'] = array_merge($this->where['bind'], $conditions);
        } elseif (is_string($conditions)) {
            $command = sprintf('%s %s', $method, $conditions);
        }

        $this->where['sql'] .= sprintf(
            ' %s ',
            empty($this->where['sql']) ? ltrim($command, $method . ' ') : $command
        );

        return $this;
    }

    public function hasWhere(): bool
    {
        return !empty(trim($this->where['sql']));
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    public function getWhereSql(): string
    {
        return $this->hasWhere() ? ' WHERE ' .  trim($this->getWhere()['sql']) . ' ' : '';
    }

    public function bindWhere(&$statement): void
    {
        foreach ($this->getWhere()['bind'] ?? [] as $attr => $value) {
            $attr = ':' . str_replace('.', '', $attr);

            if (is_array($value)) {
                foreach ($value as $index => $val) {
                    $statement->bindValue($attr . '_' . $index, $val);
                }
            } else {
                $statement->bindValue($attr, $value);
            }
        }
    }

    protected function resetWhere()
    {
        $this->where = [
            'sql' => '',
            'bind' => []
        ];
    }
}
