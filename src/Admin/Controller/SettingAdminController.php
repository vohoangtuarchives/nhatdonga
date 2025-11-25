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
            $data['date_updated'] = time();
            $success = $this->settingRepo->update($settingId, $data);
        } else {
            $data['date_created'] = time();
            $settingId = $this->settingRepo->create($data);
            $success = (bool)$settingId;
        }

        // Save SEO if provided
        if ($success && $dataSeo) {
            $seo = $this->db->rawQueryOne(
                "SELECT * FROM #_seo WHERE id_parent = ? AND com = ? AND act = ? LIMIT 0,1",
                [$settingId, 'setting', 'update']
            );
            
            if (!empty($seo)) {
                $this->db->where('id', $seo['id']);
                $this->db->update('seo', $dataSeo);
            } else {
                $dataSeo['id_parent'] = $settingId;
                $dataSeo['com'] = 'setting';
                $dataSeo['act'] = 'update';
                $this->db->insert('seo', $dataSeo);
            }
        }

        return [
            'success' => $success,
            'messages' => $success ? ['Lưu dữ liệu thành công'] : ['Lưu dữ liệu thất bại']
        ];
    }
}

