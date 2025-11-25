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

## 🧱 Bootstrap & Config mới

- Mọi entry point (frontend, admin, public API, admin API) đều chạy qua `bootstrap/context.php` → đảm bảo chỉ cần khai báo `APP_CONTEXT` và đường dẫn tùy theo môi trường, tránh define lặp lại.
- Cấu hình chung được gom vào `config/app.php` (đọc từ `config/env.example` hoặc biến môi trường) rồi inject vào `Tuezy\Application`.
- Khi thêm module/admin endpoint mới, chỉ cần `require bootstrap/context.php` và gọi `bootstrap_context('admin')` thay vì tự include `libraries/config.php`.

## 🧩 Module sản phẩm (Service + Repository)

- `src/Repository/ProductRepository.php` được viết lại với type hints rõ ràng, không còn sử dụng global function helper.
- `src/Service/ProductService.php` gom toàn bộ nghiệp vụ sản phẩm (detail, list, gallery, size/color, brand, xoá combination) để tái sử dụng giữa:
  - `sources/product.php`
  - `api/product.php`
  - `admin/api/product_size_color.php`
- View layer được chuẩn hoá thông qua component `templates/components/product-grid.php`, dùng được cho AJAX/API và template chính.

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

## 🔍 Checklist triển khai nhanh

- [ ] Route/context mới gọi `bootstrap_context()` thay vì tự define hằng số.
- [ ] Service/repository được inject thông qua `ProductService` (hoặc helper tương đương).
- [ ] View sử dụng component trong `templates/components` thay vì echo trực tiếp.
- [ ] Ghi chú thay đổi vào `docs/architecture-audit.md` để đội khác theo dõi.

