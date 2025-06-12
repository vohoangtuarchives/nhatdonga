<section class="section-slider position-relative">
    <div class="wrapper">
        <div class="flex-box">
            <div class="divLeft d-none d-lg-block">
                <?php include TEMPLATE . LAYOUT . 'second-menu.php' ?>
            </div>
            <div class="divRight">
                <div class="box-slide">
                    <?php if (count($slider)) { ?>
                        <div class="slideshow position-relative">
                            <div class="owl-page owl-carousel owl-theme" data-xsm-items="1:0" data-sm-items="1:0" data-md-items="1:0" data-lg-items="1:0" data-xlg-items="1:0" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800" data-autoplaytimeout="5000" data-dots="0" data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn" data-nav="1" data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-left' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='5' y1='12' x2='9' y2='16' /><line x1='5' y1='12' x2='9' y2='8' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-arrow-narrow-right' width='50' height='37' viewBox='0 0 24 24' stroke-width='1' stroke='#ffffff' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><line x1='5' y1='12' x2='19' y2='12' /><line x1='15' y1='16' x2='19' y2='12' /><line x1='15' y1='8' x2='19' y2='12' /></svg>" data-navcontainer=".control-slideshow">
                                <?php foreach ($slider as $v) { ?>
                                    <div class="slideshow-item" owl-item-animation>
                                        <a class="slideshow-image" href="<?= $v['link'] ?>" target="_blank" title="<?= $v['name' . $lang] ?>">
                                            <?= $func->getImage(['class' => 'img-fluid', 'sizes' => '1366x600x1', 'upload' => UPLOAD_PHOTO_L, 'image' => $v['photo'], 'alt' => $v['name' . $lang]]) ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="control-slideshow control-owl transition"></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>