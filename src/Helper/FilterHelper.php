<?php

namespace Tuezy\Helper;

use Tuezy\SecurityHelper;

/**
 * FilterHelper - Helper for filtering and searching
 * Provides utilities for building filter queries
 */
class FilterHelper
{
    private array $filters = [];
    private array $params = [];

    /**
     * Add filter condition
     * 
     * @param string $field Field name
     * @param mixed $value Filter value
     * @param string $operator Operator (=, LIKE, IN, etc.)
     * @return self
     */
    public function addFilter(string $field, $value, string $operator = '='): self
    {
        if ($value === null || $value === '') {
            return $this;
        }

        $this->filters[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];

        return $this;
    }

    /**
     * Add keyword search (searches multiple fields)
     * 
     * @param string $keyword Search keyword
     * @param array $fields Fields to search in
     * @return self
     */
    public function addKeyword(string $keyword, array $fields = []): self
    {
        if (empty($keyword) || empty($fields)) {
            return $this;
        }

        $keyword = SecurityHelper::sanitize($keyword);
        $conditions = [];

        foreach ($fields as $field) {
            $conditions[] = "{$field} LIKE ?";
            $this->params[] = "%{$keyword}%";
        }

        if (!empty($conditions)) {
            $this->filters[] = [
                'field' => '(' . implode(' OR ', $conditions) . ')',
                'value' => null,
                'operator' => '',
            ];
        }

        return $this;
    }

    /**
     * Add date range filter
     * 
     * @param string $field Date field
     * @param int|null $dateFrom Start date (timestamp)
     * @param int|null $dateTo End date (timestamp)
     * @return self
     */
    public function addDateRange(string $field, ?int $dateFrom = null, ?int $dateTo = null): self
    {
        if ($dateFrom) {
            $this->addFilter($field, $dateFrom, '>=');
        }

        if ($dateTo) {
            $this->addFilter($field, $dateTo, '<=');
        }

        return $this;
    }

    /**
     * Add status filter
     * 
     * @param string $status Status value
     * @param string $field Status field name
     * @return self
     */
    public function addStatus(string $status, string $field = 'status'): self
    {
        if (empty($status)) {
            return $this;
        }

        $this->filters[] = [
            'field' => $field,
            'value' => $status,
            'operator' => 'FIND_IN_SET',
        ];

        return $this;
    }

    /**
     * Build WHERE clause
     * 
     * @return string WHERE clause
     */
    public function buildWhere(): string
    {
        if (empty($this->filters)) {
            return '1=1';
        }

        $conditions = [];

        foreach ($this->filters as $filter) {
            if ($filter['operator'] === 'FIND_IN_SET') {
                $conditions[] = "find_in_set(?, {$filter['field']})";
                $this->params[] = $filter['value'];
            } elseif ($filter['operator'] === 'IN') {
                $placeholders = implode(',', array_fill(0, count($filter['value']), '?'));
                $conditions[] = "{$filter['field']} IN ({$placeholders})";
                $this->params = array_merge($this->params, $filter['value']);
            } elseif ($filter['value'] === null) {
                // Already processed (like keyword search)
                $conditions[] = $filter['field'];
            } else {
                $conditions[] = "{$filter['field']} {$filter['operator']} ?";
                $this->params[] = $filter['value'];
            }
        }

        return implode(' AND ', $conditions);
    }

    /**
     * Get parameters
     * 
     * @return array Parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Reset filters
     * 
     * @return self
     */
    public function reset(): self
    {
        $this->filters = [];
        $this->params = [];
        return $this;
    }
}

