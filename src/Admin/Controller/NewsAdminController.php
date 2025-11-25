<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Service\NewsService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * NewsAdminController - Handles news admin requests
 */
class NewsAdminController extends BaseAdminController
{
    private NewsService $newsService;
    private NewsRepository $newsRepo;
    private CategoryRepository $categoryRepo;
    private string $type;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $type = 'tin-tuc'
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->type = $type;
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->newsRepo = new NewsRepository($db, $lang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'news');
        $this->newsService = new NewsService($this->newsRepo, $this->categoryRepo, $db, $lang, $sluglang);
        
        // Initialize CRUD helper for news_list
        $this->crudHelper = new \Tuezy\Admin\AdminCRUDHelper(
            $db,
            $func,
            'news_list',
            $type,
            $config['news'][$type] ?? []
        );
    }

    /**
     * List news
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function man(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $this->requireAuth();

        $listing = $this->newsService->getListing($this->type, $filters, $page, $perPage);

        // Build URL for pagination
        $this->urlHelper->reset();
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $this->urlHelper->addParam($key, $value);
            }
        }
        $url = $this->urlHelper->getUrl('news', 'man', $this->type);
        $paging = $this->func->pagination($listing['total'], $perPage, $page, $url);

        return [
            'items' => $listing['items'],
            'total' => $listing['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * List news categories (level 1)
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function manList(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $this->requireAuth();

        // Build WHERE conditions
        $where = [];
        if (!empty($filters['keyword'])) {
            $where[] = [
                'clause' => '(tenvi LIKE ? OR tenen LIKE ?)',
                'params' => ["%{$filters['keyword']}%", "%{$filters['keyword']}%"]
            ];
        }

        $result = $this->crudHelper->getList($page, $perPage, $where);
        
        // Build URL for pagination
        $this->urlHelper->reset();
        if (!empty($filters['keyword'])) {
            $this->urlHelper->addParam('keyword', $filters['keyword']);
        }
        $url = $this->urlHelper->getUrl('news', 'man_list', $this->type);
        $paging = $this->func->pagination($result['total'], $perPage, $page, $url);

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * Add news list item
     * 
     * @return array View data
     */
    public function addList(): array
    {
        $this->requireAuth();

        return [
            'item' => null,
            'type' => $this->type,
        ];
    }

    /**
     * Edit news list item
     * 
     * @param int $id Item ID
     * @return array View data
     */
    public function editList(int $id): array
    {
        $this->requireAuth();

        $item = $this->crudHelper->getItem($id);

        if (!$item) {
            $this->func->transfer("Dữ liệu không có thực", "index.php?com=news&act=man_list&type=" . $this->type, false);
        }

        return [
            'item' => $item,
            'type' => $this->type,
        ];
    }

    /**
     * Save news list item
     * 
     * @param array $data Form data
     * @param int|null $id Item ID (null for new)
     * @return bool Success
     */
    public function saveList(array $data, ?int $id = null): bool
    {
        $this->requireAuth();

        return $this->crudHelper->save($data, $id);
    }

    /**
     * Delete news list item
     * 
     * @param int $id Item ID
     * @return bool Success
     */
    public function deleteList(int $id): bool
    {
        $this->requireAuth();

        return $this->crudHelper->delete($id);
    }
}

