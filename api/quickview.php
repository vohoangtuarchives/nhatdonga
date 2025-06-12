<?php
include "config.php";
$w = 307;
$h = 265;
$r = 1;
$z = 2;
$thumbnail = $w * $z . 'x' . $h * $z . 'x' . $r;
$isWater = false;
if ($isWater == false) {
    $assets = THUMBS;
} else {
    $assets = WATERMARK . '/product';
}
$id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;
if ($id) {
    $rowDetail = $d->rawQueryOne("SELECT * from table_product where type='san-pham' and id=?", array($id));
    if ($rowDetail) { ?>
        <?php $rowDetailPhoto = $d->rawQuery("select photo from #_gallery where id_parent = ? and com='product' and type = ? and kind='man' and val = ? and find_in_set('hienthi',status) order by numb,id desc", array($rowDetail['id'], 'san-pham', 'san-pham')); ?>
        <div class="modal fade" id="Modal-Quickview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-quickiew modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-bold text-main"><?= $rowDetail['name' . $lang] ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="flex-box">
                            <div class="left-pro-detail">
                                <a id="Zoom-1" class="MagicZoom" data-options="zoomMode: off; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;" href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" title="<?= $rowDetail['name' . $lang] ?>">
                                    <?= $func->getImage(['isLazy' => false, 'sizes' => $thumbnail, 'isWatermark' => $isWater, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetail['photo'], 'alt' => $rowDetail['name' . $lang]]) ?>
                                </a>
                                <?php if ($rowDetailPhoto) {
                                    if (count($rowDetailPhoto) > 0) { ?>
                                        <div class="gallery-thumb-pro">
                                            <div class="owl-page owl-carousel owl-theme owl-pro-detail" data-xsm-items="5:10" data-sm-items="5:10" data-md-items="5:10" data-lg-items="5:10" data-xlg-items="5:10" data-nav="1" data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>" data-navcontainer=".control-pro-detail">
                                                <div>
                                                    <a class="thumb-pro-detail" data-zoom-id="Zoom-1" href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" title="<?= $rowDetail['name' . $lang] ?>">
                                                        <?= $func->getImage(['class' => 'img-full', 'isLazy' => false, 'sizes' => $thumbnail, 'isWatermark' => $isWater, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetail['photo'], 'alt' => $rowDetail['name' . $lang]]) ?>
                                                    </a>
                                                </div>
                                                <?php foreach ($rowDetailPhoto as $v) { ?>
                                                    <div>
                                                        <a class="thumb-pro-detail" data-zoom-id="Zoom-1" href="<?= ASSET . $assets ?>/<?= $thumbnail ?>/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" title="<?= $rowDetail['name' . $lang] ?>">
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
                            <div class="right-pro-detail">
                                <p class="title-pro-detail mb-2"><?= $rowDetail['name' . $lang] ?></p>

                                <div class="desc-pro-detail"><?= (!empty($rowDetail['desc' . $lang])) ? htmlspecialchars_decode($rowDetail['desc' . $lang]) : '' ?></div>
                                <ul class="attr-pro-detail">
                                    <?php if (!empty($rowDetail['code'])) { ?>
                                        <li class="w-clear">
                                            <label class="attr-label-pro-detail"><?= masp ?>:</label>
                                            <div class="attr-content-pro-detail"><?= $rowDetail['code'] ?></div>
                                        </li>
                                    <?php } ?>

                                    <li class="w-clear">
                                        <label class="attr-label-pro-detail"><?= gia ?>:</label>
                                        <div class="attr-content-pro-detail append-price">
                                            <?php if ($rowDetail['sale_price']) { ?>
                                                <span class="price-new-pro-detail"><?= $func->formatMoney($rowDetail['sale_price']) ?></span>
                                                <span class="price-old-pro-detail"><?= $func->formatMoney($rowDetail['regular_price']) ?></span>
                                                <span class="discount-pro-detail">(- <?= $rowDetail['discount'] ?>%)</span>
                                            <?php } else { ?>
                                                <span class="price-new-pro-detail"><?= ($rowDetail['regular_price']) ? $func->formatMoney($rowDetail['regular_price']) : lienhe ?></span>
                                            <?php } ?>
                                        </div>
                                    </li>
                                    <?php if ($config['custom']['cart'] == true) { ?>
                                        <?php if (!empty($rowColor)) { ?>
                                            <li class="color-block-pro-detail w-clear">
                                                <label class="attr-label-pro-detail d-block"><?= mausac ?>:</label>
                                                <div class="attr-content-pro-detail d-block">
                                                    <?php foreach ($rowColor as $k => $v) { ?>
                                                        <?php if ($v['type_show'] == 1) { ?>
                                                            <label for="color-pro-detail-<?= $v['id'] ?>" class="color-pro-detail text-decoration-none" data-idproduct="<?= $rowDetail['id'] ?>" style="background-image: url(<?= UPLOAD_COLOR_L . $v['photo'] ?>)">
                                                                <input type="radio" value="<?= $v['id'] ?>" id="color-pro-detail-<?= $v['id'] ?>" name="color-pro-detail">
                                                            </label>
                                                        <?php } else { ?>
                                                            <label for="color-pro-detail-<?= $v['id'] ?>" class="color-pro-detail text-decoration-none" data-idproduct="<?= $rowDetail['id'] ?>" style="background-color: #<?= $v['color'] ?>">
                                                                <input type="radio" value="<?= $v['id'] ?>" id="color-pro-detail-<?= $v['id'] ?>" name="color-pro-detail">
                                                            </label>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php if (!empty($rowSize)) { ?>
                                            <li class="size-block-pro-detail w-clear">
                                                <label class="attr-label-pro-detail d-block"><?= kichthuoc ?>:</label>
                                                <div class="attr-content-pro-detail d-block">
                                                    <?php foreach ($rowSize as $k => $v) { ?>
                                                        <label for="size-pro-detail-<?= $v['id'] ?>" class="size-pro-detail text-decoration-none" data-idproduct="<?= $rowDetail['id'] ?>">
                                                            <input type="radio" value="<?= $v['id'] ?>" id="size-pro-detail-<?= $v['id'] ?>" name="size-pro-detail">
                                                            <?= $v['name' . $lang] ?>
                                                        </label>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <li class="w-clear">
                                            <label class="attr-label-pro-detail d-block"><?= soluong ?>:</label>
                                            <div class="attr-content-pro-detail d-block">
                                                <div class="quantity-pro-detail">
                                                    <span class="quantity-minus-pro-detail">-</span>
                                                    <input type="number" class="qty-pro" min="1" value="1" readonly />
                                                    <span class="quantity-plus-pro-detail">+</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="w-clear">
                                            <div class="cart-pro-detail">
                                                <a class="btn btn-success addcart mr-2" data-id="<?= $rowDetail['id'] ?>" data-action="addnow">
                                                    <i class="fas fa-cart-plus mr-1"></i>
                                                    <span>Thêm vào giỏ hàng</span>
                                                </a>
                                                <a class="btn btn-dark addcart" data-id="<?= $rowDetail['id'] ?>" data-action="buynow">
                                                    <i class="fas fa-shopping-bag mr-1"></i>
                                                    <span>Mua ngay</span>
                                                </a>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php }
}
?>