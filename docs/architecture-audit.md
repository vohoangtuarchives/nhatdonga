## Khảo sát entry point & kiến trúc hiện tại

### 0. Bootstrap chung (`bootstrap/app.php`)
- `bootstrap/app.php` hiện đóng vai trò bán-bootstrap: khởi động session, định nghĩa hằng đường dẫn cơ bản (LIBRARIES/SOURCES/THUMBS/WATERMARK), load composer + `libraries/autoload*.php`, sau đó khởi tạo `Tuezy\Application` với biến `$config` đến từ `libraries/config.php`.
- File này trả về thể hiện `$app` và map các service trong `$app->getGlobals()` ra `$GLOBALS`. Tuy nhiên mỗi entry point vẫn phải tự định nghĩa hằng riêng (BASE_PATH, APP_CONTEXT, TEMPLATE, v.v.) trước khi `require bootstrap/app.php`.
- `Application` chưa được dùng để register router/controller; nó mới dừng ở bước chia sẻ service/DB nên các entry point vẫn phải require router/template thủ công → cơ hội hợp nhất rõ ràng cho bước 2 của kế hoạch.

### 1. Frontend (`index.php`)
- Từ nay sử dụng `bootstrap/context.php` để thiết lập toàn bộ hằng số (thay vì define tràn lan trước khi require bootstrap) → đảm bảo front/admin/api dùng chung quy ước đường dẫn.
- Bootstrap bằng cách include `libraries/config.php`, `libraries/autoload.php`, `libraries/autoload-refactored.php`, sau đó tự tay khởi tạo ~15 service toàn cục (`PDODb`, `Cache`, `Functions`, `Seo`, `Email`, `AltoRouter`, `Cart`, v.v.).
- `libraries/router.php` xử lý toàn bộ pipeline: sanitization (AntiSQLInjection + `SecurityHelper`), map route bằng `AltoRouter`, load các cấu hình `requick`, include `sources/allpage.php` và `sources/<module>.php`, sau đó render `templates/index.php`.
- Hệ thống routing hiện tại pha trộn:
  - Bản refactor mới (RequestHandler + RouteHandler + Repository).
  - Switch-case cũ fallback cho route chưa cấu hình.
- Ngôn ngữ, SEO meta, thumbnail và watermark được xử lý trực tiếp trong router → khó test, khó tái sử dụng.

- `bootstrap/context.php` cũng được tái sử dụng trong admin để thống nhất logic định nghĩa đường dẫn, watermark, template.
- Quy trình tương tự frontend nhưng đơn giản hơn: include `libraries/config.php`, autoload, khởi tạo hằng, đối tượng `$d/$func/$cache`.
- `libraries/requick.php` vẫn được include trực tiếp để thiết lập biến `$com`, `$act`, `$type`.
- Layout điều kiện trong `admin/templates`, gắn thẳng vào `_tpl.php` tương ứng.
- Chưa dùng `src/Application.php` hay Service Container → logic bootstrap lặp lại với frontend/API.

- `api/config.php` giờ chỉ cần gọi `bootstrap_context('api', ...)` nên không còn copy-paste khối định nghĩa hằng số.
- Mỗi endpoint tự `include "config.php"` (file này lại khởi tạo session, config, autoload, DB, cache, func, custom, cart, lang, setting).
- Ví dụ `api/cart.php` bản refactor chỉ còn 20 dòng nhưng vẫn phụ thuộc trực tiếp vào các biến toàn cục tạo sẵn từ `api/config.php`.
- Không có Router chung, mỗi file PHP đóng vai trò controller độc lập.

### 4. Admin AJAX/API (`admin/api/*.php`)
- `admin/api/config.php` tiếp tục nhân bản cấu hình (session, autoload, DB, cache, func) và tự check quyền đăng nhập.
- Các file API trong thư mục này thao tác trực tiếp với DB/Files mà không thông qua lớp Service.

### 5. Tầng domain/service hiện tại
- Business logic nằm rải rác trong:
  - `sources/*.php` (frontend page controller).
  - `admin/sources/*.php` (CRUD admin).
  - `api/*.php` và `admin/api/*.php`.
- Thư mục `src/` đã có nhiều lớp refactor (Application, ServiceContainer, Repository, Helper, Middleware) nhưng chưa được entry point nào sử dụng trọn vẹn. Điều này tạo tình trạng “song song” giữa kiến trúc cũ (global functions) và kiến trúc mới (DI/namespace Tuezy).
- Module sản phẩm hiện đã map về `src/Repository/ProductRepository` + `src/Service/ProductService`, từ đó tái sử dụng cho `sources/product.php`, `api/product.php` và `admin/api/product_size_color.php`.

### 6. Tầng trình bày
- Frontend: template chính `templates/index.php` + layout con trong `templates/layout/*` + component rời.
- Admin: `admin/templates/layout/*` chứa header/menu/footer, còn mỗi module `_tpl.php` tự quản lý asset và logic riêng.
- Asset pipeline trùng lặp giữa `assets/` và `admin/assets/` (nhiều thư viện giống nhau, chưa có cơ chế build/merge).

### 7. Điểm trùng lặp & rủi ro
- **Bootstrap/config trùng**: ít nhất 4 bản copy (`index.php`, `admin/index.php`, `api/config.php`, `admin/api/config.php`). Khi đổi cấu hình DB/cache phải sửa tay ở nhiều nơi.
- **Biến global**: phần lớn luồng dựa vào biến toàn cục ($d, $func, $setting, $lang, ...), gây khó kiểm thử và tái sử dụng.
- **Routing phức tạp**: logic SEO, watermark, slug, ngôn ngữ, chọn template đều nằm trong `libraries/router.php`.
- **Không có module boundary rõ ràng**: repository/service nằm rời rạc, chưa được include bằng chuẩn PSR-4 thống nhất.
- **Bảo trì asset khó**: không có manifest hoặc helper resolve đường dẫn → template include đường dẫn tuyệt đối thủ công.

### 8. Gợi ý chuẩn hoá (liên kết với các bước kế hoạch)
- Dùng `src/Application.php` làm bootstrap duy nhất. Các entry point chỉ cần `require bootstrap.php` rồi lấy service từ container.
- Tách phần config chung (DB, cache, lang, SEO) vào `config/app.php` + `.env`, entry point chỉ quyết định context (web/admin/api).
- Đưa logic trong `sources/*` vào `src/Service` + `src/Controller` rồi render thông qua `ViewHelper`.
- Chuẩn hoá router: RouteHandler trả về controller + template; SEO, breadcrumb, watermark nên thành middleware.
- Viết tài liệu mapping giữa module cũ (`sources/product.php`) và lớp mới (`src/Service/ProductService`, `src/Repository/ProductRepository`) để làm playbook refactor module.

