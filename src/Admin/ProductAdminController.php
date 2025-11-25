<?php

namespace Tuezy\Admin;

use Tuezy\Config;

/**
 * ProductAdminController - Example admin controller for products
 * Demonstrates how to use AdminController base class
 */
class ProductAdminController extends AdminController
{
    private AdminCRUDHelper $crudHelper;
    private AdminURLHelper $urlHelper;

    public function __construct($d, $func, $flash, $cache, Config $config, string $com, string $act, string $type, array $configType)
    {
        parent::__construct($d, $func, $flash, $cache, $config, $com, $act, $type, $configType);
        
        $this->crudHelper = new AdminCRUDHelper($d, $func, 'product', $type, $configType);
        $this->urlHelper = new AdminURLHelper('index.php');
    }

    /**
     * Route to appropriate action
     */
    protected function route(): ?string
    {
        // Build return URL
        $strUrl = $this->urlHelper->buildFromRequest(
            ['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'],
            ['comment_status', 'keyword']
        );

        switch ($this->act) {
            case "man":
                return $this->handleMan();

            case "add":
                return "product/man/man_add";

            case "edit":
            case "copy":
                return $this->handleEdit();

            case "save":
            case "save_copy":
                $this->handleSave($strUrl);
                return null;

            case "delete":
                $this->handleDelete($strUrl);
                return null;

            // Add more cases as needed

            default:
                return "404";
        }
    }

    /**
     * Handle list (man) action
     */
    private function handleMan(): string
    {
        global $curPage, $items, $paging;
        
        $curPage = (int)($this->get('p') ?: 1);
        $result = $this->crudHelper->getList($curPage, 20);
        
        $items = $result['items'];
        $paging = $result['paging'];
        
        return "product/man/mans";
    }

    /**
     * Handle edit action
     */
    private function handleEdit(): string
    {
        global $item;
        
        if ($this->act == 'copy' && (!isset($this->configType['copy']) || $this->configType['copy'] == false)) {
            return "404";
        }

        $id = (int)$this->get('id', 0);
        $item = $this->crudHelper->getItem($id);
        
        if (!$item) {
            $this->transfer("Dữ liệu không tồn tại", "index.php?com=product&act=man&type={$this->type}", false);
            return null;
        }

        return "product/man/man_add";
    }

    /**
     * Handle save action
     */
    private function handleSave(string $strUrl): void
    {
        $id = !empty($this->get('id')) ? (int)$this->get('id') : null;
        $data = $_POST['data'] ?? [];

        if ($this->crudHelper->save($data, $id)) {
            $message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
            $returnUrl = $this->urlHelper->getReturnUrl('product', 'man', $this->type) . $strUrl;
            $this->transfer($message, $returnUrl);
        } else {
            $this->transfer("Có lỗi xảy ra", "index.php?com=product&act=man&type={$this->type}", false);
        }
    }

    /**
     * Handle delete action
     */
    private function handleDelete(string $strUrl): void
    {
        $id = (int)$this->get('id', 0);
        
        if ($this->crudHelper->delete($id)) {
            $this->transfer("Xóa dữ liệu thành công", "index.php?com=product&act=man&type={$this->type}" . $strUrl);
        } else {
            $this->transfer("Có lỗi xảy ra", "index.php?com=product&act=man&type={$this->type}" . $strUrl, false);
        }
    }
}

