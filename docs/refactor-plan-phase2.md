# Kế hoạch Refactor Phase 2 - Hoàn thiện & Mở rộng

## Tổng quan

Sau Phase 1 đã hoàn thành:
- ✅ Bootstrap & config chuẩn hóa
- ✅ Product module (Repository + Service)
- ✅ News module (Service + Admin integration)
- ✅ Admin UI assets manifest

Phase 2 tập trung vào:
1. Hoàn thiện Service layer cho các module đã có Repository
2. Refactor các API endpoints còn lại
3. Refactor admin sources theo từng module
4. Tối ưu hóa và chuẩn hóa toàn bộ

---

## Phase 2.1: Hoàn thiện Service Layer (Ưu tiên cao)

### 2.1.1. News Service
**Mục tiêu:** Tạo `src/Service/NewsService.php` để tách business logic từ `sources/news.php`

**Công việc (✅ 25/11/2025):**
- [x] Tạo `NewsService` với các method:
  - `getDetailContext(int $id, string $type, bool $increaseView = true)`
  - `getListing(string $type, array $filters, int $page, int $perPage)`
  - `getRelatedNews(int $id, string $type, int $limit)`
- [x] Refactor `src/Repository/NewsRepository.php` bổ sung filter + alias
- [x] Refactor `sources/news.php` để sử dụng `NewsService`
- [x] Refactor `admin/sources/news.php` để sử dụng `NewsService`
- [ ] Refactor `api/video.php` nếu cần news data (chưa cần ở hiện trạng)

**File liên quan:**
- `src/Repository/NewsRepository.php`
- `src/Service/NewsService.php`
- `sources/news.php`
- `admin/sources/news.php`

**Lợi ích:**
- Giảm code lặp lại giữa frontend và admin
- Dễ test và maintain
- Chuẩn hóa cách xử lý news

---

### 2.1.2. Order Service
**Mục tiêu:** Tạo `src/Service/OrderService.php` để tách business logic từ `sources/order.php` và `admin/sources/order.php`

**Công việc (✅ 25/11/2025):**
- [x] Tạo `OrderService` với các method:
  - `createOrder(array $data): int|false`
  - `updateOrderStatus(int $id, int $status): bool`
  - `getOrderDetail(int $id): ?array` (getDetailContext)
  - `getOrderList(array $filters, int $start, int $perPage): array` (getListing)
  - `cancelOrder(int $id, string $reason): bool`
  - `getStatistics(): array`
  - `saveOrderDetails(int $orderId, array $items): bool`
- [x] Tích hợp `OrderService` vào `OrderHandler`
- [x] Refactor `admin/sources/order.php` để sử dụng `OrderService`
- [x] `OrderHandler` giữ lại như wrapper cho validation và email

**File liên quan:**
- `src/Repository/OrderRepository.php` (đã có)
- `src/OrderHandler.php` (đã có)
- `sources/order.php` (đã refactor một phần)
- `admin/sources/order.php` (đã refactor một phần)

**Lợi ích:**
- Thống nhất logic xử lý order giữa frontend và admin
- Dễ mở rộng tính năng (refund, tracking, etc.)

---

### 2.1.3. User Repository & Service
**Mục tiêu:** Tạo `src/Repository/UserRepository.php` và `src/Service/UserService.php`

**Công việc (✅ 25/11/2025):**
- [x] Tạo `UserRepository` với các method:
  - `getById(int $id): ?array`
  - `getByEmail(string $email): ?array`
  - `getByUsername(string $username): ?array`
  - `getByUsernameOrEmail(string $identifier): ?array`
  - `create(array $data): int|false`
  - `update(int $id, array $data): bool`
  - `updatePassword(int $id, string $password): bool`
  - `emailExists()`, `usernameExists()`, `getAll()`, `count()`
- [x] Tạo `UserService` với các method:
  - `register(array $data): int|false`
  - `login(string $username, string $password): ?array`
  - `updateProfile(int $id, array $data): bool`
  - `updatePassword(int $id, string $oldPassword, string $newPassword): bool`
  - `forgotPassword(string $email): string|false`
  - `resetPassword(string $code, string $password): bool`
  - `getListing(array $filters, int $page, int $perPage): array`
- [x] Refactor `src/UserHandler.php` để sử dụng `UserService`
- [x] Refactor `sources/user.php` để sử dụng `UserService`
- [x] Refactor `admin/sources/user.php` để sử dụng `UserService`

**File liên quan:**
- `src/UserHandler.php` (đã có)
- `sources/user.php` (đã refactor một phần)
- `admin/sources/user.php` (chưa refactor)

**Lợi ích:**
- Tách biệt data access và business logic
- Dễ test authentication/authorization
- Chuẩn hóa user management

---

### 2.1.4. Static/Photo/Video Service
**Mục tiêu:** Tạo Service layer cho các module nhỏ

**Công việc (✅ 25/11/2025):**
- [x] Tạo `StaticService` - wrapper đơn giản cho StaticRepository
- [x] Tạo `PhotoService` với các method:
  - `getPhotoGallery(string $type, int $parentId, string $kind, string $val): array`
  - `getWatermarkConfig(): ?array`
  - `getLogo()`, `getFavicon()`, `getBanner()`, `getSlider()`, `getSocial()`, `getPartners()`
- [x] Tạo `VideoService` với các method:
  - `getVideoList(string $type, array $filters, int $start, int $limit): array`
  - `getVideoDetail(int $id): ?array`
  - `countVideos(string $type, array $filters): int`
  - `getFeaturedVideos()`, `getVideoLink()`
- [x] Refactor `sources/static.php` để sử dụng `StaticService`
- [x] Refactor `sources/photo.php` để sử dụng `PhotoService`
- [x] Refactor `sources/video.php` để sử dụng `VideoService`

**File liên quan:**
- `src/Repository/StaticRepository.php` (đã có)
- `src/Repository/PhotoRepository.php` (đã có)
- `sources/photo.php` (chưa refactor)
- `sources/video.php` (chưa refactor)
- `sources/static.php` (chưa refactor)

---

### 2.1.5. Product Admin Integration
**Mục tiêu:** Đưa `admin/sources/product.php` vào chung kiến trúc Service/Repository

**Công việc (✅ 25/11/2025):**
- [x] Khởi tạo `ProductService` trong admin context
- [x] Thay `AdminCRUDHelper` theo signature mới
- [x] Sử dụng `ProductService->getListing()` cho trang danh sách
- [x] Sử dụng `ProductService->getDetailContext()` cho trang edit/copy
- [x] Chuẩn hóa luồng delete và pagination

**File liên quan:**
- `admin/sources/product.php`
- `src/Service/ProductService.php`
- `src/Repository/ProductRepository.php`

**Lợi ích:**
- Admin và frontend dùng chung business layer
- Giảm raw query trong admin
- Dễ bảo trì khi thêm filter hoặc thuộc tính mới

---

## Phase 2.2: Refactor API Endpoints (Ưu tiên trung bình)

### 2.2.1. Video API
**Công việc (✅ 25/11/2025):**
- [x] Refactor `api/video.php` để sử dụng `VideoService`
- [x] Sử dụng `SecurityHelper` cho sanitization
- [x] Chuẩn hóa response format

### 2.2.2. Location APIs
**Công việc (✅ 25/11/2025):**
- [x] Tạo `LocationRepository` với methods: `getCities()`, `getDistrictsByCity()`, `getWardsByDistrict()`
- [x] Refactor `api/district.php` để sử dụng `LocationRepository`
- [x] Refactor `api/ward.php` để sử dụng `LocationRepository`
- [x] Sử dụng `SecurityHelper` cho sanitization

### 2.2.3. Comment API
**Công việc (✅ 25/11/2025):**
- [x] Hoàn thiện `api/comment.php` với error handling
- [x] Sử dụng `SecurityHelper` cho sanitization
- [x] Chuẩn hóa error handling (JSON response)
- [ ] Tạo `CommentService` nếu logic phức tạp (Comments class đã đủ tốt)

---

## Phase 2.3: Refactor Admin Sources (Ưu tiên trung bình)

### 2.3.1. Admin User Management
**Công việc (✅ 25/11/2025):**
- [x] Refactor `admin/sources/user.php` để sử dụng `UserService`
- [x] Sử dụng `UserService->getListing()` cho member management
- [x] Chuẩn hóa permission checking với `AdminPermissionHelper`

### 2.3.2. Admin Contact Management
**Công việc (✅ 25/11/2025):**
- [x] Refactor `admin/sources/contact.php` để sử dụng `ContactRepository`
- [x] Sử dụng `AdminCRUDHelper`
- [x] Chuẩn hóa form handling

### 2.3.3. Admin Static/Photo/Tags
**Công việc (✅ 25/11/2025):**
- [x] Refactor `admin/sources/static.php` để sử dụng `StaticService`
- [x] Refactor `admin/sources/photo.php` để sử dụng `PhotoService`
- [x] `admin/sources/tags.php` đã refactor trước đó
- [x] Sử dụng Repository và Service

### 2.3.4. Admin API Endpoints
**Công việc (✅ 25/11/2025):**
- [x] Refactor `admin/api/category.php` để sử dụng `SecurityHelper`
- [x] Refactor `admin/api/comment.php` để sử dụng `SecurityHelper`
- [x] Refactor `admin/api/upload.php` để sử dụng `SecurityHelper`
- [x] Refactor `admin/api/status.php` để sử dụng `SecurityHelper`
- [x] Sử dụng Repository pattern và SecurityHelper

---

## Phase 2.4: Tối ưu hóa & Chuẩn hóa (Ưu tiên thấp)

### 2.4.1. Middleware System
**Công việc (✅ 25/11/2025):**
- [x] Tạo `AuthMiddleware` cho authentication
- [x] Tạo `AdminAuthMiddleware` cho admin authentication
- [x] Tạo `LoggingMiddleware` cho request logging
- [x] Tạo `RateLimitingMiddleware` cho rate limiting (100 requests/minute default)
- [x] Tạo `MiddlewareStack` để quản lý và execute middleware chain
- [x] Tạo ví dụ sử dụng middleware (`examples/middleware-usage.php`)
- [ ] Áp dụng middleware vào router (cần refactor router trước, có thể làm sau)

### 2.4.2. Error Handling
**Công việc (✅ 25/11/2025):**
- [x] Chuẩn hóa error response format (JSON cho API, HTML cho web)
- [x] Tạo `ErrorHandler` class với error logging
- [x] Implement error logging vào file
- [x] Custom error pages (404, 403, 500)
- [x] Register error handlers (set_error_handler, set_exception_handler)

### 2.4.3. Validation Layer
**Công việc (✅ 25/11/2025):**
- [x] Mở rộng `ValidationHelper` với rules phức tạp hơn:
  - `length()` - Validate string length
  - `numeric()` - Validate numeric with min/max
  - `url()` - Validate URL
  - `date()` - Validate date format
  - `password()` - Validate password strength
  - `array()` - Validate array of values
- [x] Tạo validation methods cho từng module:
  - `validateOrder()` - Order validation
  - `validateUserRegistration()` - User registration validation
- [x] Chuẩn hóa validation messages (tiếng Việt)

### 2.4.4. Testing Infrastructure
**Công việc (✅ 25/11/2025):**
- [x] Setup PHPUnit configuration (`phpunit.xml`)
- [x] Tạo test bootstrap file (`tests/bootstrap.php`)
- [x] Tạo example test (`tests/Unit/ExampleTest.php`)
- [x] Tạo testing guide (`tests/README.md`)
- [ ] Viết unit tests cho Repository (có thể làm sau)
- [ ] Viết unit tests cho Service (có thể làm sau)
- [ ] Viết integration tests cho API endpoints (có thể làm sau)

---

## Thứ tự ưu tiên thực hiện

1. **Phase 2.1.1** - News Service (1-2 ngày)
2. **Phase 2.1.2** - Order Service (1-2 ngày)
3. **Phase 2.1.3** - User Repository & Service (2-3 ngày)
4. **Phase 2.1.4** - Static/Photo/Video Service (1-2 ngày)
5. **Phase 2.2** - API Endpoints (2-3 ngày)
6. **Phase 2.3** - Admin Sources (3-5 ngày)
7. **Phase 2.4** - Tối ưu hóa (ongoing)

---

## Checklist cho mỗi module refactor

Khi refactor một module, đảm bảo:

- [ ] Tạo Repository nếu chưa có
- [ ] Tạo Service nếu logic phức tạp
- [ ] Refactor sources/*.php để sử dụng Service
- [ ] Refactor api/*.php để sử dụng Service
- [ ] Refactor admin/sources/*.php để sử dụng Service
- [ ] Sử dụng SecurityHelper cho sanitization
- [ ] Sử dụng ValidationHelper cho validation
- [ ] Sử dụng SEOHelper cho SEO (nếu cần)
- [ ] Sử dụng BreadcrumbHelper cho breadcrumbs (nếu cần)
- [ ] Test kỹ các chức năng
- [ ] Cập nhật documentation

---

## Metrics & Goals

**Mục tiêu giảm code:**
- Mỗi module refactor: giảm 70-90% code trong sources/*.php
- API endpoints: giảm 60-80% code
- Admin sources: giảm 80-95% code

**Mục tiêu chất lượng:**
- Type hints đầy đủ
- PSR-4 autoloading
- Separation of concerns rõ ràng
- Dễ test và maintain

---

## Notes

- Luôn backup file gốc trước khi refactor
- Test kỹ từng module trước khi chuyển sang module khác
- Giữ backward compatibility khi có thể
- Document các breaking changes
- Review code trước khi merge

