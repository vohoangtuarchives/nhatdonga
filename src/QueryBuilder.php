<?php

namespace Tuezy;

/**
 * QueryBuilder - Fluent query builder wrapper
 * Provides a more intuitive interface for database queries
 */
class QueryBuilder
{
    private $d;
    private string $table;
    private array $wheres = [];
    private array $params = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private ?int $offset = null;
    private array $selects = ['*'];

    public function __construct($d, string $table)
    {
        $this->d = $d;
        $this->table = $table;
    }

    /**
     * Select columns
     * 
     * @param array|string $columns Columns to select
     * @return self
     */
    public function select($columns): self
    {
        if (is_string($columns)) {
            $this->selects = [$columns];
        } else {
            $this->selects = $columns;
        }
        return $this;
    }

    /**
     * Add WHERE condition
     * 
     * @param string $column Column name
     * @param mixed $value Value
     * @param string $operator Operator (=, !=, >, <, etc.)
     * @return self
     */
    public function where(string $column, $value, string $operator = '='): self
    {
        $this->wheres[] = [
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        ];
        $this->params[] = $value;
        return $this;
    }

    /**
     * Add WHERE IN condition
     * 
     * @param string $column Column name
     * @param array $values Array of values
     * @return self
     */
    public function whereIn(string $column, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'column' => $column,
            'value' => $values,
            'operator' => 'IN',
            'raw' => "$column IN ($placeholders)",
        ];
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    /**
     * Add WHERE LIKE condition
     * 
     * @param string $column Column name
     * @param string $value Value
     * @return self
     */
    public function whereLike(string $column, string $value): self
    {
        $this->wheres[] = [
            'column' => $column,
            'value' => "%$value%",
            'operator' => 'LIKE',
        ];
        $this->params[] = "%$value%";
        return $this;
    }

    /**
     * Add ORDER BY
     * 
     * @param string $column Column name
     * @param string $direction ASC or DESC
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "$column $direction";
        return $this;
    }

    /**
     * Add LIMIT
     * 
     * @param int $limit Limit
     * @param int|null $offset Offset
     * @return self
     */
    public function limit(int $limit, ?int $offset = null): self
    {
        $this->limit = $limit;
        if ($offset !== null) {
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * Get all results
     * 
     * @return array
     */
    public function get(): array
    {
        $sql = $this->buildSql();
        return $this->d->rawQuery($sql, $this->params);
    }

    /**
     * Get first result
     * 
     * @return array|null
     */
    public function first(): ?array
    {
        $this->limit(1);
        $sql = $this->buildSql();
        $result = $this->d->rawQueryOne($sql, $this->params);
        return $result ?: null;
    }

    /**
     * Get count
     * 
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM #_{$this->table}";
        $where = $this->buildWhere();
        if ($where) {
            $sql .= " WHERE $where";
        }
        $result = $this->d->rawQueryOne($sql, $this->params);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Build SQL query
     * 
     * @return string
     */
    private function buildSql(): string
    {
        $select = implode(', ', $this->selects);
        $sql = "SELECT $select FROM #_{$this->table}";
        
        $where = $this->buildWhere();
        if ($where) {
            $sql .= " WHERE $where";
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset !== null) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        return $sql;
    }

    /**
     * Build WHERE clause
     * 
     * @return string
     */
    private function buildWhere(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $conditions = [];
        foreach ($this->wheres as $where) {
            if (isset($where['raw'])) {
                $conditions[] = $where['raw'];
            } else {
                $conditions[] = "{$where['column']} {$where['operator']} ?";
            }
        }

        return implode(' AND ', $conditions);
    }

    /**
     * Reset query builder
     * 
     * @return self
     */
    public function reset(): self
    {
        $this->wheres = [];
        $this->params = [];
        $this->orderBy = null;
        $this->limit = null;
        $this->offset = null;
        $this->selects = ['*'];
        return $this;
    }
}

