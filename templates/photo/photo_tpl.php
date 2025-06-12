<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($photos)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row row-photo">
                    <?php foreach ($photos as $k => $v) { ?>
                        <div class="col-6 col-md-4 col-photo">
                            <div class="hover-scale overflow-hidden " data-animation="animate__zoomIn">
                                <a href="<?= UPLOAD_PHOTO_L . $v['photo'] ?>" data-fancybox>
                                    <?= $func->getImage(['class' => 'img-full', 'sizes' => '414x300x1','upload'=>UPLOAD_PHOTO_L ,'image'=>$v['photo']]) ?>
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