<?php
$aboutushome =  $d->rawQueryOne(
    "select name$lang,  desc$lang, date_created, slugvi, id, photo from #_static where type = ? and find_in_set('hienthi', status) order by id desc",
    array('gioi-thieu')
);
$tieuchi =
    $d->rawQuery(
        "select name$lang, photo from #_news where type = ? and find_in_set('hienthi', status) order by numb,id desc",
        array('tieu-chi')
    );
?>
<session class="aboutus-home">
    <div class="wrapper">
        <div class="aboutus-box">
            <div class="aboutus-left">
                <p class="scale-img">
                    <?= $func->getImage(['sizes' => '720x750x2', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $aboutushome['photo'], 'alt' => $aboutushome['name' . $lang]]) ?>
                </p>
            </div>
            <div class="aboutus-right">
                <h2 class="aboutus-title">
                    <?php echo $aboutushome['name' . $lang]; ?>
                </h2>
                <p class="aboutus-desc">
                    <?php echo $aboutushome['desc' . $lang]; ?>
                </p>
                <?php if (count($tieuchi)) { ?>
                    <div class="tieuchi-row row">
                        <?php foreach ($tieuchi as $k => $v) { ?>
                            <div class="tieuchi-items col-md-4 col-sm-4 col-xs-12 ">
                                <div class="image">
                                    <?= $func->getImage(['sizes' => '60x60x2', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name' . $lang]]) ?>
                                </div>
                                <h3 class="tieuchi-name"><?= $v['name' . $lang] ?></h3>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <a href="gioi-thieu" class="baoutus-link"><img src="assets/images/icon-right.png" alt="icon"> Tìm hiểu thêm</a>
            </div>
        </div>
    </div>
</session>