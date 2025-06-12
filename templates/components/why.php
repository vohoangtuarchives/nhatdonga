<?php
$taisaochon =
$d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo from #_news where type = ? and find_in_set('noibat', status) and find_in_set('hienthi', status) order by numb,id desc limit 5",
array('tai-sao-chon'));
if(count($taisaochon)) { ?>
    <div class="wrap-taisaochon wrapper py-5">
    <div class="title-main">
            <p class="title-main__desc">[ WE HAVE ]</p>
            <h2>
                NHA KHOA <br />
                TOÀN MỸ VỚI
                <span class="decoration"></span>
            </h2>
            
            <?php
            /*
            <div class="paging-product-category paging-product-category-<?=$vlist['id']?>" data-list="<?=$vlist['id']?>">

            */
            ?>

        </div>
        <div class="product-wrapper1 pb-5">
            <div class="">
                <div class=" owl-carousel owl-theme owl-page owl-product" data-xsm-items="1:0" data-sm-items="1:0"
                    data-md-items="1:0" data-lg-items="1:0" data-xlg-items="5:10" data-rewind="1" data-autoplay="1" data-loop="0"
                    data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800"
                    data-autoplaytimeout="5000" data-dots="0"
                    data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn"
                    data-nav="1"
                    data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>"
                    data-navcontainer=".control-dichvu">
                <?php foreach($taisaochon as $stt => $v) { ?>
                    <div class="<?=$stt%2==1 ? 'even':''?>">
                    <div class="why-items">
                            <a class="box-product text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                <div class="pic-product scale-img">
                                    <div>
                                    <?=$func->getImage(['sizes' => '100x100x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name'.$lang]])?>
                                    </div>
                                </div>
                                <h3 class="news-name text-center p-3"><?=$v['name'.$lang]?></h3>
                            </a>
                        </div> 
                        </div>
                <?php } ?>
            </div>
            <div class="control-taisaochon control-owl transition"></div>
                </div>
        </div>
    </div>
<?php } ?>
