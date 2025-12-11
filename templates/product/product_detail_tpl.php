<?php
// Template cho trang chi tiết sản phẩm ga-dabaco với UI/UX hiện đại
// Đã bỏ phần giỏ hàng và đặt hàng

$w = 614;
$h = 530;
$r = 1;
$z = 1;
$thumbnail = $w * $z . 'x' . $h * $z . 'x' . $r;
$isWater = false;
$assets = $isWater ? WATERMARK . '/product' : THUMBS;
?>

<?php if (empty($rowDetail)) { ?>
    <div class="section-main">
        <div class="wrapper">
            <div class="container">
                <div class="alert alert-warning text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p class="mb-0"><?= sanphamkhongtontai ?></p>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>

<div class="section-main product-detail-modern">
    <div class="wrapper">
        <div class="container">
            <!-- Product Detail Section -->
            <div class="row product-detail-wrapper">
                <!-- Left: Product Images -->
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <div class="product-images-modern">
                        <!-- Main Image -->
                        <div class="main-image-wrapper mb-3">
                            <a id="Zoom-1" class="MagicZoom product-main-image" 
                               data-options="zoomMode: off; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;" 
                               href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" 
                               data-fancybox="product-gallery"
                               data-src="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>"
                               title="<?= $rowDetail['name' . $lang] ?>">
                                <?= $func->getImage(['isLazy' => false, 'sizes' => $thumbnail, 'isWatermark' => $isWater, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetail['photo'], 'alt' => $rowDetail['name' . $lang], 'class' => 'img-fluid']) ?>
                            </a>
                        </div>

                    <?php if ($rowDetailPhoto) {

                        if (count($rowDetailPhoto) > 0) {  ?>

                            <div class="gallery-thumb-pro">

                                <div class="owl-page owl-carousel owl-theme owl-pro-detail" data-xsm-items="5:10" data-sm-items="5:10" data-md-items="5:10" data-lg-items="5:10" data-xlg-items="5:10" data-nav="1" data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>" data-navcontainer=".control-pro-detail">

                                    <div>

                                        <a class="thumb-pro-detail mz-thumb active" 
                                           data-zoom-id="Zoom-1" 
                                           href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" 
                                           data-main-image="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>"
                                           data-fancybox="product-gallery"
                                           data-src="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>"
                                           title="<?= $rowDetail['name' . $lang] ?>">

                                            <?= $func->getImage(['class' => 'img-full', 'isLazy' => false, 'sizes' => $thumbnail, 'isWatermark' => $isWater, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetail['photo'], 'alt' => $rowDetail['name' . $lang]]) ?>

                                        </a>

                                    </div>

                                    <?php foreach ($rowDetailPhoto as $v) { ?>

                                        <div>

                                            <a class="thumb-pro-detail mz-thumb" 
                                               data-zoom-id="Zoom-1" 
                                               href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                               data-main-image="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>"
                                               data-fancybox="product-gallery"
                                               data-src="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>"
                                               title="<?= $rowDetail['name' . $lang] ?>">

                                                <?= $func->getImage(['class' => 'img-full', 'isLazy' => false, 'sizes' => $thumbnail, 'isWatermark' => $isWater, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $v['photo'], 'alt' => $rowDetail['name' . $lang]]) ?>

                                            </a>

                                        </div>

                                    <?php } ?>

                                </div>

                                <div class="control-pro-detail control-owl transition"></div>

                            </div>

                    <?php }

                    } ?>
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="col-12 col-lg-6">
                    <div class="product-info-modern">
                        <!-- Product Title -->
                        <h1 class="product-title-modern mb-3">
                            <?= !empty($rowDetail) ? ($rowDetail['name' . $lang] ?? '') : '' ?>
                        </h1>

                        <!-- Product Description -->
                        <?php if (!empty($rowDetail['desc' . $lang])) { ?>
                            <div class="product-desc-modern mb-4">
                                <?= htmlspecialchars_decode($rowDetail['desc' . $lang]) ?>
                            </div>
                        <?php } ?>

                        <!-- Product Attributes -->
                        <div class="product-attributes-modern mb-4">
                            <?php if (!empty($rowDetail['code'])) { ?>
                                <div class="attribute-item d-flex align-items-center mb-3">
                                    <span class="attribute-label">
                                        <i class="fas fa-barcode me-2"></i><?= masp ?>:
                                    </span>
                                    <span class="attribute-value fw-bold"><?= $rowDetail['code'] ?></span>
                                </div>
                            <?php } ?>

                            <!-- Price -->
                            <div class="attribute-item d-flex align-items-center mb-3">
                                <span class="attribute-label">
                                    <i class="fas fa-tag me-2"></i><?= gia ?>:
                                </span>
                                <div class="attribute-value price-wrapper">
                                    <?php if (!empty($rowDetail) && !empty($rowDetail['sale_price'])) { ?>
                                        <span class="price-new-modern"><?= $func->formatMoney($rowDetail['sale_price']) ?></span>
                                        <span class="price-old-modern"><?= $func->formatMoney($rowDetail['regular_price'] ?? 0) ?></span>
                                        <?php if (!empty($rowDetail['discount'])) { ?>
                                            <span class="discount-badge-modern">-<?= $rowDetail['discount'] ?>%</span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="price-new-modern">
                                            <?= (!empty($rowDetail) && !empty($rowDetail['regular_price'])) ? $func->formatMoney($rowDetail['regular_price']) : lienhe ?>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- View Count -->
                            <div class="attribute-item d-flex align-items-center mb-3">
                                <span class="attribute-label">
                                    <i class="fas fa-eye me-2"></i><?= luotxem ?>:
                                </span>
                                <span class="attribute-value"><?= number_format($rowDetail['view'] ?? 0) ?></span>
                            </div>

                            <!-- Tags -->
                            <?php if (!empty($rowTags)) { ?>
                                <div class="attribute-item mb-3">
                                    <span class="attribute-label d-block mb-2">
                                        <i class="fas fa-tags me-2"></i>Tags:
                                    </span>
                                    <div class="tags-wrapper-modern">
                                        <?php foreach ($rowTags as $v) { ?>
                                            <a class="tag-badge-modern" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                                <i class="fas fa-tag me-1"></i><?= $v['name' . $lang] ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Social Share -->
                            <div class="attribute-item share-attribute-item">
                                <?php include TEMPLATE . LAYOUT . "share-social.php"; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Content Tabs -->
            <div class="product-tabs-modern mt-5">
                <ul class="nav nav-tabs nav-tabs-modern" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="fas fa-info-circle me-2"></i><?= thongtinsanpham ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="comment-tab" data-bs-toggle="tab" data-bs-target="#comment" type="button" role="tab">
                            <i class="fas fa-comments me-2"></i><?= binhluan ?>
                        </button>
                    </li>
                </ul>
                <div class="tab-content tab-content-modern" id="productTabsContent">
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="product-content-modern p-4">
                            <?= htmlspecialchars_decode($rowDetail['content' . $lang]) ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="comment" role="tabpanel">
                        <div class="product-comments-modern p-4">
                            <div class="fb-comments" data-href="<?= $func->getCurrentPageURL() ?>" data-numposts="3" data-colorscheme="light" data-width="100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php if (!empty($relatedProducts)) { ?>
                <div class="related-products-modern mt-5">
                    <h3 class="section-title-modern mb-4">
                        <i class="fas fa-th-large me-2"></i><?= sanphamcungloai ?>
                    </h3>
                    <div class="row g-3 related-products-grid">
                        <?php foreach ($relatedProducts as $k => $v): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-6 product-item-related">
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
                                        <h4 class="product-name-isp">
                                            <a href="<?= $configBase . $v['slug' . $lang] ?>" title="<?= htmlspecialchars($v['name' . $lang]) ?>">
                                                <?= htmlspecialchars($v['name' . $lang]) ?>
                                            </a>
                                        </h4>
                                        <div class="product-price-isp">
                                            <?php if (!empty($v['sale_price'])): ?>
                                                <span class="price-new"><?= $func->formatMoney($v['sale_price']) ?></span>
                                                <?php if (!empty($v['regular_price']) && $v['regular_price'] > $v['sale_price']): ?>
                                                    <span class="price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                                                <?php endif; ?>
                                            <?php elseif (!empty($v['regular_price'])): ?>
                                                <span class="price-new"><?= $func->formatMoney($v['regular_price']) ?></span>
                                            <?php else: ?>
                                                <span class="price-contact"><?= lienhe ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php } ?>

<style>
/* Modern Product Detail Styles */
.product-detail-modern {
    padding: 2rem 0;
}

.product-detail-wrapper {
    margin-bottom: 3rem;
}

/* Product Images */
.product-images-modern .main-image-wrapper {
    position: relative;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.product-image-container {
    position: relative;
    padding-bottom: 75%;
    overflow: hidden;
}

.product-image-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-gallery {
    margin-top: 1rem;
}

.thumb-pro-detail-modern {
    display: block;
    border: 2px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
}

.thumb-pro-detail-modern:hover,
.thumb-pro-detail-modern.active {
    border-color: #dc3545;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
}

.thumb-pro-detail-modern img {
    width: 100%;
    height: auto;
    display: block;
}

/* Product Info */
.product-info-modern {
    padding: 1.5rem;
}

.product-title-modern {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.3;
}

.product-desc-modern {
    font-size: 1.1rem;
    color: #555;
    line-height: 1.7;
}

.product-attributes-modern {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.attribute-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.attribute-item:last-child {
    border-bottom: none;
}

.attribute-label {
    min-width: 150px;
    color: #6c757d;
    font-weight: 500;
}

.attribute-value {
    color: #2c3e50;
}

.price-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.price-new-modern {
    font-size: 1.75rem;
    font-weight: 700;
    color: #dc3545;
}

.price-old-modern {
    font-size: 1.25rem;
    color: #6c757d;
    text-decoration: line-through;
}

.discount-badge-modern {
    background: #dc3545;
    color: #fff;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.tags-wrapper-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    color: #495057;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.tag-badge-modern:hover {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
}

/* Product Tabs */
.product-tabs-modern {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.nav-tabs-modern {
    border-bottom: 2px solid #e9ecef;
    padding: 0 1.5rem;
    margin: 0;
}

.nav-tabs-modern .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    padding: 1rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs-modern .nav-link:hover {
    color: #dc3545;
    border-bottom-color: #dc3545;
}

.nav-tabs-modern .nav-link.active {
    color: #dc3545;
    background: transparent;
    border-bottom-color: #dc3545;
}

.tab-content-modern {
    min-height: 200px;
}

.product-content-modern {
    font-size: 1rem;
    line-height: 1.8;
    color: #495057;
}

.product-content-modern img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

/* Related Products */
.related-products-modern {
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 2px solid #e9ecef;
}

.section-title-modern {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.related-products-grid {
    margin-top: 1.5rem;
}

.product-item-related {
    margin-bottom: 1.5rem;
}

.product-card-isp {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card-isp:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.product-image-isp {
    position: relative;
    padding-bottom: 100%;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image-isp a {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.product-image-isp img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card-isp:hover .product-image-isp img {
    transform: scale(1.05);
}

.discount-badge-isp {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: #fff;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

.product-info-isp {
    padding: 1rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-name-isp {
    margin: 0 0 0.75rem 0;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.4;
    min-height: 2.8rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-name-isp a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-name-isp a:hover {
    color: #dc3545;
}

.product-price-isp {
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.product-price-isp .price-new {
    font-size: 1.125rem;
    font-weight: 700;
    color: #dc3545;
}

.product-price-isp .price-old {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
}

.product-price-isp .price-contact {
    font-size: 1rem;
    color: #6c757d;
    font-style: italic;
}

/* Share Social Modern */
.share-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.share-label {
    color: #6c757d;
    font-weight: 500;
    white-space: nowrap;
}

.social-plugin-modern {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.share-attribute-item {
    flex-direction: column;
    align-items: flex-start !important;
}

.share-attribute-item .share-modern {
    width: 100%;
}

/* Responsive */
@media (max-width: 991.98px) {
    .product-title-modern {
        font-size: 1.5rem;
    }
    
    .price-new-modern {
        font-size: 1.5rem;
    }
    
    .attribute-label {
        min-width: 120px;
    }
}

@media (max-width: 575.98px) {
    .product-info-modern {
        padding: 1rem;
    }
    
    .product-attributes-modern {
        padding: 1rem;
    }
    
    .product-title-modern {
        font-size: 1.25rem;
    }
    
    .attribute-item {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .attribute-label {
        min-width: auto;
        margin-bottom: 0.5rem;
    }
    
    .share-modern {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .related-products-grid .product-item-related {
        margin-bottom: 1rem;
    }
    
    .product-name-isp {
        font-size: 0.9rem;
        min-height: 2.4rem;
    }
    
    .product-price-isp .price-new {
        font-size: 1rem;
    }
}

/* Thumbnail Gallery Styles */
.gallery-thumb-pro {
    margin-top: 1rem;
}

.thumb-pro-detail {
    display: block !important;
    border: 2px solid #e9ecef;
    padding: 5px;
    border-radius: 5px;
    cursor: pointer;
    background-color: #ffffff;
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.thumb-pro-detail:hover {
    border-color: #dc3545;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
}

.thumb-pro-detail.active,
.thumb-pro-detail.mz-thumb-selected {
    border-color: #dc3545;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.thumb-pro-detail img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 3px;
}

.main-image-wrapper {
    position: relative;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.product-main-image {
    display: block;
    cursor: pointer;
}

.product-main-image img {
    width: 100%;
    height: auto;
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý click vào thumbnail để thay đổi ảnh chính
    const thumbnails = document.querySelectorAll('.thumb-pro-detail');
    const mainImageLink = document.getElementById('Zoom-1');
    const mainImage = mainImageLink ? mainImageLink.querySelector('img') : null;
    
    if (thumbnails.length > 0 && mainImageLink && mainImage) {
        thumbnails.forEach(function(thumb) {
            thumb.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Lấy URL ảnh từ data attribute
                const newImageUrl = this.getAttribute('data-main-image') || this.getAttribute('href');
                const newImageSrc = this.querySelector('img').src;
                
                if (newImageUrl) {
                    // Cập nhật href của ảnh chính
                    mainImageLink.setAttribute('href', newImageUrl);
                    mainImageLink.setAttribute('data-src', newImageUrl);
                    
                    // Cập nhật src của ảnh chính
                    if (mainImage) {
                        mainImage.src = newImageSrc;
                        mainImage.alt = this.getAttribute('title') || mainImage.alt;
                    }
                    
                    // Refresh MagicZoom
                    if (typeof MagicZoom !== 'undefined') {
                        MagicZoom.refresh('Zoom-1');
                    }
                    
                    // Cập nhật active class cho thumbnail
                    thumbnails.forEach(function(t) {
                        t.classList.remove('active', 'mz-thumb-selected');
                    });
                    this.classList.add('active', 'mz-thumb-selected');
                }
            });
        });
    }
    
    // Khởi tạo Fancybox cho gallery
    if (typeof jQuery !== 'undefined' && typeof jQuery.fancybox !== 'undefined') {
        jQuery('[data-fancybox="product-gallery"]').fancybox({
            buttons: [
                "zoom",
                "share",
                "slideShow",
                "fullScreen",
                "download",
                "thumbs",
                "close"
            ],
            loop: true,
            protect: true
        });
    }
});
</script>

