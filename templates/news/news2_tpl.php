<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($news)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row">
                    <div class="d-none d-lg-block col-12 col-lg-3" data-animation="animate__fadeInLeft">
                        <?php include TEMPLATE . LAYOUT . "box-left-news.php"; ?>
                    </div>
                    <div class="col-12 col-lg-9" data-animation="animate__fadeInRight">
                        <div class="row row-news">
                            <?php foreach ($news as $k => $v) {
                                echo $custom->news($v);
                            } ?>
                        </div>
                    </div>
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