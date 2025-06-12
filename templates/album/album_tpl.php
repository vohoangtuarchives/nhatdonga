<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($news)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row row-photo">
                    <?php foreach ($news as $k => $v) { ?>
                        <div class="col-6 col-md-4 col-photo">
                            <div class="hover-scale overflow-hidden">
                                <a href="<?= $v['slug'.$lang] ?>">
                                    <?= $func->getImage(['class' => 'img-full', 'sizes' => '589x288x1', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo']]) ?>
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
            <div class="col-12">
                <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
            </div>
        </div>
    </div>
</div>