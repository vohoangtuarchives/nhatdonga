<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($product)) { ?>
                <?php if ($source == 'product') { ?>
                    <?php $titleProduct = '';
                    if ($idl != '') {
                        $titleProduct = $productList['name' . $lang];
                    } elseif ($idc != '') {
                        $titleProduct = $productCat['name' . $lang];
                    } elseif ($idi != '') {
                        $titleProduct = $productItem['name' . $lang];
                    } elseif ($ids != '') {
                        $titleProduct = $productSub['name' . $lang];
                    } elseif ($idb != '') {
                        $titleProduct = $productBrand['name' . $lang];
                    } else {
                        $titleProduct = $titleMain;
                    } ?>
                    <?= $custom->titleContainer($titleProduct) ?>
                <?php } else { ?>
                    <?= $custom->titleContainer($titleMain) ?>
                <?php } ?>
                <div class="row">
                    <div class="d-none d-lg-block col-12 col-lg-3 mb-3 mb-lg-0">
                        <?php include TEMPLATE . LAYOUT . "box-left-product.php"; ?>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="row row-product">
                            <?php foreach ($product as $k => $v) {
                                echo $custom->products($v);
                            } ?>
                        </div>
                        <div class="w-100">
                            <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning w-100" role="alert">
                    <strong><?= khongtimthayketqua ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
</div>