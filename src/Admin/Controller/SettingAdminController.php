<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\SettingRepository;
use Tuezy\ValidationHelper;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * SettingAdminController - Handles setting admin requests
 */
class SettingAdminController extends BaseAdminController
{
    private SettingRepository $settingRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->settingRepo = new SettingRepository($db, $cache);
    }

    /**
     * Get first setting
     * 
     * @return array|null
     */
    public function getFirst(): ?array
    {
        $this->requireAuth();
        return $this->settingRepo->getFirst();
    }

    /**
     * Save setting
     * 
     * @param array $data Setting data
     * @param array|null $dataSeo SEO data
     * @param int|null $id Setting ID
     * @return array ['success' => bool, 'messages' => array]
     */
    public function save(array $data, ?array $dataSeo = null, ?int $id = null): array
    {
        $this->requireAuth();

        $messages = [];
        $row = $id ? $this->settingRepo->get($id) : $this->settingRepo->getFirst();
        $option = !empty($row['options']) ? json_decode($row['options'], true) : [];

        // Process data
        foreach ($data as $column => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $v2) {
                    if ($k2 == 'coords_iframe') {
                        $option[$k2] = SecurityHelper::sanitize($v2);
                    } else {
                        $option[$k2] = $v2;
                    }
                }
                $data[$column] = json_encode($option);
            } else {
                if (in_array($column, ['mastertool', 'headjs', 'bodyjs', 'analytics'])) {
                    $data[$column] = SecurityHelper::sanitize($value);
                } else {
                    $data[$column] = SecurityHelper::sanitize($value);
                }
            }
        }

        // Validate
        if (empty($option['address'])) {
            $messages[] = 'Địa chỉ không được trống';
        }
        if (empty($option['email'])) {
            $messages[] = 'Email không được trống';
        }
        if (!empty($option['email']) && !ValidationHelper::isEmail($option['email'])) {
            $messages[] = 'Email không hợp lệ';
        }

        if (!empty($messages)) {
            return ['success' => false, 'messages' => $messages];
        }

        // Save setting
        $settingId = $id ?? ($row['id'] ?? null);
        if ($settingId) {
            // Không thêm date_updated vì bảng setting có thể không có cột này
            // Chỉ thêm nếu cột tồn tại trong database
            $success = $this->settingRepo->update($settingId, $data);
        } else {
            $data['date_created'] = time();
            $settingId = $this->settingRepo->create($data);
            $success = (bool)$settingId;
        }

        // Save SEO if provided
        if ($success && $dataSeo && is_array($dataSeo) && !empty($dataSeo)) {
            // Sanitize SEO data
            $dataSeoSanitized = [];
            foreach ($dataSeo as $key => $value) {
                // Chỉ lấy các field SEO hợp lệ
                if (strpos($key, 'title') !== false || 
                    strpos($key, 'keywords') !== false || 
                    strpos($key, 'description') !== false) {
                    $dataSeoSanitized[$key] = SecurityHelper::sanitize($value);
                }
            }
            
            // Chỉ lưu nếu có dữ liệu SEO
            if (!empty($dataSeoSanitized)) {
                // Tìm SEO với id_parent = 0 (vì setting thường dùng id_parent = 0) hoặc settingId
                // Và type = 'setting' để match với getOnDB
                $seo = $this->db->rawQueryOne(
                    "SELECT * FROM #_seo WHERE (id_parent = ? OR id_parent = 0) AND com = ? AND act = ? AND type = ? LIMIT 0,1",
                    [$settingId, 'setting', 'update', 'setting']
                );
                
                if (!empty($seo)) {
                    // Update existing SEO
                    $this->db->where('id', $seo['id']);
                    if (!$this->db->update('seo', $dataSeoSanitized)) {
                        $messages[] = 'Có lỗi khi cập nhật SEO';
                    } else {
                        // Xóa cache sau khi update SEO thành công
                        $this->cache->delete();
                    }
                } else {
                    // Insert new SEO
                    $dataSeoSanitized['id_parent'] = $settingId ?: 0; // Sử dụng 0 nếu settingId null
                    $dataSeoSanitized['com'] = 'setting';
                    $dataSeoSanitized['act'] = 'update';
                    $dataSeoSanitized['type'] = 'setting'; // Thêm type để match với getOnDB
                    if (!$this->db->insert('seo', $dataSeoSanitized)) {
                        $messages[] = 'Có lỗi khi thêm SEO';
                    } else {
                        // Xóa cache sau khi insert SEO thành công
                        $this->cache->delete();
                    }
                }
            }
        }

        return [
            'success' => $success,
            'messages' => $success ? ['Lưu dữ liệu thành công'] : ['Lưu dữ liệu thất bại']
        ];
    }
}

