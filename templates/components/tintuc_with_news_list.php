<?php
$newsnb = $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc limit 6", array('tin-tuc'));
$blogDesk = $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc limit 4", array('blog'));

if (!empty($newsnb)) { ?>
    <div class="block-thieves-news">
        <div class="container py-4">
            <div class="content-news-index">
                <div class="content-news">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="title-main">
                                        <div class="section-header-isp text-center">
                                            <h2 class="section-title-isp">TIN TỨC</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-12 col-lg-6">
                                    <?php if (!empty($newsnb[0]) && $newsnb[0][$sluglang] != "") { ?>
                                        <div class="main-news">
                                            <div class="news-img">
                                                <a href="<?= $newsnb[0][$sluglang] ?>" title="<?= $newsnb[0]['name' . $lang] ?>">
                                                    <img onerror="this.src='<?= THUMBS ?>/560x320x1/assets/images/noimage.png';" src="<?= THUMBS ?>/500x500x1/<?= UPLOAD_NEWS_L . $newsnb[0]['photo'] ?>" class="img-responsive" alt="<?= $newsnb[0]['name' . $lang] ?>">
                                                </a>
                                            </div>
                                            <div class="main-news__detail">
                                                <div class="name-news"><a href="<?= $newsnb[0][$sluglang] ?>" title="<?= $newsnb[0]['name' . $lang] ?>"><?= $newsnb[0]['name' . $lang] ?></a></div>
                                                <div class="desc-news text-split"><?= htmlspecialchars_decode($newsnb[0]['desc' . $lang]) ?></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <?php for ($i = 1; $i < count($newsnb); $i++) { ?>
                                        <?php if (!empty($newsnb[$i]) && $newsnb[$i][$sluglang] != "") { ?>
                                            <div class="item-news mb-4 clearfix item-news-desktop">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="_image">
                                                            <a href="<?= $newsnb[$i][$sluglang] ?>" title="<?= $newsnb[$i]['name' . $lang] ?>">
                                                                <img onerror="this.src='<?= THUMBS ?>/155x155x1/assets/images/noimage.png';" src="<?= THUMBS ?>/155x155x1/<?= UPLOAD_NEWS_L . $newsnb[$i]['photo'] ?>" class="img-responsive" alt="<?= $newsnb[$i]['name' . $lang] ?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-9 ps-1">
                                                        <div class="">
                                                            <div class="name-news" style="margin-top:0"><a href="<?= $newsnb[$i][$sluglang] ?>" title="<?= $newsnb[$i]['name' . $lang] ?>"><?= $newsnb[$i]['name' . $lang] ?></a></div>
                                                            <div class="desc-news text-split"><?= htmlspecialchars_decode($newsnb[$i]['desc' . $lang]) ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>