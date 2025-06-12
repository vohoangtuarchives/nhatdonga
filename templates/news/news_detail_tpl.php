<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <?= $custom->titleContainer($rowDetail['name' . $lang]) ?>
            <div class="time-main"><i class="fas fa-calendar-week"></i><span><?= ngaydang ?>: <?= date("d/m/Y h:i A", $rowDetail['date_created']) ?></span></div>
            <?php if (!empty($rowDetail['content' . $lang])) { ?>
                <div class="meta-toc">
                    <div class="box-readmore">
                        <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
                    </div>
                </div>
                <article class="noidung"><?= htmlspecialchars_decode($rowDetail['content' . $lang]) ?></article>
                <?php include TEMPLATE . LAYOUT . "share-social.php"; ?>
            <?php } else { ?>
                <div class="alert alert-warning w-100" role="alert">
                    <strong><?= noidungdangcapnhat ?></strong>
                </div>
            <?php } ?>
            <?php if (!empty($otherNewss)) { ?>
                <div class="share othernews mb-3">
                    <b><?= baivietkhac ?>:</b>
                    <ul class="list-news-other">
                        <?php foreach ($otherNewss as $k => $v) { ?>
                            <li><a class="text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?> - <?= date("d/m/Y", $v['date_created']) ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>