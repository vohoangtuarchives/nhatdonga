<?php

namespace Tuezy\Admin;

use Tuezy\UploadHandler;
use Tuezy\SecurityHelper;

/**
 * AdminCRUDHelper - CRUD operations helper for admin
 * Reduces repetitive CRUD code in admin sources
 */
class AdminCRUDHelper
{
    private $d;
    private $func;
    private string $table;
    private string $type;
    private array $configType;
    private UploadHandler $uploadHandler;

    public function __construct($d, $func, string $table, string $type, array $configType)
    {
        $this->d = $d;
        $this->func = $func;
        $this->table = $table;
        $this->type = $type;
        $this->configType = $configType;
        $this->uploadHandler = new UploadHandler($func, $d);
    }

    /**
     * Get list of items with pagination
     * 
     * @param int $curPage Current page
     * @param int $perPage Items per page
     * @param array $where Additional WHERE conditions
     * @param array $params Query parameters
     * @return array ['items' => array, 'paging' => string]
     */
    public function getList(int $curPage = 1, int $perPage = 20, array $where = [], array $params = []): array
    {
        $whereClause = "type = ?";
        $queryParams = [$this->type];

        // Add additional where conditions
        foreach ($where as $condition) {
            $whereClause .= " AND " . $condition['clause'];
            $queryParams = array_merge($queryParams, $condition['params']);
        }

        // Merge with provided params
        $queryParams = array_merge($queryParams, $params);

        // Calculate pagination
        $startpoint = ($curPage * $perPage) - $perPage;
        $limit = " LIMIT $startpoint, $perPage";

        // Get items
        $sql = "SELECT * FROM #_{$this->table} WHERE $whereClause ORDER BY numb, id DESC $limit";
        $items = $this->d->rawQuery($sql, $queryParams);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM #_{$this->table} WHERE $whereClause";
        $total = $this->d->rawQueryOne($countSql, $queryParams);
        $totalItems = (int)($total['total'] ?? 0);

        // Generate pagination
        $paging = $this->func->pagination($totalItems, $perPage, $curPage, "index.php?com={$this->table}&act=man&type={$this->type}");

        return [
            'items' => $items,
            'paging' => $paging,
            'total' => $totalItems,
        ];
    }

    /**
     * Get single item by ID
     * 
     * @param int $id Item ID
     * @return array|null
     */
    public function getItem(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_{$this->table} WHERE id = ? AND type = ? LIMIT 0,1",
            [$id, $this->type]
        );
    }

    /**
     * Save item (insert or update)
     * 
     * @param array $data Item data
     * @param int|null $id Item ID (null for insert)
     * @return bool Success status
     */
    public function save(array $data, ?int $id = null): bool
    {
        // Sanitize data
        $data = $this->sanitizeData($data);

        // Set type
        $data['type'] = $this->type;

        // Check slug uniqueness before saving
        $this->validateSlug($data, $id);

        // Handle file uploads
        $this->handleFileUploads($data, $id);

        if ($id) {
            // Update
            $this->d->where('id', $id);
            return $this->d->update($this->table, $data);
        } else {
            // Insert
            if (!isset($data['date_created'])) {
                $data['date_created'] = time();
            }
            if (!isset($data['numb'])) {
                $data['numb'] = 0;
            }
            return $this->d->insert($this->table, $data);
        }
    }

    /**
     * Validate slug uniqueness
     * 
     * @param array $data Item data
     * @param int|null $id Item ID (for edit)
     * @throws \Exception If slug already exists
     */
    private function validateSlug(array $data, ?int $id = null): void
    {
        // Check if slug fields exist in data
        $slugFields = ['slugvi', 'slugen'];
        $hasSlug = false;
        $slugToCheck = '';

        foreach ($slugFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $hasSlug = true;
                $slugToCheck = $data[$field];
                break;
            }
        }

        if (!$hasSlug) {
            return; // No slug to validate
        }

        // Prepare checkSlug data
        $checkSlugData = [
            'slug' => $slugToCheck,
            'id' => $id ?? 0,
            'table' => $this->table,
            'type' => $this->type,
        ];

        // Check slug uniqueness
        $result = $this->func->checkSlug($checkSlugData);

        if ($result === 'exist') {
            throw new \Exception("Đường dẫn đã tồn tại. Đường dẫn truy cập mục này có thể bị trùng lặp.");
        }
    }

    /**
     * Delete item
     * 
     * @param int $id Item ID
     * @return bool Success status
     */
    public function delete(int $id): bool
    {
        // Sử dụng rawQuery để đảm bảo chính xác
        // rawQuery trả về số dòng bị ảnh hưởng hoặc false nếu có lỗi
        $result = $this->d->rawQuery("DELETE FROM #_{$this->table} WHERE id = ? AND type = ?", [$id, $this->type]);
        // Kiểm tra xem có dòng nào bị xóa không (result có thể là array hoặc số)
        if ($result === false) {
            return false;
        }
        // Nếu result là array (PDODb có thể trả về array), kiểm tra số dòng bị ảnh hưởng
        if (is_array($result)) {
            return count($result) > 0 || (isset($result['affected_rows']) && $result['affected_rows'] > 0);
        }
        // Nếu result là số (số dòng bị ảnh hưởng)
        return (int)$result > 0;
    }

    /**
     * Delete multiple items
     * 
     * @param array $ids Array of IDs
     * @return int Number of deleted items
     */
    public function deleteMultiple(array $ids): int
    {
        $count = 0;
        foreach ($ids as $id) {
            if ($this->delete((int)$id)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Update status
     * 
     * @param int $id Item ID
     * @param string $status Status value
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $this->d->where('id', $id);
        return $this->d->update($this->table, ['status' => $status]);
    }

    /**
     * Update order (numb)
     * 
     * @param int $id Item ID
     * @param int $numb Order number
     * @return bool
     */
    public function updateOrder(int $id, int $numb): bool
    {
        $this->d->where('id', $id);
        return $this->d->update($this->table, ['numb' => $numb]);
    }

    /**
     * Sanitize data array
     * 
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    private function sanitizeData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = SecurityHelper::sanitize($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = SecurityHelper::sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Handle file uploads based on config
     * 
     * @param array $data Data array (will be modified)
     * @param int|null $id Item ID
     */
    private function handleFileUploads(array &$data, ?int $id = null): void
    {
        if (empty($this->configType['img_type'])) {
            return;
        }

        $imgType = $this->configType['img_type'];
        
        // Handle main photo
        if ($this->func->hasFile("photo")) {
            $uploadPath = $this->getUploadPath();
            $this->uploadHandler->setUploadPath($uploadPath);
            $uploaded = $this->uploadHandler->upload('photo');
            if ($uploaded) {
                $data['photo'] = $uploaded;
            }
        }

        // Handle other file fields based on config
        // Can be extended based on specific needs
    }

    /**
     * Get upload path based on table and type
     * 
     * @return string Upload path
     */
    private function getUploadPath(): string
    {
        $uploadPaths = [
            'product' => UPLOAD_PRODUCT_L,
            'news' => UPLOAD_NEWS_L,
            'photo' => UPLOAD_PHOTO_L,
        ];

        return $uploadPaths[$this->table] ?? UPLOAD_PHOTO_L;
    }
}

