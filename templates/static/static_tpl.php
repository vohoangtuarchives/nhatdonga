<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?php if (!empty($static)) { ?>
                <?= $custom->titleContainer($titleMain) ?>
                <div class="meta-toc">
                    <div class="box-readmore">
                        <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
                    </div>
                </div>
                <article class="noidung"><?= (!empty($static['content' . $lang])) ? htmlspecialchars_decode($static['content' . $lang]) : '' ?></article>
                <?php include TEMPLATE . LAYOUT . "share-social.php"; ?>
            <?php } else { ?>
                <div class="alert alert-warning w-100" role="alert">
                    <strong><?= dangcapnhatdulieu ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
</div>