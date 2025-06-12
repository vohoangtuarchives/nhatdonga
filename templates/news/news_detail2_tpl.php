<div class="section-main">
    <div class="wrapper">
        <div class="content-main">
            <div class="row">
                <div class="d-none d-lg-block col-12 col-lg-3 mb-3 mb-lg-0">
                    <?php include TEMPLATE . LAYOUT . "box-left-news.php"; ?>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="title-main"><span><?= $rowDetail['name' . $lang] ?></span></div>
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
                </div>
            </div>
            <?php if (!empty($news)) { ?>
                <div class="share othernews mb-3 d-none">
                    <b><?= baivietkhac ?>:</b>
                    <ul class="list-news-other">
                        <?php foreach ($news as $k => $v) { ?>
                            <li><a class="text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?> - <?= date("d/m/Y", $v['date_created']) ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<style>
    .box-list>h3 {
        font-size: 17px;
        padding: 5px 0;
        font-weight: bold;
        background: #cccccc3d;
        padding: 10px;
    }
    .box-list>h3 i{
        margin-right: 10px;
    }
    .list-category {
        margin: 0;
        border-top: 1px solid #ddd;
        border: 1px solid #ccc;
    }
    .list-category li{
        padding: 8px 10px;
        line-height: 1.5rem;
    }
    .list-category li + li{
        border-top: 1px solid #ccc;
    }
    .list-category li a{
        text-decoration: none;
    }