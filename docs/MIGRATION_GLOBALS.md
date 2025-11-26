# Migration Guide: Từ Global Variables sang Context/DI

## Tổng quan

Codebase hiện tại đang sử dụng quá nhiều global variables (`$d`, `$func`, `$cache`, `$seo`, etc.), điều này gây khó khăn cho:
- **Testing**: Khó mock dependencies
- **Maintainability**: Khó theo dõi dependencies
- **Coupling**: Code bị ràng buộc chặt với global state

## Giải pháp

Đã tạo 2 lớp để quản lý dependencies:

1. **`Context`** - Class quản lý dependencies tập trung
2. **`GlobalHelper`** - Helper functions để migrate dần dần

## Cách sử dụng

### Phương pháp 1: Sử dụng Context (Khuyến nghị cho code mới)

```php
use Tuezy\Context;

// Lấy Context instance
$context = Context::getInstance();

// Sử dụng services
$db = $context->db();
$cache = $context->cache();
$func = $context->func();
$config = $context->configArray();
```

### Phương pháp 2: Sử dụng Helper Functions (Dễ migrate)

```php
// Thay vì:
global $d, $func, $cache;
$d->query(...);
$func->redirect(...);

// Sử dụng:
use function Tuezy\db;
use function Tuezy\func;
use function Tuezy\cache;

db()->query(...);
func()->redirect(...);
```

### Phương pháp 3: Dependency Injection (Tốt nhất cho Controllers/Services)

```php
class MyController
{
    private $db;
    private $cache;
    private $func;
    
    public function __construct($db, $cache, $func)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->func = $func;
    }
}
```

## Migration Plan

### Phase 1: Setup (Đã hoàn thành)
- ✅ Tạo `Context` class
- ✅ Tạo `GlobalHelper` class
- ✅ Khởi tạo Context trong `bootstrap/app.php`

### Phase 2: Refactor Libraries (Tiếp theo)
- [ ] Refactor `libraries/router.php` để sử dụng Context
- [ ] Refactor `libraries/class/*.php` để nhận dependencies qua constructor

### Phase 3: Refactor Sources
- [ ] Refactor `sources/*.php` để sử dụng helper functions
- [ ] Dần dần chuyển sang Controllers với DI

### Phase 4: Refactor Admin
- [ ] Refactor `admin/sources/*.php` để giảm global variables
- [ ] Sử dụng Admin Controllers đã có với DI

### Phase 5: Cleanup
- [ ] Loại bỏ `getGlobals()` từ Application
- [ ] Loại bỏ việc set vào `$GLOBALS` trong bootstrap
- [ ] Update documentation

## Ví dụ Migration

### Trước (Sử dụng global):

```php
function processOrder() {
    global $d, $func, $cache, $config;
    
    $order = $d->rawQueryOne("SELECT * FROM orders WHERE id = ?", [1]);
    $func->redirect($config['database']['url'] . 'thank-you');
}
```

### Sau (Sử dụng Helper Functions):

```php
use function Tuezy\db;
use function Tuezy\func;
use function Tuezy\config;

function processOrder() {
    $d = db();
    $func = func();
    $config = config();
    
    $order = $d->rawQueryOne("SELECT * FROM orders WHERE id = ?", [1]);
    $func->redirect($config['database']['url'] . 'thank-you');
}
```

### Sau (Sử dụng Context):

```php
use Tuezy\Context;

function processOrder() {
    $context = Context::getInstance();
    
    $order = $context->db()->rawQueryOne("SELECT * FROM orders WHERE id = ?", [1]);
    $func = $context->func();
    $config = $context->configArray();
    $func->redirect($config['database']['url'] . 'thank-you');
}
```

### Sau (Sử dụng DI - Tốt nhất):

```php
class OrderService
{
    private $db;
    private $func;
    private $config;
    
    public function __construct($db, $func, array $config)
    {
        $this->db = $db;
        $this->func = $func;
        $this->config = $config;
    }
    
    public function processOrder(int $orderId): void
    {
        $order = $this->db->rawQueryOne("SELECT * FROM orders WHERE id = ?", [$orderId]);
        $this->func->redirect($this->config['database']['url'] . 'thank-you');
    }
}
```

## Lợi ích

1. **Testability**: Dễ dàng mock dependencies trong unit tests
2. **Maintainability**: Dependencies rõ ràng, dễ theo dõi
3. **Flexibility**: Có thể thay đổi implementation mà không ảnh hưởng toàn bộ codebase
4. **Type Safety**: Type hints giúp phát hiện lỗi sớm
5. **Documentation**: Constructor parameters là documentation tự động

## Backward Compatibility

- Global variables vẫn hoạt động (để đảm bảo không phá vỡ code cũ)
- Helper functions fallback về global nếu Context chưa được khởi tạo
- Migration có thể thực hiện từng phần, không cần refactor toàn bộ cùng lúc

## Best Practices

1. **Code mới**: Luôn sử dụng DI hoặc Context
2. **Code cũ**: Migrate dần dần, ưu tiên các file được sử dụng nhiều
3. **Controllers/Services**: Luôn sử dụng DI qua constructor
4. **Helper functions**: Chỉ dùng cho migration, không dùng cho code mới
5. **Testing**: Mock dependencies thay vì global variables

