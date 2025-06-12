<?php
$daotao = $d->rawQuery("select ten$lang, tenkhongdauvi, tenkhongdauen, mota$lang, ngaytao, id, photo from #_news where type = ? and noibat > 0 and hienthi > 0 order by stt,id desc",array('dao-tao'));
if(count($daotao)) { ?>
    <div class="wrap-daotao">
        <div class="title-main"><span class="text-white">Đào tạo</span></div>
        <div class="title-main__desc"><span class="text-white">Với nhiều năm kinh nghiệm i Care Pet tự tin là <br />nơi đào tạo nghề chất lượng</span></div>
        <div class="wrap-content d-flex align-items-center justify-content-between">
            <p class="control-carousel prev-carousel prev-partner transition"><i class="fas fa-chevron-left"></i></p>
            <div class="owl-carousel owl-theme owl-daotao">
                <?php foreach($daotao as $v) { ?>
                    <div class="daotao-item">
                        <div class="img-daotao mb-3">
                            <a class="partner text-decoration-none" href="<?=$v[$sluglang]?>" target="_blank" title="<?=$v['ten'.$lang]?>">
                                <img onerror="this.src='<?=THUMBS?>/200x200x2/assets/images/noimage.png';" src="<?=THUMBS?>/200x200x1/<?=UPLOAD_NEWS_L.$v['photo']?>" alt="<?=$v['ten'.$lang]?>"/>
                            </a>
                        </div>
                        <div class="daotao-item__detail">
                            <div class="name-news mb-2"><a href="<?= $v[$sluglang] ?>" title="<?= $v['ten'.$lang] ?>"><?= $v['ten'.$lang] ?></a></div>
                            <div class="desc-news text-split"><?= htmlspecialchars_decode($v['mota'.$lang])?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <p class="control-carousel next-carousel next-partner transition"><i class="fas fa-chevron-right"></i></p>
        </div>
    </div>
<?php } ?>
