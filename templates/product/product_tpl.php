<div class="section-main section-product-listing">

    <div class="wrapper">

        <div class="content-main">

            <?php if (!empty($products)) { ?>

                <?php 
                // Xác định title
                $titleProduct = '';
                if ($source == 'product') {
                    if ($idl != '') {
                        $titleProduct = $productList['name' . $lang] ?? '';
                    } elseif ($idc != '') {
                        $titleProduct = $productCat['name' . $lang] ?? '';
                    } elseif ($idi != '') {
                        $titleProduct = $productItem['name' . $lang] ?? '';
                    } elseif ($ids != '') {
                        $titleProduct = $productSub['name' . $lang] ?? '';
                    } elseif ($idb != '') {
                        $titleProduct = $productBrand['name' . $lang] ?? '';
                    } else {
                        $titleProduct = $titleMain ?? '';
                    }
                } else {
                    $titleProduct = $titleMain ?? '';
                }
                ?>

                <?= $custom->titleContainer($titleProduct) ?>

                <!-- Breadcrumbs -->
                <?php if (!empty($breadcrumbs)): ?>
                <div class="breadcrumbs-container mb-3">
                    <?= $breadcrumbs ?>
                </div>
                <?php endif; ?>

                <div class="product-listing-container">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-lg-3 col-md-12 mb-4 mb-lg-0 d-none">
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
                        <div class="col-lg-12 col-md-12">
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
                                                            'sizes' => '400x400x1',
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
                </div>

            <?php } else { ?>

                <div class="empty-products-container text-center py-5">
                    <div class="empty-products-icon mb-4">
                        <i class="fas fa-box-open fa-4x text-muted"></i>
                    </div>
                    <h3 class="mb-3"><?= khongtimthayketqua ?></h3>
                    <p class="text-muted mb-4">
                        Chúng tôi không tìm thấy sản phẩm nào phù hợp với yêu cầu của bạn.<br>
                        Vui lòng thử lại với bộ lọc khác hoặc quay lại sau.
                    </p>
                    <a href="/san-pham" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Xem tất cả sản phẩm
                    </a>
                </div>

            <?php } ?>

        </div>

    </div>

</div>

<script>
// Product listing JavaScript - Global functions for inline handlers
window.updateProductSort = function() {
    const sort = document.getElementById('productSort').value;
    const url = new URL(window.location.href);
    if (sort === 'default') {
        url.searchParams.delete('sort');
    } else {
        url.searchParams.set('sort', sort);
    }
    url.searchParams.delete('p'); // Reset to page 1
    window.location.href = url.toString();
};

window.updateProductPerPage = function() {
    const perPage = document.getElementById('productPerPage').value;
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('p'); // Reset to page 1
    window.location.href = url.toString();
};

window.updateViewMode = function(mode) {
    localStorage.setItem('productViewMode', mode);
    const gridView = document.getElementById('productGrid');
    const listView = document.getElementById('productList');
    
    if (mode === 'list') {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
    } else {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
    }
};

window.removeFilter = function(key, value) {
    const url = new URL(window.location.href);
    if (key.includes('[]')) {
        // Handle array parameters
        const paramName = key.replace('[]', '');
        const values = url.searchParams.getAll(paramName);
        const newValues = values.filter(v => v !== value);
        url.searchParams.delete(paramName);
        newValues.forEach(v => url.searchParams.append(paramName, v));
    } else if (key === 'price') {
        url.searchParams.delete('price_min');
        url.searchParams.delete('price_max');
    } else {
        url.searchParams.delete(key);
    }
    url.searchParams.delete('p'); // Reset to page 1
    window.location.href = url.toString();
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load saved view mode from localStorage (default to grid)
    const savedViewMode = localStorage.getItem('productViewMode') || 'grid';
    
    // Set default to grid view
    const gridRadio = document.getElementById('viewGrid');
    const listRadio = document.getElementById('viewList');
    const gridView = document.getElementById('productGrid');
    const listView = document.getElementById('productList');
    
    if (savedViewMode === 'list') {
        if (listRadio) {
            listRadio.checked = true;
        }
        if (gridRadio) {
            gridRadio.checked = false;
        }
        if (gridView) gridView.classList.add('d-none');
        if (listView) listView.classList.remove('d-none');
    } else {
        // Default to grid view
        if (gridRadio) {
            gridRadio.checked = true;
        }
        if (listRadio) {
            listRadio.checked = false;
        }
        if (gridView) gridView.classList.remove('d-none');
        if (listView) listView.classList.add('d-none');
    }

    // Quick View buttons
    const quickViewBtns = document.querySelectorAll('.quick-view-btn');
    quickViewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            loadQuickView(productId);
        });
    });
});

function loadQuickView(productId) {
    // Show loading
    let modal = document.getElementById('quickViewModal');
    if (!modal) {
        // Create modal if not exists
        createQuickViewModal();
        modal = document.getElementById('quickViewModal');
    }
    const modalBody = document.querySelector('#quickViewModal .modal-body');
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Load product data
    fetch('<?= $configBase ?>api/quickview.php?id=' + productId)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Không thể tải thông tin sản phẩm. Vui lòng thử lại.</div>';
        });
}

function createQuickViewModal() {
    const modalHTML = `
        <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickViewModalLabel">Xem nhanh sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}
</script>