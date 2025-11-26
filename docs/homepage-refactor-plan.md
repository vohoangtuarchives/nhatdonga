# Đánh giá Kế hoạch Xây dựng Trang Chủ ISP Việt Nam

## 📋 Tổng Quan

Kế hoạch hiện tại **KHÔNG hoàn toàn phù hợp** với cấu trúc đã refactor. Cần điều chỉnh để tuân theo các pattern đã được thiết lập.

---

## ❌ Vấn Đề Với Kế Hoạch Hiện Tại

### 1. **Vi phạm Separation of Concerns**
- **Kế hoạch đề xuất:** Query database trực tiếp trong template (`index_tpl_new.php`)
- **Vấn đề:** Template không nên chứa logic truy cập database
- **Code hiện tại đang làm:**
```php
// ❌ KHÔNG NÊN - Query trong template
$featuredProducts = $cache->get("SELECT ... FROM #_product ...", [...], 'result', 7200);
```

### 2. **Không Sử Dụng Repository Pattern**
- **Kế hoạch đề xuất:** Sử dụng `$cache->get()` trực tiếp với raw SQL
- **Vấn đề:** Bỏ qua `ProductRepository` đã được refactor
- **Đã có sẵn:** `ProductRepository` với các methods như `getProducts()`, `getFeaturedProducts()`

### 3. **Không Sử Dụng Controller Pattern**
- **Kế hoạch đề xuất:** Logic trong `sources/index.php` và template
- **Vấn đề:** Không tận dụng `BaseController` và Controller pattern
- **Đã có sẵn:** `ProductController`, `NewsController`, `StaticController` làm ví dụ

### 4. **Không Sử Dụng ViewRenderer**
- **Kế hoạch đề xuất:** Include template trực tiếp
- **Vấn đề:** Không tận dụng `ViewRenderer` để quản lý data flow
- **Đã có sẵn:** `ViewRenderer` với methods `render()`, `share()`

---

## ✅ Cách Tiếp Cận Đúng (Phù Hợp Với Refactor)

### 1. **Tạo HomeController**

**File:** `src/Controller/HomeController.php`

```php
<?php

namespace Tuezy\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\PhotoRepository; // Cần tạo cho certificates

class HomeController extends BaseController
{
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private PhotoRepository $photoRepo;

    public function __construct($db, $cache, $func, $seo, array $config)
    {
        parent::__construct($db, $cache, $func, $seo, $config);
        
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';
        
        $this->productRepo = new ProductRepository($db, $cache, $lang, $sluglang, 'san-pham');
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $this->photoRepo = new PhotoRepository($db, $cache, $lang); // Cần tạo
    }

    public function index(): array
    {
        // Lấy dữ liệu qua Repository
        $featuredProducts = $this->productRepo->getFeaturedProducts(12);
        $productCategories = $this->categoryRepo->getCategories('san-pham', 8);
        $certificates = $this->photoRepo->getByType('chung-nhan', 6);
        
        // Lấy sản phẩm theo danh mục
        $categoryProducts = [];
        foreach (array_slice($productCategories, 0, 2) as $category) {
            $categoryProducts[$category['id']] = [
                'info' => $category,
                'products' => $this->productRepo->getProductsByCategory($category['id'], 8)
            ];
        }

        // SEO
        $seoDB = $this->seo->getOnDB(0, 'setting', 'update', 'setting');
        $seolang = 'vi';
        
        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
            $this->seo->set('h1', $seoDB['title' . $seolang]);
        }
        
        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }
        
        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }
        
        $this->seo->set('url', $this->func->getPageURL());

        // Return data cho view
        return [
            'featuredProducts' => $featuredProducts,
            'productCategories' => $productCategories,
            'categoryProducts' => $categoryProducts,
            'certificates' => $certificates,
        ];
    }
}
```

### 2. **Mở Rộng ProductRepository**

**File:** `src/Repository/ProductRepository.php` (thêm methods)

```php
/**
 * Lấy sản phẩm nổi bật
 */
public function getFeaturedProducts(int $limit = 12): array
{
    $cacheKey = "featured_products_{$this->lang}_{$limit}";
    
    return $this->cache->get(
        "SELECT id, name{$this->lang}, slugvi, slugen, photo, regular_price, sale_price, discount 
         FROM #_product 
         WHERE type = ? AND find_in_set('hienthi',status) AND find_in_set('noibat',status) 
         ORDER BY numb, id DESC 
         LIMIT 0, ?",
        [$this->defaultType, $limit],
        'result',
        7200
    );
}

/**
 * Lấy sản phẩm theo danh mục
 */
public function getProductsByCategory(int $categoryId, int $limit = 8): array
{
    $cacheKey = "products_category_{$categoryId}_{$this->lang}_{$limit}";
    
    return $this->cache->get(
        "SELECT id, name{$this->lang}, slugvi, slugen, photo, regular_price, sale_price, discount 
         FROM #_product 
         WHERE type = ? AND find_in_set('hienthi',status) AND id_list = ? 
         ORDER BY numb, id DESC 
         LIMIT 0, ?",
        [$this->defaultType, $categoryId, $limit],
        'result',
        7200
    );
}
```

### 3. **Tạo PhotoRepository (Cho Certificates)**

**File:** `src/Repository/PhotoRepository.php`

```php
<?php

namespace Tuezy\Repository;

class PhotoRepository
{
    private \PDODb $d;
    private ?\Cache $cache;
    private string $lang;

    public function __construct(\PDODb $d, ?\Cache $cache, string $lang)
    {
        $this->d = $d;
        $this->cache = $cache;
        $this->lang = $lang;
    }

    public function getByType(string $type, int $limit = 6): array
    {
        return $this->cache->get(
            "SELECT name{$this->lang}, photo, link 
             FROM #_photo 
             WHERE type = ? AND find_in_set('hienthi',status) 
             ORDER BY numb, id DESC
             LIMIT 0, ?",
            [$type, $limit],
            'result',
            7200
        );
    }
}
```

### 4. **Cập Nhật sources/index.php**

**File:** `sources/index.php`

```php
<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Helper\GlobalHelper;
use Tuezy\Controller\HomeController;

// Get dependencies
$db = GlobalHelper::db();
$cache = GlobalHelper::cache();
$seo = GlobalHelper::seo();
$func = GlobalHelper::func();
$config = GlobalHelper::config();

// Tạo HomeController
$homeController = new HomeController($db, $cache, $func, $seo, $config);

// Lấy data từ controller
$viewData = $homeController->index();

// Extract data để template sử dụng
extract($viewData);

// Include template (giữ nguyên cách hiện tại để tương thích)
include TEMPLATE . "index/index_tpl.php";
```

### 5. **Template Chỉ Nhận Data (Không Query DB)**

**File:** `templates/index/index_tpl_new.php`

```php
<?php
/**
 * Template trang chủ - ISP Việt Nam
 * Template chỉ nhận data từ controller, KHÔNG query database
 * 
 * Variables có sẵn từ controller:
 * - $featuredProducts: Sản phẩm nổi bật
 * - $productCategories: Danh mục sản phẩm
 * - $categoryProducts: Sản phẩm theo danh mục
 * - $certificates: Chứng nhận
 */

// ✅ Template chỉ hiển thị, không query
?>

<!-- Hero Section -->
<section class="hero-section-isp">
    <!-- ... HTML ... -->
    <?php if (!empty($featuredProducts[0])): 
        $heroProduct = $featuredProducts[0];
    ?>
        <!-- Hiển thị sản phẩm -->
    <?php endif; ?>
</section>

<!-- Product Grid -->
<section class="products-section-isp">
    <?php foreach ($featuredProducts as $product): ?>
        <!-- Product card -->
    <?php endforeach; ?>
</section>
```

---

## 📝 Kế Hoạch Điều Chỉnh

### Phase 1: Tạo Repository Methods
- [ ] Thêm `getFeaturedProducts()` vào `ProductRepository`
- [ ] Thêm `getProductsByCategory()` vào `ProductRepository`
- [ ] Tạo `PhotoRepository` cho certificates

### Phase 2: Tạo HomeController
- [ ] Tạo `HomeController` extends `BaseController`
- [ ] Implement method `index()` để lấy tất cả data
- [ ] Setup SEO trong controller

### Phase 3: Cập Nhật sources/index.php
- [ ] Sử dụng `HomeController` thay vì query trực tiếp
- [ ] Pass data từ controller vào template

### Phase 4: Làm Sạch Template
- [ ] Xóa tất cả queries khỏi `index_tpl_new.php`
- [ ] Template chỉ nhận và hiển thị data
- [ ] Đảm bảo tất cả variables được pass từ controller

### Phase 5: CSS & Styling
- [ ] Tạo file SCSS mới: `assets/scss/pages/_homepage-isp.scss`
- [ ] Import vào `main.scss`
- [ ] Implement design theo yêu cầu (xanh lá, cam, etc.)

---

## ✅ Lợi Ích Của Cách Tiếp Cận Này

1. **Tuân Thủ Architecture:** Sử dụng Controller-Repository pattern đã refactor
2. **Separation of Concerns:** Logic tách biệt khỏi template
3. **Testability:** Dễ test controller và repository
4. **Maintainability:** Dễ bảo trì và mở rộng
5. **Consistency:** Đồng nhất với các controller khác (ProductController, NewsController)

---

## 🔄 Migration Path

1. **Giữ nguyên template hiện tại** để không break
2. **Tạo HomeController** song song
3. **Test kỹ** trước khi thay thế
4. **Dần dần migrate** từ query trực tiếp sang repository

---

## 📌 Kết Luận

Kế hoạch hiện tại **CẦN ĐIỀU CHỈNH** để phù hợp với architecture đã refactor. Nên:

- ✅ Sử dụng Controller-Repository pattern
- ✅ Tách logic khỏi template
- ✅ Sử dụng ViewRenderer (nếu cần)
- ❌ KHÔNG query database trong template
- ❌ KHÔNG bỏ qua Repository pattern

