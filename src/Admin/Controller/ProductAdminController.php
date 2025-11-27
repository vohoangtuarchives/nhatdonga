<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Service\ProductService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * ProductAdminController - Handles product admin requests
 */
class ProductAdminController extends BaseAdminController
{
    private ProductService $productService;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private TagsRepository $tagsRepo;
    private string $type;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $type = 'san-pham'
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->type = $type;
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->productRepo = new ProductRepository($db, $cache, $lang, $sluglang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $this->tagsRepo = new TagsRepository($db, $cache, $lang, $sluglang);
        $this->productService = new ProductService($this->productRepo, $this->categoryRepo, $this->tagsRepo, $db, $lang);
        
        // Initialize CRUD helper for product_list
        $this->crudHelper = new AdminCRUDHelper(
            $db,
            $func,
            'product_list',
            $type,
            $config['product'][$type] ?? []
        );
    }

    /**
     * List products
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function man(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $this->requireAuth();

        // Admin: không filter theo status mặc định (activeOnly = false)
        $listing = $this->productService->getListing($this->type, $filters, $page, $perPage, 'default', 'desc', false);

        // Build URL for pagination
        $this->urlHelper->reset();
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $this->urlHelper->addParam($key, $value);
            }
        }
        $url = $this->urlHelper->getUrl('product', 'man', $this->type);
        $paging = $this->func->pagination($listing['total'], $perPage, $page, $url);

        return [
            'items' => $listing['items'],
            'total' => $listing['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * List product categories (level 1)
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
        $url = $this->urlHelper->getUrl('product', 'man_list', $this->type);
        $paging = $this->func->pagination($result['total'], $perPage, $page, $url);

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * Add product list item
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
     * Edit product list item
     * 
     * @param int $id Item ID
     * @return array View data
     */
    public function editList(int $id): array
    {
        $this->requireAuth();

        $item = $this->crudHelper->getItem($id);

        if (!$item) {
            $this->func->transfer("Dữ liệu không có thực", "index.php?com=product&act=man_list&type=" . $this->type, false);
        }

        return [
            'item' => $item,
            'type' => $this->type,
        ];
    }

    /**
     * Save product list item
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
     * Delete product list item
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

