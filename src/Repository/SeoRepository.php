<?php

namespace Tuezy\Repository;

use Tuezy\Domain\SEO\SeoRepository as SeoRepositoryInterface;

class SeoRepository implements SeoRepositoryInterface
{
    public function __construct(private \PDODb $db) {}

    public function getByParent(int $idParent, string $com, string $act, string $type): ?array
    {
        if (!$idParent && $act !== 'update') {
            return null;
        }
        if ($idParent) {
            $row = $this->db->rawQueryOne(
                "SELECT * FROM #_seo WHERE id_parent = ? AND com = ? AND act = ? AND type = ? LIMIT 0,1",
                [$idParent, $com, $act, $type]
            );
            return is_array($row) ? $row : null;
        }
        $row = $this->db->rawQueryOne(
            "SELECT * FROM #_seo WHERE com = ? AND act = ? AND type = ? LIMIT 0,1",
            [$com, $act, $type]
        );
        return is_array($row) ? $row : null;
    }

    public function saveMeta(int $idParent, string $com, string $act, string $type, array $data): bool
    {
        $existing = $this->getByParent($idParent, $com, $act, $type);
        $seoData = [
            'id_parent' => $idParent,
            'com' => $com,
            'act' => $act,
            'type' => $type,
        ];
        foreach ($data as $key => $value) {
            if (!is_string($value)) continue;
            if (strpos($key, 'title') !== false || strpos($key, 'keywords') !== false || strpos($key, 'description') !== false || strpos($key, 'schema') !== false) {
                $seoData[$key] = $value;
            }
        }
        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update('seo', $seoData);
        }
        return $this->db->insert('seo', $seoData);
    }

    public function getMetaVoByParent(int $idParent, string $com, string $act, string $type, string $seolang = 'vi'): ?\Tuezy\Domain\SEO\SeoMeta
    {
        $row = $this->getByParent($idParent, $com, $act, $type);
        if (!$row) return null;
        $title = $row['title' . $seolang] ?? null;
        $keywords = $row['keywords' . $seolang] ?? null;
        $description = $row['description' . $seolang] ?? null;
        $schema = $row['schema' . $seolang] ?? null;
        return new \Tuezy\Domain\SEO\SeoMeta($title, $keywords, $description, $schema);
    }
}
