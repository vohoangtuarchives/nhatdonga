<?php

namespace Tuezy\API\Controller;

/**
 * LocationAPIController - Handles location API requests (district, ward)
 */
class LocationAPIController extends BaseAPIController
{
    /**
     * Get districts by city
     * 
     * @return void Outputs HTML options
     */
    public function getDistricts(): void
    {
        $id_city = (int)$this->post('id_city', 0);

        $districts = [];
        if ($id_city > 0) {
            $districts = $this->db->rawQuery(
                "SELECT id, name FROM #_district WHERE id_city = ? ORDER BY name ASC",
                [$id_city]
            );
        }

        // Output HTML options
        echo '<option value="">' . (defined('quanhuyen') ? quanhuyen : 'Chọn quận/huyện') . '</option>';
        foreach ($districts as $district) {
            echo '<option value="' . $district['id'] . '">' . htmlspecialchars($district['name']) . '</option>';
        }
        exit;
    }

    /**
     * Get wards by district
     * 
     * @return void Outputs HTML options
     */
    public function getWards(): void
    {
        $id_district = (int)$this->post('id_district', 0);

        $wards = [];
        if ($id_district > 0) {
            $wards = $this->db->rawQuery(
                "SELECT id, name FROM #_ward WHERE id_district = ? ORDER BY name ASC",
                [$id_district]
            );
        }

        // Output HTML options
        echo '<option value="">' . (defined('phuongxa') ? phuongxa : 'Chọn phường/xã') . '</option>';
        foreach ($wards as $ward) {
            echo '<option value="' . $ward['id'] . '">' . htmlspecialchars($ward['name']) . '</option>';
        }
        exit;
    }
}

