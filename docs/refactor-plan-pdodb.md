# Kế Hoạch Refactor PDODb.php

## Tổng Quan
- **File hiện tại**: `src/Infrastructure/Database/PDODb.php` (3,622 dòng)
- **Vấn đề chính**: 
  - File quá lớn, khó maintain
  - Nhiều responsibilities (query builder, connection, transaction, logging)
  - Thiếu type hints
  - Code style không nhất quán
  - Namespace issue với PDOStatement đã được fix

## Mục Tiêu Refactor
1. Tách file lớn thành nhiều class nhỏ theo Single Responsibility Principle
2. Cải thiện type safety với strict types và type hints
3. Chuẩn hóa code style theo PSR-12
4. Tăng khả năng test được
5. Giữ backward compatibility với code hiện tại

## Kiến Trúc Mới

### 1. Core Classes

#### 1.1. `PDODb.php` (Main Class - Facade)
**Mục đích**: Entry point, delegate to specialized classes
**Dòng code dự kiến**: ~200-300
**Responsibilities**:
- Khởi tạo và quản lý các sub-components
- Provide public API cho backward compatibility
- Delegate operations to specialized classes

#### 1.2. `ConnectionManager.php`
**Mục đích**: Quản lý PDO connection
**Dòng code dự kiến**: ~150
**Responsibilities**:
- Tạo và quản lý PDO connection
- Handle connection pooling (nếu cần)
- Connection configuration
**Methods**:
- `connect(): PDO`
- `disconnect(): void`
- `pdo(): PDO`
- `isConnected(): bool`

#### 1.3. `QueryBuilder.php`
**Mục đích**: Build SQL queries
**Dòng code dự kiến**: ~500-600
**Responsibilities**:
- WHERE, JOIN, ORDER BY, GROUP BY, HAVING
- Build INSERT, UPDATE, DELETE queries
- Parameter binding
**Methods**:
- `select(string|array $columns): self`
- `from(string $table): self`
- `where(...): self`
- `join(...): self`
- `orderBy(...): self`
- `limit(...): self`
- `build(): string`

#### 1.4. `QueryExecutor.php`
**Mục đích**: Execute queries và handle results
**Dòng code dự kiến**: ~200-250
**Responsibilities**:
- Prepare statements
- Execute queries
- Fetch results
- Handle errors
**Methods**:
- `execute(string $query, array $params = []): mixed`
- `fetchAll(string $query, array $params = []): array`
- `fetchOne(string $query, array $params = []): ?array`
- `fetchColumn(string $query, array $params = []): mixed`

#### 1.5. `TransactionManager.php`
**Mục đích**: Quản lý database transactions
**Dòng code dự kiến**: ~100
**Responsibilities**:
- Begin, commit, rollback transactions
- Nested transaction support
- Auto-rollback on errors
**Methods**:
- `begin(): void`
- `commit(): void`
- `rollback(): void`
- `inTransaction(): bool`

#### 1.6. `SchemaInspector.php`
**Mục đích**: Inspect database schema
**Dòng code dự kiến**: ~150
**Responsibilities**:
- Get table schema
- Get column information
- Check table existence
**Methods**:
- `getTableSchema(string $table): array`
- `getColumns(string $table): array`
- `tableExists(string $table): bool`

#### 1.7. `QueryLogger.php`
**Mục đích**: Log queries và errors
**Dòng code dự kiến**: ~100
**Responsibilities**:
- Log executed queries
- Log errors
- Performance tracking
**Methods**:
- `logQuery(string $query, array $params, float $duration): void`
- `logError(string $error, string $query): void`
- `getLastQuery(): string`

### 2. Helper Classes

#### 2.1. `ParameterBinder.php`
**Mục đích**: Bind parameters to prepared statements
**Dòng code dự kiến**: ~80
**Methods**:
- `bind(PDOStatement $stmt, array $params): void`
- `determineType(mixed $value): int`

#### 2.2. `ResultBuilder.php`
**Mục đích**: Build results from PDOStatement
**Dòng code dự kiến**: ~100
**Methods**:
- `buildArray(PDOStatement $stmt): array`
- `buildGenerator(PDOStatement $stmt): Generator`
- `buildObject(PDOStatement $stmt, string $class): object`

#### 2.3. `ExceptionHandler.php`
**Mục đích**: Handle và format database exceptions
**Dòng code dự kiến**: ~80
**Methods**:
- `handle(Exception $e, string $query): string`
- `formatError(array $errorInfo): string`

### 3. Value Objects

#### 3.1. `QueryOptions.php`
**Mục đích**: Encapsulate query options
**Properties**:
- `returnType`
- `fetchMode`
- `useGenerator`
- `pageLimit`

#### 3.2. `ConnectionConfig.php`
**Mục đích**: Encapsulate connection configuration
**Properties**:
- `type`, `host`, `port`, `dbname`
- `username`, `password`
- `charset`, `prefix`

## Kế Hoạch Thực Hiện

### Phase 1: Preparation (Không thay đổi code)
**Thời gian**: 30 phút
**Tasks**:
1. ✅ Tạo file kế hoạch này
2. Backup file PDODb.php hiện tại
3. Tạo test cases để verify backward compatibility
4. Tạo cấu trúc thư mục mới

### Phase 2: Extract Helper Classes (Ít rủi ro)
**Thời gian**: 1-2 giờ
**Tasks**:
1. Tạo `ParameterBinder.php`
2. Tạo `ResultBuilder.php`
3. Tạo `ExceptionHandler.php`
4. Update PDODb.php để sử dụng các helper classes
5. Test

### Phase 3: Extract Value Objects
**Thời gian**: 30 phút
**Tasks**:
1. Tạo `QueryOptions.php`
2. Tạo `ConnectionConfig.php`
3. Update PDODb.php constructor
4. Test

### Phase 4: Extract Core Components
**Thời gian**: 2-3 giờ
**Tasks**:
1. Tạo `ConnectionManager.php`
2. Tạo `TransactionManager.php`
3. Tạo `SchemaInspector.php`
4. Tạo `QueryLogger.php`
5. Update PDODb.php để delegate
6. Test từng component

### Phase 5: Extract Query Builder & Executor
**Thời gian**: 3-4 giờ
**Tasks**:
1. Tạo `QueryBuilder.php` (phần lớn nhất)
2. Tạo `QueryExecutor.php`
3. Update PDODb.php để sử dụng QueryBuilder
4. Test toàn bộ CRUD operations

### Phase 6: Refactor PDODb.php thành Facade
**Thời gian**: 1-2 giờ
**Tasks**:
1. Giữ lại public methods cho backward compatibility
2. Delegate tất cả operations to specialized classes
3. Add deprecation notices cho old methods
4. Update documentation

### Phase 7: Testing & Optimization
**Thời gian**: 2-3 giờ
**Tasks**:
1. Run full test suite
2. Test với real application
3. Performance benchmarking
4. Fix bugs nếu có

### Phase 8: Documentation & Cleanup
**Thời gian**: 1 giờ
**Tasks**:
1. Update PHPDoc
2. Tạo migration guide
3. Update README
4. Remove debug code

## Cấu Trúc Thư Mục Mới

```
src/Infrastructure/Database/
├── PDODb.php                    (Facade - 200-300 dòng)
├── Connection/
│   ├── ConnectionManager.php    (~150 dòng)
│   └── ConnectionConfig.php     (Value Object)
├── Query/
│   ├── QueryBuilder.php         (~500-600 dòng)
│   ├── QueryExecutor.php        (~200-250 dòng)
│   └── QueryOptions.php         (Value Object)
├── Transaction/
│   └── TransactionManager.php   (~100 dòng)
├── Schema/
│   └── SchemaInspector.php      (~150 dòng)
├── Logging/
│   └── QueryLogger.php          (~100 dòng)
└── Support/
    ├── ParameterBinder.php      (~80 dòng)
    ├── ResultBuilder.php        (~100 dòng)
    └── ExceptionHandler.php     (~80 dòng)
```

## Backward Compatibility Strategy

### Giữ nguyên Public API
```php
// Old code vẫn hoạt động
$db->where('id', 1)->get('users');
$db->rawQuery("SELECT * FROM users WHERE id = ?", [1]);
$db->insert('users', $data);
```

### Shim Layer
File `libraries/class/class.PDODb.php` vẫn extend từ class mới:
```php
<?php
use Tuezy\Infrastructure\Database\PDODb as NewPDODb;
class PDODb extends NewPDODb {}
```

## Rủi Ro & Giảm Thiểu

### Rủi Ro Cao
1. **Breaking backward compatibility**
   - Giảm thiểu: Giữ nguyên tất cả public methods
   - Test kỹ với code hiện tại

2. **Performance regression**
   - Giảm thiểu: Benchmark trước và sau
   - Optimize hot paths

### Rủi Ro Trung Bình
1. **Bugs trong quá trình refactor**
   - Giảm thiểu: Refactor từng phần nhỏ
   - Test sau mỗi phase

2. **Thời gian lâu hơn dự kiến**
   - Giảm thiểu: Có thể dừng sau bất kỳ phase nào
   - Mỗi phase đều có giá trị độc lập

## Lợi Ích Sau Refactor

### Ngắn Hạn
- Code dễ đọc, dễ maintain hơn
- Dễ debug hơn
- Dễ test hơn

### Dài Hạn
- Dễ thêm features mới
- Dễ optimize performance
- Dễ migrate sang database khác
- Team mới dễ onboard

## Quyết Định

**Có nên refactor ngay bây giờ?**

### NÊN refactor nếu:
- ✅ Có thời gian (10-15 giờ)
- ✅ Cần thêm features mới vào database layer
- ✅ Team sẽ maintain code này lâu dài
- ✅ Có test coverage tốt

### KHÔNG NÊN refactor nếu:
- ❌ Đang gấp deadline
- ❌ Code hiện tại đang hoạt động ổn định
- ❌ Không có test coverage
- ❌ Chỉ cần fix bug nhỏ

## Khuyến Nghị

**Cho dự án hiện tại**: 
- Website đang hoạt động sau khi fix namespace issue
- Nên **TẠM HOÃN** refactor toàn diện
- Chỉ nên refactor khi:
  1. Cần thêm feature mới phức tạp
  2. Có thời gian rảnh
  3. Đã có test coverage đầy đủ

**Refactor nhỏ có thể làm ngay**:
1. ✅ Add strict types: `declare(strict_types=1);`
2. ✅ Add type hints cho parameters và return types
3. ✅ Extract magic numbers thành constants
4. ✅ Improve PHPDoc comments
5. ✅ Remove unused code

---

**Tác giả**: Antigravity AI
**Ngày tạo**: 2025-12-11
**Phiên bản**: 1.0
