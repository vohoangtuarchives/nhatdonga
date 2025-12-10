<?php

namespace Tuezy\Repository;

/**
 * SettingRepository - Data access layer for settings
 */
class SettingRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get setting
     * 
     * @param int $id Setting ID (usually 0 or 1)
     * @return array|null
     */
    public function get(int $id = 0): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT * FROM #_setting WHERE id = ? LIMIT 0,1",
            [$id]
        );
        return $result ?: null;
    }

    /**
     * Get first setting (default)
     * 
     * @return array|null
     */
    public function getFirst(): ?array
    {
        $result = $this->d->rawQueryOne("SELECT * FROM #_setting LIMIT 0,1");
        return $result ?: null;
    }

    /**
     * Create setting
     * 
     * @param array $data Setting data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        return $this->d->insert('setting', $data);
    }

    /**
     * Update setting
     * 
     * @param int $id Setting ID
     * @param array $data Setting data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        // Note: Setting table may not have date_updated column
        // Only add date_updated if it's explicitly provided in $data
        // Don't auto-add to avoid column not found errors
        $this->d->where('id', $id);
        return $this->d->update('setting', $data);
    }

    /**
     * Update setting options
     * 
     * @param int $id Setting ID
     * @param array $options Options array
     * @return bool
     */
    public function updateOptions(int $id, array $options): bool
    {
        return $this->update($id, ['options' => json_encode($options)]);
    }

    /**
     * Get setting options
     * 
     * @param int $id Setting ID
     * @return array
     */
    public function getOptions(int $id = 0): array
    {
        $setting = $this->get($id);
        if (!empty($setting['options'])) {
            return json_decode($setting['options'], true) ?? [];
        }
        return [];
    }
}

