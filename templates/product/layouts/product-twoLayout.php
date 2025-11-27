<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
        <div class="product-sidebar">
            <!-- Mobile sidebar toggle -->
            <button class="btn btn-outline-primary d-lg-none w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#productSidebar" aria-expanded="false" aria-controls="productSidebar">
                <i class="fas fa-filter me-2"></i> Bộ lọc & Danh mục
            </button>

            <div class="collapse d-lg-block" id="productSidebar">
                <!-- Categories -->
                <?php if (!empty($categoriesTree)): ?>
                    <div class="sidebar-widget mb-4">
                        <h4 class="sidebar-widget-title">
                            <i class="fas fa-list me-2"></i>
                            Danh mục sản phẩm
                        </h4>
                        <div class="sidebar-categories">
                            <ul class="category-list">
                                <li class="category-item <?= empty($idl) && empty($idc) ? 'active' : '' ?>">
                                    <a href="/san-pham" class="category-link">
                                        <i class="fas fa-th me-2"></i>
                                        Tất cả sản phẩm
                                    </a>
                                </li>
                                <?php foreach ($categoriesTree as $treeItem):
                                    $list = $treeItem['list'];
                                    $cats = $treeItem['cats'];
                                    $isListActive = ($idl == $list['id']);
                                    ?>
                                    <li class="category-item category-parent <?= $isListActive ? 'active' : '' ?>">
                                        <a href="<?= $configBase . $list['slug' . $lang] ?>" class="category-link">
                                            <i class="fas fa-folder me-2"></i>
                                            <?= htmlspecialchars($list['name' . $lang]) ?>
                                        </a>
                                        <?php if (!empty($cats)): ?>
                                            <button class="category-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#cat-<?= $list['id'] ?>" aria-expanded="<?= $isListActive ? 'true' : 'false' ?>">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <ul class="category-sub collapse <?= $isListActive ? 'show' : '' ?>" id="cat-<?= $list['id'] ?>">
                                                <?php foreach ($cats as $cat):
                                                    $isCatActive = ($idc == $cat['id']);
                                                    ?>
                                                    <li class="category-sub-item <?= $isCatActive ? 'active' : '' ?>">
                                                        <a href="<?= $configBase . $cat['slug' . $lang] ?>" class="category-sub-link">
                                                            <i class="fas fa-folder-open me-2"></i>
                                                            <?= htmlspecialchars($cat['name' . $lang]) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="sidebar-widget mb-4">
                    <h4 class="sidebar-widget-title">
                        <i class="fas fa-filter me-2"></i>
                        Bộ lọc
                    </h4>

                    <form method="GET" action="" id="productFiltersForm" class="product-filters">
                        <!-- Status Filters -->
                        <div class="filter-group mb-3">
                            <h5 class="filter-group-title">Trạng thái</h5>
                            <div class="filter-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status_filter[]" value="noibat" id="filter-noibat" <?= (!empty($_GET['status_filter']) && in_array('noibat', (array)$_GET['status_filter'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-noibat">
                                        <i class="fas fa-star text-warning me-1"></i> Nổi bật
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status_filter[]" value="moi" id="filter-moi" <?= (!empty($_GET['status_filter']) && in_array('moi', (array)$_GET['status_filter'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-moi">
                                        <i class="fas fa-sparkles text-info me-1"></i> Mới
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_discount" value="1" id="filter-discount" <?= (!empty($_GET['has_discount']) && $_GET['has_discount'] == '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-discount">
                                        <i class="fas fa-tag text-danger me-1"></i> Có khuyến mãi
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Filters -->
                        <?php if (!empty($brands)): ?>
                            <div class="filter-group mb-3">
                                <h5 class="filter-group-title">Thương hiệu</h5>
                                <div class="filter-options">
                                    <?php foreach ($brands as $brand): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="brand" value="<?= $brand['id'] ?>" id="brand-<?= $brand['id'] ?>" <?= (!empty($_GET['brand']) && $_GET['brand'] == $brand['id']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="brand-<?= $brand['id'] ?>">
                                                <?= htmlspecialchars($brand['name' . $lang]) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="brand" value="" id="brand-all" <?= empty($_GET['brand']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="brand-all">
                                            Tất cả thương hiệu
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Price Filter -->
                        <div class="filter-group mb-3">
                            <h5 class="filter-group-title">Khoảng giá</h5>
                            <div class="price-filter">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="price_min" placeholder="Từ" value="<?= !empty($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>" min="0" step="1000">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="price_max" placeholder="Đến" value="<?= !empty($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>" min="0" step="1000">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Nhập giá (VNĐ)</small>
                            </div>
                        </div>

                        <!-- Preserve other GET params -->
                        <?php if (!empty($_GET['idl'])): ?>
                            <input type="hidden" name="idl" value="<?= (int)$_GET['idl'] ?>">
                        <?php endif; ?>
                        <?php if (!empty($_GET['idc'])): ?>
                            <input type="hidden" name="idc" value="<?= (int)$_GET['idc'] ?>">
                        <?php endif; ?>
                        <?php if (!empty($_GET['keyword'])): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($_GET['keyword']) ?>">
                        <?php endif; ?>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-search me-2"></i> Áp dụng
                            </button>
                            <a href="/san-pham" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-redo me-2"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="col-lg-9 col-md-12">
        <?php
        // Sử dụng $total từ controller thay vì count array
        $totalProducts = $total ?? count($products);
        ?>

        <?php if ($totalProducts > 0): ?>
            <div class="product-listing-header mb-4">
                <!-- Active Filters -->
                <?php
                $activeFilters = [];
                if (!empty($_GET['status_filter'])) {
                    foreach ((array)$_GET['status_filter'] as $status) {
                        $statusNames = ['noibat' => 'Nổi bật', 'moi' => 'Mới'];
                        $activeFilters[] = [
                            'key' => 'status_filter[]',
                            'value' => $status,
                            'label' => $statusNames[$status] ?? $status
                        ];
                    }
                }
                if (!empty($_GET['has_discount']) && $_GET['has_discount'] == '1') {
                    $activeFilters[] = ['key' => 'has_discount', 'value' => '1', 'label' => 'Có khuyến mãi'];
                }
                if (!empty($_GET['brand'])) {
                    $brandName = '';
                    foreach ($brands ?? [] as $b) {
                        if ($b['id'] == $_GET['brand']) {
                            $brandName = $b['name' . $lang];
                            break;
                        }
                    }
                    $activeFilters[] = ['key' => 'brand', 'value' => $_GET['brand'], 'label' => 'Thương hiệu: ' . $brandName];
                }
                if (!empty($_GET['price_min']) || !empty($_GET['price_max'])) {
                    $priceLabel = 'Giá: ';
                    if (!empty($_GET['price_min'])) $priceLabel .= number_format($_GET['price_min'], 0, ',', '.') . ' đ';
                    $priceLabel .= ' - ';
                    if (!empty($_GET['price_max'])) $priceLabel .= number_format($_GET['price_max'], 0, ',', '.') . ' đ';
                    $activeFilters[] = ['key' => 'price', 'value' => '1', 'label' => $priceLabel];
                }
                ?>
                <?php if (!empty($activeFilters)): ?>
                    <div class="active-filters mb-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted small"><i class="fas fa-filter me-1"></i> Bộ lọc đang áp dụng:</span>
                            <?php foreach ($activeFilters as $filter): ?>
                                <span class="badge bg-primary">
                                            <?= htmlspecialchars($filter['label']) ?>
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('<?= htmlspecialchars($filter['key'], ENT_QUOTES) ?>', '<?= htmlspecialchars($filter['value'], ENT_QUOTES) ?>')" aria-label="Xóa"></button>
                                        </span>
                            <?php endforeach; ?>
                            <a href="/san-pham" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Xóa tất cả
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row align-items-center">
                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <p class="mb-0 text-muted">
                            <i class="fas fa-box me-2"></i>
                            Hiển thị <strong><?= count($products) ?></strong> / <strong><?= $totalProducts ?></strong> sản phẩm
                        </p>
                    </div>
                    <div class="col-md-8 col-12">
                        <div class="row g-2">
                            <div class="col-md-4 col-6">
                                <label class="form-label small mb-1">Sắp xếp:</label>
                                <select class="form-select form-select-sm" name="sort" id="productSort" onchange="updateProductSort()">
                                    <option value="default" <?= (empty($_GET['sort']) || $_GET['sort'] == 'default') ? 'selected' : '' ?>>Mặc định</option>
                                    <option value="price_asc" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá: Tăng dần</option>
                                    <option value="price_desc" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá: Giảm dần</option>
                                    <option value="name_asc" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên: A-Z</option>
                                    <option value="name_desc" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>Tên: Z-A</option>
                                    <option value="newest" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="oldest" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : '' ?>>Cũ nhất</option>
                                    <option value="popular" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'popular') ? 'selected' : '' ?>>Xem nhiều nhất</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small mb-1">Hiển thị:</label>
                                <select class="form-select form-select-sm" name="per_page" id="productPerPage" onchange="updateProductPerPage()">
                                    <option value="12" <?= (empty($_GET['per_page']) || $_GET['per_page'] == '12') ? 'selected' : '' ?>>12 sản phẩm</option>
                                    <option value="24" <?= (!empty($_GET['per_page']) && $_GET['per_page'] == '24') ? 'selected' : '' ?>>24 sản phẩm</option>
                                    <option value="48" <?= (!empty($_GET['per_page']) && $_GET['per_page'] == '48') ? 'selected' : '' ?>>48 sản phẩm</option>
                                    <option value="999" <?= (!empty($_GET['per_page']) && $_GET['per_page'] == '999') ? 'selected' : '' ?>>Tất cả</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label small mb-1">Chế độ xem:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="view_mode" id="viewGrid" value="grid" checked onchange="updateViewMode('grid')">
                                    <label class="btn btn-outline-primary btn-sm" for="viewGrid" title="Lưới">
                                        <i class="fas fa-th"></i>
                                    </label>
                                    <input type="radio" class="btn-check" name="view_mode" id="viewList" value="list" onchange="updateViewMode('list')">
                                    <label class="btn btn-outline-primary btn-sm" for="viewList" title="Danh sách">
                                        <i class="fas fa-list"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-3" id="productGrid">
            <?php foreach ($products as $k => $v): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 product-item">
                    <div class="product-card-isp">
                        <div class="product-image-isp">
                            <a href="<?= $configBase . $v['slug' . $lang] ?>">
                                <?php
                                $image = $func->getImage([
                                    'sizes' => '400x400x2',
                                    'isWatermark' => false,
                                    'prefix' => 'product',
                                    'upload' => UPLOAD_PRODUCT_L,
                                    'image' => $v['photo'],
                                    'alt' => $v['name' . $lang]
                                ]);
                                echo $image;
                                ?>
                            </a>
                            <?php if (!empty($v['discount']) && $v['discount'] > 0): ?>
                                <div class="discount-badge-isp">
                                    <span>-<?= $v['discount'] ?>%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info-isp">
                            <h3 class="product-name-isp">
                                <a href="<?= $configBase . $v['slug' . $lang] ?>">
                                    <?= htmlspecialchars($v['name' . $lang]) ?>
                                </a>
                            </h3>
                            <div class="product-price-isp">
                                <?php if (!empty($v['sale_price']) && $v['sale_price'] > 0): ?>
                                    <span class="price-new"><?= number_format($v['sale_price'], 0, ',', '.') ?> đ</span>
                                    <?php if (!empty($v['regular_price']) && $v['regular_price'] > $v['sale_price']): ?>
                                        <span class="price-old"><?= number_format($v['regular_price'], 0, ',', '.') ?> đ</span>
                                    <?php endif; ?>
                                <?php elseif (!empty($v['regular_price']) && $v['regular_price'] > 0): ?>
                                    <span class="price-new"><?= number_format($v['regular_price'], 0, ',', '.') ?> đ</span>
                                <?php else: ?>
                                    <span class="price-contact">Liên hệ</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions d-flex gap-2">
                                <a href="<?= $configBase . $v['slug' . $lang] ?>" class="btn-buy-isp flex-grow-1">
                                    MUA NGAY
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-sm quick-view-btn" data-product-id="<?= $v['id'] ?>" title="Xem nhanh">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- List View (hidden by default) -->
        <div class="product-list-view d-none" id="productList">
            <?php foreach ($products as $k => $v): ?>
                <div class="product-list-item mb-3">
                    <div class="row g-3">
                        <div class="col-md-3 col-12">
                            <div class="product-list-image">
                                <a href="<?= $configBase . $v['slug' . $lang] ?>">
                                    <?php
                                    // Kiểm tra và sử dụng ảnh sản phẩm
                                    $productPhoto = !empty($v['photo']) ? $v['photo'] : '';
                                    $productName = !empty($v['name' . $lang]) ? $v['name' . $lang] : 'Sản phẩm';
                                    
                                    $image = $func->getImage([
                                        'sizes' => '300x300x2',
                                        'isWatermark' => false,
                                        'prefix' => 'product',
                                        'upload' => UPLOAD_PRODUCT_L,
                                        'image' => $productPhoto,
                                        'alt' => $productName,
                                        'class' => 'img-fluid w-100'
                                    ]);
                                    echo $image;
                                    ?>
                                </a>
                                <?php if (!empty($v['discount']) && $v['discount'] > 0): ?>
                                    <div class="discount-badge-isp">
                                        <span>-<?= $v['discount'] ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-9 col-12">
                            <div class="product-list-info">
                                <h3 class="product-name-isp mb-2">
                                    <a href="<?= $configBase . $v['slug' . $lang] ?>">
                                        <?= htmlspecialchars($v['name' . $lang]) ?>
                                    </a>
                                </h3>
                                <div class="product-price-isp mb-3">
                                    <?php if (!empty($v['sale_price']) && $v['sale_price'] > 0): ?>
                                        <span class="price-new fs-5"><?= number_format($v['sale_price'], 0, ',', '.') ?> đ</span>
                                        <?php if (!empty($v['regular_price']) && $v['regular_price'] > $v['sale_price']): ?>
                                            <span class="price-old ms-2"><?= number_format($v['regular_price'], 0, ',', '.') ?> đ</span>
                                        <?php endif; ?>
                                    <?php elseif (!empty($v['regular_price']) && $v['regular_price'] > 0): ?>
                                        <span class="price-new fs-5"><?= number_format($v['regular_price'], 0, ',', '.') ?> đ</span>
                                    <?php else: ?>
                                        <span class="price-contact fs-5">Liên hệ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions d-flex gap-2">
                                    <a href="<?= $configBase . $v['slug' . $lang] ?>" class="btn-buy-isp flex-grow-1">
                                        MUA NGAY
                                    </a>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-view-btn" data-product-id="<?= $v['id'] ?>" title="Xem nhanh">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($paging)): ?>
            <div class="w-100 mt-5">
                <div class="pagination-home w-100 text-center"><?= $paging ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>