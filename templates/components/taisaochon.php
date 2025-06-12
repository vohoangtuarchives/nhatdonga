<?php
$award =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
        array('award'));
if(count($award)) { ?>
    <div class="wrap-award my-5">
        <div class="wrapper award-wrapper1">
            <div class="row gx-4">
                <?php foreach($award as $k => $v) { ?>
                    <!-- <div class="product"> class mặc định của source -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="award-items news-item mb-3">
                            <a class="d-flex align-items-center gx-2 text-decoration-none"  title="<?=$v['name'.$lang]?>">
                                <div class="image me-3">
                                    <?=$func->getImage([ 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                </div>
                                <div class="content">
                                    <h3 class="news-name"><?=$v['desc'.$lang]?></h3>
                                    <p class="news-desc text-split">
                                        <?=$v['name'.$lang]?>

                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<?php
$taisaochon =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
    array('tai-sao-chon'));
if(count($taisaochon)) { ?>
    <div class="wrap-taisaochon wrapper py-5">
        <div class="title-main">
            <h2>
                TẠI SAO CHỌN CHÚNG TÔI
                <span class="decoration"></span>
            </h2>
            <p class="title-main__desc"><span class="text-red">Shunco</span> - Tiên phong trong lĩnh vực cung ứng nhân lực Toàn Quốc</p>
            <?php
            /*
            <div class="paging-product-category paging-product-category-<?=$vlist['id']?>" data-list="<?=$vlist['id']?>">

            */
            ?>

        </div>
        <div class="taisaochon-wrapper1">
            <div class="row gx-4">
                <?php foreach($taisaochon as $k => $v) { ?>
                    <!-- <div class="product"> class mặc định của source -->
                    <div class="col-lg-6">
                        <div class="taisaochon-items news-item px-4 mb-3">
                            <a class="row gx-1 align-items-center gx-2 text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                <div class="col-2 text-center me-2">
                                    <?=$func->getImage(['sizes' => '65x75x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                </div>
                                <div class="col">
                                    <h3 class="news-name mb-3"><?=$v['name'.$lang]?></h3>
                                    <p class="news-desc text-split">
                                        <?=$v['desc'.$lang]?>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
