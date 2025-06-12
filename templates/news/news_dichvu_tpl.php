<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($news)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row row-news">
                    <?php foreach ($news as $k => $v) { ?>
                        <div class="col-md-3">
                        <div class="dichvu-items news-item">
                            <a class="box-product text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                <p class="pic-product scale-img">
                                    <?=$func->getImage(['sizes' => '280x280x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                </p>
                                <h3 class="news-name text-center text-black p-3"><?=$v['name'.$lang]?></h3>
                            </a>
                        </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning w-100" role="alert">
                    <strong><?= khongtimthayketqua ?></strong>
                </div>
            <?php } ?>
            <div class="w-100">
                <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
            </div>
        </div>
    </div>
</div>