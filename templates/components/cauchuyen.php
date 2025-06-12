<?php
$cauchuyen =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
        array('cau-chuyen'));
if(count($cauchuyen)) { ?>
    <div class="wrap-cauchuyen">
        <div class="wrapper  py-5">
            <div class="title-main">
                <h2>
                    CÂU CHUYỆN THÀNH CÔNG
                    <span class="decoration"></span>
                </h2>
                <p class="title-main__desc"><span class="text-red">Shunco</span> - Tiên phong trong lĩnh vực cung ứng nhân lực Toàn Quốc</p>
            </div>
            <div class="cauchuyen-wrapper1 mt-5">
                <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="1:0" data-sm-items="1:0"
                     data-md-items="1:0" data-lg-items="1:0" data-xlg-items="1:0" data-rewind="1" data-autoplay="1" data-loop="0"
                     data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                     data-autoplaytimeout="5000" data-dots="1"
                     data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                     data-nav="1"
                     data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                     data-navcontainer=".control-cauchuyenthanhcong">

                    <?php foreach($cauchuyen as $k => $v) { ?>
                        <!-- <div class="product"> class mặc định của source -->
                        <div class="cauchuyen-items news-item">
                            <a class="text-decoration-none d-flex flex-wrap justify-content-between"  title="<?=$v['name'.$lang]?>">
                                <div class="image">
                                    <p class="scale-img">
                                        <?=$func->getImage(['sizes' => '260x260x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                    </p>
                                </div>
                                <div class="content">
                                    <h3 class="news-name"><?=$v['name'.$lang]?></h3>
                                    <p class="news-desc">
                                        <?=$v['desc'.$lang]?>
                                    </p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
                <div class="control-cauchuyenthanhcong control-owl transition"></div>
            </div>
        </div>
    </div>
<?php } ?>
