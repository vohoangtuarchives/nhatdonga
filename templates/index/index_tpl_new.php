<?php
/**
 * Template trang chủ mới - ISP Việt Nam
 * Thiết kế theo design Canva với database integration
 * 
 * Template chỉ nhận data từ HomeController, KHÔNG query database
 * 
 * Variables có sẵn từ controller:
 * - $featuredProducts: Sản phẩm nổi bật
 * - $productCategories: Danh mục sản phẩm
 * - $categoryProducts: Sản phẩm theo danh mục
 * - $certificates: Chứng nhận
 */
?>

<!-- Hero Section -->
<section class="hero-section-isp">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content-isp">
                    <h1 class="hero-title-isp">ISF Việt Nam</h1>
                    <p class="hero-subtitle-isp">Chuyên cung cấp nguyên liệu thức ăn chăn nuôi</p>
                    <p class="hero-desc-isp">
                        Men chăn nuôi, vi sinh, enzyme và nguyên liệu đạt tiêu chuẩn chất lượng cao
                    </p>
                    <div class="hero-contact-isp">
                        <div class="contact-item-isp">
                            <i class="fas fa-phone"></i>
                            <span>HOTLINE: <?= $optsetting['hotline'] ?? '0866.844.512' ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-product-isp">
                    <?php if (!empty($featuredProducts[0])): 
                        $heroProduct = $featuredProducts[0];
                    ?>
                        <div class="hero-product-image">
                            <img src="<?= $configBase . THUMBS . '/600x600x2/' . UPLOAD_PRODUCT_L . $heroProduct['photo'] ?>" 
                                 alt="<?= htmlspecialchars($heroProduct['name' . $lang]) ?>"
                                 onerror="this.src='<?= $configBase ?>thumbs/600x600x1/assets/images/noimage.png';">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sản phẩm nổi bật -->
<?php if (!empty($featuredProducts)): ?>
<section class="products-section-isp">
    <div class="container">
        <div class="section-header-isp text-center">
            <h2 class="section-title-isp">SẢN PHẨM NỔI BẬT</h2>
        </div>
        <?php if(!empty($featuredProducts)): ?>
        <div class="row g-3">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="product-card-isp">
                    <div class="product-image-isp">
                        <a href="<?= $configBase . $product['slugvi'] ?>">
                            <img src="<?= $configBase . THUMBS . '/400x400x2/' . UPLOAD_PRODUCT_L . $product['photo'] ?>" 
                                 alt="<?= htmlspecialchars($product['name' . $lang]) ?>"
                                 onerror="this.src='<?= $configBase ?>thumbs/400x400x1/assets/images/noimage.png';">
                        </a>
                    </div>
                    <div class="product-info-isp">
                        <h3 class="product-name-isp">
                            <a href="<?= $configBase . $product['slugvi'] ?>">
                                <?= htmlspecialchars($product['name' . $lang]) ?>
                            </a>
                        </h3>
                        <div class="product-price-isp">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] > 0): ?>
                                <?= number_format($product['sale_price'], 0, ',', '.') ?> đ
                            <?php elseif (!empty($product['regular_price']) && $product['regular_price'] > 0): ?>
                                <?= number_format($product['regular_price'], 0, ',', '.') ?> đ
                            <?php else: ?>
                                Liên hệ
                            <?php endif; ?>
                        </div>
                        <a href="<?= $configBase . $product['slugvi'] ?>" class="btn-buy-isp">
                            MUA NGAY
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Sản phẩm theo danh mục -->
<?php foreach ($categoryProducts as $catData): ?>
    <?php if (!empty($catData['products'])): ?>
<section class="category-section-isp">
    <div class="container">
        <div class="section-header-isp text-center">
            <h2 class="section-title-isp"><?= strtoupper($catData['info']['name' . $lang]) ?></h2>
        </div>
        
        <div class="row g-3">
            <?php foreach ($catData['products'] as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="product-card-isp">
                    <div class="product-image-isp">
                        <a href="<?= $configBase . $product['slugvi'] ?>">
                            <img src="<?= $configBase . THUMBS . '/400x400x2/' . UPLOAD_PRODUCT_L . $product['photo'] ?>" 
                                 alt="<?= htmlspecialchars($product['name' . $lang]) ?>"
                                 onerror="this.src='<?= $configBase ?>thumbs/400x400x1/assets/images/noimage.png';">
                        </a>
                    </div>
                    <div class="product-info-isp">
                        <h3 class="product-name-isp">
                            <a href="<?= $configBase . $product['slugvi'] ?>">
                                <?= htmlspecialchars($product['name' . $lang]) ?>
                            </a>
                        </h3>
                        <div class="product-price-isp">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] > 0): ?>
                                <?= number_format($product['sale_price'], 0, ',', '.') ?> đ
                            <?php elseif (!empty($product['regular_price']) && $product['regular_price'] > 0): ?>
                                <?= number_format($product['regular_price'], 0, ',', '.') ?> đ
                            <?php else: ?>
                                Liên hệ
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php if (!empty($catData['info']["photo"])): ?>
    <div class="w-100 category-image-isp">
        <div class="container">
            <div class="text-center py-3">
                <?= $func->getImage([
                        'isLazy' => false,
                        'sizes' => '1230x175x2',
                        'isWatermark' => false,
                        'prefix' => 'product',
                        'upload' => UPLOAD_PRODUCT_L,
                        'image' => $catData['info']['photo'],
                        'alt' => $catData['info']['name' . $lang],
                        'class' => 'img-fluid'
                ]) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>

<!-- Certificates Section -->
<?php if (!empty($certificates)): ?>
<section class="certificates-section-isp">
    <div class="container">
        <div class="section-header-isp text-center">
            <h2 class="section-title-isp">CHỨNG NHẬN</h2>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($certificates as $cert): ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="certificate-box-isp">
                    <?php if (!empty($cert['link'])): ?>
                        <a href="<?= $cert['link'] ?>" target="_blank">
                    <?php endif; ?>
                        <img src="<?= $configBase . THUMBS . '/200x200x1/' . UPLOAD_PHOTO_L . $cert['photo'] ?>" 
                             alt="<?= htmlspecialchars($cert['name' . $lang] ?? '') ?>"
                             onerror="this.src='<?= $configBase ?>thumbs/200x200x1/assets/images/noimage.png';">
                    <?php if (!empty($cert['link'])): ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
