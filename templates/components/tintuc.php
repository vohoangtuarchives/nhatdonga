<?php
$bacsi =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo, career from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
        array('bac-si'));
if(count($bacsi)) { ?>
    <div class="block-news block-bacsi bg-main">
        <div class="wrap-bacsi wrapper py-5">
            <div class="title-main">
                <h2>
                    Đội ngũ bác sĩ
                    <span class="decoration"></span>
                </h2>
                <p class="title-main__desc">
                    Đội ngũ bác sĩ tại trung tâm chúng tôi là những chuyên gia da liễu giàu kinh nghiệm, tận tâm, luôn cập nhật công nghệ hiện đại
                </p>
            </div>
            <div class="row">
            <div class="col-lg-6 col-12 block-images__bacsi mb-3">
                <div class="product-wrapper1">
                    <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="1:0" data-sm-items="1s:0"
                         data-md-items="2:15" data-lg-items="2:15" data-xlg-items="2:15" data-rewind="1"
                         data-autoplay="1" data-loop="0"
                         data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                         data-autoplaytimeout="5000" data-dots="0"
                         data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                         data-nav="1"
                         data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                         data-navcontainer=".control-khachhang">

                        <?php foreach ($bacsi as $k => $v) {
                            echo $custom->itemCustomer(array_merge($v, [
                                'thumb_size' => '400x600x1'
                            ]), "bacsi_item", false);
                        } ?>
                    </div>
                    <div class="control-khachhang control-owl transition"></div>
                </div>
            </div>
            <div class="col-lg-6 col-12 block-tieuchi">
                <?php
$taisaochon =
$d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc limit 4",
array('tai-sao-chon'));
                if(count($taisaochon)) { ?>

                <div class="product-wrapper1 pb-5">
                    <div class="row">
                            <?php foreach($taisaochon as $stt => $v) { ?>
                                <div class="col-6">
                                    <div class="why-items">
                                        <a class="box-product text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                            <div class="pic-product scale-img">
                                                <div>
                                                    <?=$func->getImage(['sizes' => '75x74x2', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                                </div>
                                            </div>
                                            <h3 class="news-name text-center p-3"><?=$v['name'.$lang]?></h3>
                                            <div class="text-white">
                                                <p><?=$v['desc'.$lang]?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                    </div>
                </div>

        <?php } ?>
            </div>
            </div>
        </div>
    </div>
<?php } ?>



<?php
$khachhang =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo, career from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
        array('khach-hang'));
if(count($khachhang)) { ?>
    <div class="block-news block-khachhang">
        <div class="wrap-dichvu wrapper py-5">
            <div class="title-main">
                <h2>
                    Khách hàng nói gì?
                    <span class="decoration"></span>
                </h2>
                <p class="title-main__desc">Những thông tin mới nhất về xu hướng, nghiên cứu, và công nghệ trong ngành nuôi trồng gà, giúp nông dân cập nhật và áp dụng những phương pháp hiệu quả</p>
            </div>

            <div class="product-wrapper1">
                <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="1:0" data-sm-items="1:0"
                     data-md-items="2:15" data-lg-items="3:15" data-xlg-items="3:15" data-rewind="1"
                     data-autoplay="1" data-loop="0"
                     data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                     data-autoplaytimeout="5000" data-dots="0"
                     data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                     data-nav="1"
                     data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                     data-navcontainer=".control-khachhang">

                    <?php foreach ($khachhang as $k => $v) {
                        echo $custom->itemCustomer(array_merge($v, [
                                'thumb_size' => '105x105x1'
                        ]), "khachhang_item", true);
                    } ?>
                </div>
                <div class="control-khachhang control-owl transition"></div>
            </div>
        </div>
    </div>
<?php } ?>


<?php
$tintucIndex =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
        array('tin-tuc'));
if(count($tintucIndex)) { ?>
    <div class="block-news bg-main">
        <div class="wrap-tintuc wrapper py-5">
            <div class="title-main">
                <h2>
                    Tin Tức
                    <span class="decoration"></span>
                </h2>
                <p class="title-main__desc">Những thông tin mới nhất về xu hướng, nghiên cứu, và công nghệ trong ngành nuôi trồng gà, giúp nông dân cập nhật và áp dụng những phương pháp hiệu quả</p>
            </div>

            <div class="product-wrapper1">
                <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="2:10" data-sm-items="2:10"
                     data-md-items="3:15" data-lg-items="3:20" data-xlg-items="3:30" data-rewind="1"
                     data-autoplay="1" data-loop="0"
                     data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                     data-autoplaytimeout="5000" data-dots="0"
                     data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                     data-nav="1"
                     data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                     data-navcontainer=".control-tintuc">

                    <?php foreach ($tintucIndex as $k => $v) {
                        echo $custom->news($v, "");
                    } ?>
                </div>
                <div class="control-tintuc control-owl transition"></div>
            </div>

        </div>
    </div>
<?php } ?>

