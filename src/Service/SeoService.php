<?php

namespace Tuezy\Service;

/**
 * SeoService - SEO data management
 * Refactored from class.Seo.php
 * 
 * Handles SEO metadata operations
 */
class SeoService
{
    private $db;
    private array $data = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Set SEO data
     * 
     * @param string $key Data key
     * @param string $value Data value
     */
    public function set(string $key, string $value): void
    {
        if (!empty($key) && !empty($value)) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Get SEO data
     * 
     * @param string $key Data key
     * @return string Data value or empty string
     */
    public function get(string $key): string
    {
        return $this->data[$key] ?? '';
    }

    /**
     * Get SEO data from database
     * 
     * @param int $id Parent ID
     * @param string $com Component name
     * @param string $act Action name
     * @param string $type Type
     * @return array|null SEO data or null
     */
    public function getOnDB(int $id = 0, string $com = '', string $act = '', string $type = ''): ?array
    {
        if (!$id && $act !== 'update') {
            return null;
        }

        if ($id) {
            return $this->db->rawQueryOne(
                "SELECT * FROM #_seo 
                 WHERE id_parent = ? AND com = ? AND act = ? AND type = ? 
                 LIMIT 0,1",
                [$id, $com, $act, $type]
            );
        }

        return $this->db->rawQueryOne(
            "SELECT * FROM #_seo 
             WHERE com = ? AND act = ? AND type = ? 
             LIMIT 0,1",
            [$com, $act, $type]
        );
    }

    /**
     * Update SEO data in table
     * 
     * @param string $json JSON encoded SEO data
     * @param string $table Table name
     * @param int $id Record ID
     */
    public function updateSeoDB(string $json, string $table, int $id): void
    {
        if (!empty($table) && $id > 0) {
            $this->db->rawQuery(
                "UPDATE #_$table SET options = ? WHERE id = ?",
                [$json, $id]
            );
        }
    }

    /**
     * Save SEO data
     * 
     * @param int $idParent Parent ID
     * @param string $com Component name
     * @param string $act Action name
     * @param string $type Type
     * @param array $data SEO data
     * @return bool Success
     */
    public function saveSeo(int $idParent, string $com, string $act, string $type, array $data): bool
    {
        $existing = $this->getOnDB($idParent, $com, $act, $type);

        $seoData = [
            'id_parent' => $idParent,
            'com' => $com,
            'act' => $act,
            'type' => $type,
        ];

        // Merge SEO fields
        foreach ($data as $key => $value) {
            if (strpos($key, 'title') !== false || 
                strpos($key, 'keywords') !== false || 
                strpos($key, 'description') !== false) {
                $seoData[$key] = $value;
            }
        }

        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update('seo', $seoData);
        } else {
            return $this->db->insert('seo', $seoData);
        }
    }
}

