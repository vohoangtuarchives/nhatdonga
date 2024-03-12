<div class="news-item service-item position-relative mb-4 rounded-4 overflow-hidden">
    <a class="box-news text-decoration-none" href="<?= $item[$sluglang] ?>" title="<?= $item['name' . $lang] ?>">
        <p class="pic-news scale-img mb-0">
            <?= $func->getImage(['sizes' => '480x480x1', 'isWatermark' => true, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $item['photo'], 'alt' => $item['name' . $lang]]) ?>
        </p>
        <h3 class="name-news text-split position-absolute bottom-0 p-3 text-center w-100"><?= $item['name' . $lang] ?></h3>
    </a>
</div>