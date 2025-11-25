<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\StaticRepository;
use Tuezy\Service\StaticService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * StaticAdminController - Handles static page admin requests
 */
class StaticAdminController extends BaseAdminController
{
    private StaticService $staticService;
    private StaticRepository $staticRepo;
    private string $type;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $type = ''
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->type = $type;
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->staticRepo = new StaticRepository($db, $cache, $lang, $sluglang);
        $this->staticService = new StaticService($this->staticRepo);
    }

    /**
     * Get static content by type
     * 
     * @return array|null
     */
    public function getByType(): ?array
    {
        $this->requireAuth();
        return $this->staticService->getByType($this->type);
    }

    /**
     * Save static content
     * 
     * @param array $data Static data
     * @param array|null $dataSeo SEO data
     * @return bool Success
     */
    public function save(array $data, ?array $dataSeo = null): bool
    {
        $this->requireAuth();

        // Sanitize data
        $data = SecurityHelper::sanitizeArray($data);

        // Handle status
        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_value) {
                if ($attr_value != "") {
                    $status .= $attr_value . ',';
                }
            }
            $data['status'] = !empty($status) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        // Generate slug if needed
        if (!empty($this->config['static'][$this->type]['name'])) {
            $data['slugvi'] = (!empty($data['namevi'])) ? $this->func->changeTitle($data['namevi']) : '';
            $data['slugen'] = (!empty($data['nameen'])) ? $this->func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $this->type;

        // Save static content
        $static = $this->staticService->getByType($this->type);
        if ($static) {
            return $this->staticRepo->update($static['id'], $data);
        } else {
            $data['date_created'] = time();
            return $this->staticRepo->create($data);
        }
    }
}

