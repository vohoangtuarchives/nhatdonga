<?php
/**
 * Quick View Template for Product
 * Hiển thị thông tin sản phẩm trong modal quick view
 */

if (empty($rowDetail)) {
    echo '<div class="alert alert-warning">' . sanphamkhongtontai . '</div>';
    return;
}

$lang = $_SESSION['lang'] ?? 'vi';
$configBase = $config['database']['url'] ?? '';
?>

<div class="quickview-product">
    <div class="row g-4">
        <!-- Product Image -->
        <div class="col-md-6">
            <div class="quickview-image">
                <a href="<?= $configBase . $rowDetail['slug' . $lang] ?>" title="<?= htmlspecialchars($rowDetail['name' . $lang]) ?>">
                    <?= $func->getImage([
                        'isLazy' => false,
                        'sizes' => $thumbnail,
                        'isWatermark' => $isWater,
                        'prefix' => 'product',
                        'upload' => UPLOAD_PRODUCT_L,
                        'image' => $rowDetail['photo'],
                        'alt' => $rowDetail['name' . $lang],
                        'class' => 'img-fluid w-100'
                    ]) ?>
                </a>
                <?php if (!empty($rowDetail['discount']) && $rowDetail['discount'] > 0): ?>
                <div class="discount-badge-isp position-absolute top-0 start-0 m-2">
                    <span>-<?= $rowDetail['discount'] ?>%</span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($rowDetailPhoto) && count($rowDetailPhoto) > 0): ?>
            <div class="quickview-gallery mt-3">
                <div class="row g-2">
                    <?php foreach (array_slice($rowDetailPhoto, 0, 4) as $photo): ?>
                    <div class="col-3">
                        <a href="<?= $configBase . $rowDetail['slug' . $lang] ?>">
                            <?= $func->getImage([
                                'isLazy' => false,
                                'sizes' => '100x100x1',
                                'isWatermark' => false,
                                'prefix' => 'product',
                                'upload' => UPLOAD_PRODUCT_L,
                                'image' => $photo['photo'],
                                'alt' => $rowDetail['name' . $lang],
                                'class' => 'img-fluid w-100'
                            ]) ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <div class="quickview-info">
                <h3 class="quickview-title mb-3">
                    <a href="<?= $configBase . $rowDetail['slug' . $lang] ?>" class="text-decoration-none">
                        <?= htmlspecialchars($rowDetail['name' . $lang]) ?>
                    </a>
                </h3>

                <!-- Price -->
                <div class="quickview-price mb-3">
                    <?php if (!empty($rowDetail['sale_price']) && $rowDetail['sale_price'] > 0): ?>
                        <span class="price-new fs-4 fw-bold text-danger">
                            <?= number_format($rowDetail['sale_price'], 0, ',', '.') ?> đ
                        </span>
                        <?php if (!empty($rowDetail['regular_price']) && $rowDetail['regular_price'] > $rowDetail['sale_price']): ?>
                            <span class="price-old ms-2 text-muted text-decoration-line-through">
                                <?= number_format($rowDetail['regular_price'], 0, ',', '.') ?> đ
                            </span>
                        <?php endif; ?>
                    <?php elseif (!empty($rowDetail['regular_price']) && $rowDetail['regular_price'] > 0): ?>
                        <span class="price-new fs-4 fw-bold">
                            <?= number_format($rowDetail['regular_price'], 0, ',', '.') ?> đ
                        </span>
                    <?php else: ?>
                        <span class="price-contact fs-4 fw-bold">Liên hệ</span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <?php if (!empty($rowDetail['desc' . $lang])): ?>
                <div class="quickview-desc mb-3">
                    <p class="text-muted">
                        <?= htmlspecialchars_decode($rowDetail['desc' . $lang]) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Colors -->
                <?php if (!empty($rowColor)): ?>
                <div class="quickview-colors mb-3">
                    <label class="form-label fw-bold">Màu sắc:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($rowColor as $color): ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($color['color'] ?? '') ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Sizes -->
                <?php if (!empty($rowSize)): ?>
                <div class="quickview-sizes mb-3">
                    <label class="form-label fw-bold">Kích thước:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($rowSize as $size): ?>
                        <span class="badge bg-info"><?= htmlspecialchars($size['name' . $lang] ?? '') ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="quickview-actions mt-4">
                    <div class="d-flex gap-2">
                        <a href="<?= $configBase . $rowDetail['slug' . $lang] ?>" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-shopping-cart me-2"></i> MUA NGAY
                        </a>
                        <a href="<?= $configBase . $rowDetail['slug' . $lang] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-info-circle me-2"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

