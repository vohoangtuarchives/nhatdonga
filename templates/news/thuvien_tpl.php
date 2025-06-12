<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($news)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="row row-news">
                    <?php foreach ($news as $k => $v) {
                        echo $custom->album($v);
                    } ?>
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