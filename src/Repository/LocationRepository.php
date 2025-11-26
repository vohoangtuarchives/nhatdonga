<?php

namespace Tuezy\Repository;

/**
 * LocationRepository - Data access layer for locations (city, district, ward)
 */
class LocationRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get all cities
     * 
     * @return array
     */
    public function getCities(): array
    {
        return $this->d->rawQuery(
            "SELECT name, id FROM #_city ORDER BY id ASC",
            []
        ) ?: [];
    }

    /**
     * Get districts by city ID
     * 
     * @param int $cityId City ID
     * @return array
     */
    public function getDistrictsByCity(int $cityId): array
    {
        return $this->d->rawQuery(
            "SELECT name, id FROM #_district WHERE id_city = ? ORDER BY id ASC",
            [$cityId]
        ) ?: [];
    }

    /**
     * Get wards by district ID
     * 
     * @param int $districtId District ID
     * @return array
     */
    public function getWardsByDistrict(int $districtId): array
    {
        return $this->d->rawQuery(
            "SELECT name, id FROM #_ward WHERE id_district = ? ORDER BY id ASC",
            [$districtId]
        ) ?: [];
    }

    /**
     * Get city by ID
     * 
     * @param int $id City ID
     * @return array|null
     */
    public function getCityById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_city WHERE id = ? LIMIT 0,1",
            [$id]
        ) ?: null;
    }

    /**
     * Get district by ID
     * 
     * @param int $id District ID
     * @return array|null
     */
    public function getDistrictById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_district WHERE id = ? LIMIT 0,1",
            [$id]
        ) ?: null;
    }

    /**
     * Get ward by ID
     * 
     * @param int $id Ward ID
     * @return array|null
     */
    public function getWardById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_ward WHERE id = ? LIMIT 0,1",
            [$id]
        ) ?: null;
    }
}

