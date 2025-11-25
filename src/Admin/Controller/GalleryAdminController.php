<?php

namespace Tuezy\Admin\Controller;

use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * GalleryAdminController - Handles gallery admin requests
 */
class GalleryAdminController extends BaseAdminController
{
    /**
     * List gallery photos
     * 
     * @param int $idParent Parent ID
     * @param string $com Component name
     * @param string $type Type
     * @param string $kind Kind
     * @param string $val Value
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function manPhoto(int $idParent, string $com, string $type, string $kind, string $val, int $page = 1, int $perPage = 10): array
    {
        $this->requireAuth();

        $where = "id_parent = ? and com = ? and type = ? and kind = ? and val = ?";
        $params = [$idParent, $com, $type, $kind, $val];
        
        $start = ($page - 1) * $perPage;
        $sql = "SELECT * FROM #_gallery WHERE {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
        $items = $this->db->rawQuery($sql, $params);
        
        $countSql = "SELECT COUNT(*) as total FROM #_gallery WHERE {$where}";
        $total = $this->db->rawQueryOne($countSql, $params);
        $totalItems = (int)($total['total'] ?? 0);
        
        // Build URL for pagination
        $url = "index.php?com={$com}&act=man_photo&id_parent={$idParent}&type={$type}&kind={$kind}&val={$val}";
        $paging = $this->func->pagination($totalItems, $perPage, $page, $url);

        return [
            'items' => $items,
            'total' => $totalItems,
            'paging' => $paging,
        ];
    }

    /**
     * Get gallery photo by ID
     * 
     * @param int $id Photo ID
     * @param int $idParent Parent ID
     * @param string $com Component name
     * @param string $type Type
     * @param string $kind Kind
     * @param string $val Value
     * @return array|null
     */
    public function getPhoto(int $id, int $idParent, string $com, string $type, string $kind, string $val): ?array
    {
        $this->requireAuth();
        
        return $this->db->rawQueryOne(
            "SELECT * FROM #_gallery WHERE id_parent = ? AND com = ? AND type = ? AND kind = ? AND val = ? AND id = ? LIMIT 0,1",
            [$idParent, $com, $type, $kind, $val, $id]
        );
    }

    /**
     * Save gallery photo
     * 
     * @param array $data Photo data
     * @param int|null $id Photo ID (null for new)
     * @param int $idParent Parent ID
     * @param string $com Component name
     * @param string $type Type
     * @param string $kind Kind
     * @param string $val Value
     * @return bool Success
     */
    public function savePhoto(array $data, ?int $id, int $idParent, string $com, string $type, string $kind, string $val): bool
    {
        $this->requireAuth();

        $data['id_parent'] = $idParent;
        $data['com'] = $com;
        $data['type'] = $type;
        $data['kind'] = $kind;
        $data['val'] = $val;

        if ($id) {
            $data['date_updated'] = time();
            $this->db->where('id', $id);
            return $this->db->update('gallery', $data);
        } else {
            $data['date_created'] = time();
            $maxNumb = $this->db->rawQueryOne(
                "SELECT MAX(numb) as max_numb FROM #_gallery WHERE id_parent = ? AND com = ? AND type = ? AND kind = ? AND val = ?",
                [$idParent, $com, $type, $kind, $val]
            );
            $data['numb'] = ($maxNumb['max_numb'] ?? 0) + 1;
            return $this->db->insert('gallery', $data);
        }
    }

    /**
     * Delete gallery photo
     * 
     * @param int $id Photo ID
     * @return bool Success
     */
    public function deletePhoto(int $id): bool
    {
        $this->requireAuth();
        
        $item = $this->db->rawQueryOne("SELECT * FROM #_gallery WHERE id = ? LIMIT 0,1", [$id]);
        if ($item) {
            if ($this->db->rawQuery("DELETE FROM #_gallery WHERE id = ?", [$id])) {
                if (!empty($item['photo'])) {
                    $this->func->deleteFile(UPLOAD_GALLERY . $item['photo']);
                }
                return true;
            }
        }
        return false;
    }
}

