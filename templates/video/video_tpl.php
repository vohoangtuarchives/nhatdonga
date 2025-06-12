<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($video)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row row-video">
                    <?php foreach ($video as $k => $v) { ?>
                        <div class="col-6 col-md-4 col-lg-3 col-xl-3 col-video " data-animation="animate__zoomIn">
                            <div class="video hover-scale" data-fancybox="video" data-src="<?= $v['link_video'] ?>">
                                <div class="video-image youtube-logo overflow-hidden">
                                    <?= $func->getImage(['class' => 'img-fluid', 'size-error' => '480x360x1', 'url' => $custom->getImgYoutube($v['link_video']), 'alt' => $v['name' . $lang]]) ?>
                                </div>
                                <h3 class="video-name text-split"><?= $v['name' . $lang] ?></h3>
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