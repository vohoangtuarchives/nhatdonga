# Admin Panel Refactoring Guide

## 🎯 Tổng Quan

Admin panel đã được refactor với **5 class mới** để giảm code lặp lại và cải thiện maintainability.

## 📦 Các Class Mới

### 1. AdminController
Base controller cho tất cả admin controllers.

### 2. AdminAuthHelper
Quản lý authentication và authorization.

### 3. AdminCRUDHelper
CRUD operations helper - giảm code lặp lại trong admin sources.

### 4. AdminURLHelper
URL building và parameter management.

### 5. AdminPermissionHelper
Permission và role-based access control.

## 🚀 Cách Áp Dụng

### Bước 1: Refactor admin/sources/product.php

**Code cũ (~2800 dòng):**
```php
switch ($act) {
    case "man":
        viewMans();
        $template = "product/man/mans";
        break;
    case "save":
        saveMan();
        break;
    // ... nhiều cases và functions
}

function viewMans() {
    global $d, $func, $curPage, $items, $paging;
    // ... 50+ dòng code
}

function saveMan() {
    global $d, $func;
    // ... 100+ dòng code
}
```

**Code mới (~100 dòng):**
```php
use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Admin\AdminURLHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\Config;
use Tuezy\RequestHandler;

$configObj = new Config($config);
$params = RequestHandler::getParams();
$adminAuth = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminAuth->requireLogin();

$crudHelper = new AdminCRUDHelper($d, $func, 'product', $type, $config['product'][$type]);
$urlHelper = new AdminURLHelper('index.php');

switch ($act) {
    case "man":
        $result = $crudHelper->getList($curPage, 20);
        $items = $result['items'];
        $paging = $result['paging'];
        $template = "product/man/mans";
        break;
    
    case "save":
        $data = $_POST['data'] ?? [];
        if ($crudHelper->save($data, $id)) {
            $func->transfer("Thành công", $urlHelper->getReturnUrl('product', 'man', $type));
        }
        break;
}
```

### Bước 2: Sử Dụng AdminController (Advanced)

Tạo controller class:
```php
use Tuezy\Admin\ProductAdminController;
use Tuezy\Config;

$configObj = new Config($config);
$controller = new ProductAdminController($d, $func, $flash, $cache, $configObj, $com, $act, $type, $config['product'][$type] ?? []);
$template = $controller->handle();
```

## 📊 So Sánh

### admin/sources/product.php
- **Code cũ:** ~2800 dòng
- **Code mới:** ~100-200 dòng (tùy cách áp dụng)
- **Giảm:** ~90-95% code

### admin/sources/news.php
- **Code cũ:** ~1950 dòng
- **Code mới:** ~100-200 dòng
- **Giảm:** ~90% code

## ✅ Lợi Ích

1. **Consistency** - Tất cả admin modules dùng cùng pattern
2. **Maintainability** - Dễ sửa, dễ mở rộng
3. **Type Safety** - Type hints và return types
4. **Reusability** - CRUD helper dùng cho nhiều modules
5. **Security** - Centralized authentication và permission

## 🎓 Examples

Xem `examples/admin_product_refactored.php` để biết cách áp dụng chi tiết.

## ⚠️ Lưu Ý

- Backup file gốc trước khi refactor
- Test kỹ từng module
- Có thể áp dụng từng phần
- Giữ backward compatible

