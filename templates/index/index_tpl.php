<div class="block-sanpham block-padding">
    <div class="container ">
        <div class="title-main"><span>SẢN PHẨM CỦA CHÚNG TÔI</span></div>
        <div class="w-100">
			<?php foreach ($indexProductListsNoiBat as $k => $item) { ?>
			        <span class="btn btn-sm"><?=$item["name$lang"]?></span>
			<?php } ?>
        </div>
    </div>
</div>

<div class="block-dichvu block-padding">
    <div class="container ">
        <div class="title-main"><span>DỊCH VỤ</span></div>
        <div class="w-100">
            <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:3|margin:15,screen:575|items:4|margin:20,screen:767" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="1" data-navcontainer=".control-article">
				<?php foreach($newsDichvu as $item){  ?>
                    <div class="news-item dichvu-item position-relative mb-4 overflow-hidden">
                        <a class="box-news text-decoration-none" href="<?= $item[$sluglang] ?>" title="<?= $item['name' . $lang] ?>">
                            <p class="pic-news scale-img mb-0">
								<?= $func->getImage(['sizes' => '480x480x1', 'isWatermark' => true, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $item['photo'], 'alt' => $item['name' . $lang]]) ?>
                            </p>
                            <h3 class="name-news text-split position-absolute p-3 text-center w-100"><?= $item['name' . $lang] ?></h3>
                        </a>
                    </div>
				<?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="block-duan block-padding">
	<div class="container ">
        <div class="title-main"><span>DỰ ÁN</span></div>
		<div class="w-100">
			<div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:2|margin:15,screen:575|items:3|margin:20,screen:767" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="1" data-navcontainer=".control-article">
                    <?php foreach($newsDuan as $item){  ?>
                        <div class="news-item duan-item position-relative mb-4 overflow-hidden">
                            <a class="box-news text-decoration-none" href="<?= $item[$sluglang] ?>" title="<?= $item['name' . $lang] ?>">
                                <p class="pic-news scale-img mb-0">
									<?= $func->getImage(['sizes' => '480x480x1', 'isWatermark' => true, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $item['photo'], 'alt' => $item['name' . $lang]]) ?>
                                </p>
                                <h3 class="name-news text-split position-absolute p-3 text-center w-100"><?= $item['name' . $lang] ?></h3>
                            </a>
                        </div>
                <?php } ?>
			</div>
		</div>
	</div>
</div>


