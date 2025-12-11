<?php

namespace Tuezy\Service;

use Tuezy\Repository\StaticRepository;

/**
 * StaticService - Business logic layer for static content
 * Simple wrapper around StaticRepository
 */
class StaticService
{
    public function __construct(
        private StaticRepository $statics
    ) {
    }

    /**
     * Get static content by type
     * 
     * @param string $type Static type
     * @param bool $activeOnly Only active items (with 'hienthi' status)
     * @return array|null
     */
    public function getByType(string $type, bool $activeOnly = true): ?array
    {
        return $this->statics->getByType($type, $activeOnly);
    }

    /**
     * Get static content by ID
     * 
     * @param int $id Static ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->statics->getById($id);
    }

    /**
     * Get all static content by type
     * 
     * @param string $type Static type
     * @param bool $active Only active items
     * @return array
     */
    public function getAllByType(string $type, bool $active = true): array
    {
        return $this->statics->getAllByType($type, $active);
    }
}

