<?php
$khachhang =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo, career from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc",
    array('kien-thuc'));
    $khachhang_chunk = array_chunk($khachhang, 2);
if(count($khachhang)) { ?>
    <div class="wrap-khachhang wrapper py-5">
    <div class="title-main">
            <h2>
                Khách hàng nói gì?
            </h2>
        <p class="title-main__desc">[ CLIENT ]</p>

        <?php
            /*
            <div class="paging-product-category paging-product-category-<?=$vlist['id']?>" data-list="<?=$vlist['id']?>">

            */
            ?>

        </div>
        <div class="product-wrapper1">
            <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="2:10" data-sm-items="2:20"
                 data-md-items="4:20" data-lg-items="4:20" data-xlg-items="4:20" data-rewind="1" data-autoplay="1" data-loop="0"
                 data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                 data-autoplaytimeout="5000" data-dots="0"
                 data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                 data-nav="1"
                 data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                 data-navcontainer=".control-khachhang">

                <?php foreach($khachhang_chunk as $stt => $kh) { ?>
                    <div class="khachhang-group">
                    <?php foreach($kh as $k => $v) { ?>
                    <!-- <div class="product"> class mặc định của source -->
                    <div class="khachhang-items news-item mb-4">
                        <a class="box-product text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                            <p class="pic-product scale-img">
                                <?=$func->getImage(['sizes' => '281x468x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                            </p>
                            <div class="_content">
                                <div class="mb-4">
                                <div class="_icon">
                                    <img src="assets/images/c_<?=$v['career']?>.png" alt="career">
                                </div>
                                <div class="_career"><?=$config['careers'][$v['career']]?></div>
                                <h3 class="news-name text-center p-3"><?=$v['name'.$lang]?></h3>
                                </div>
                            </div>
                            
                        </a>
                    </div>
                    <?php } ?>
                    </div>    
                <?php } ?>
            </div>
            <div class="control-khachhang control-owl transition"></div>
        </div>
    </div>
<?php } ?>
