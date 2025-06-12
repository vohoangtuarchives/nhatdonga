<?php
$ketqua =
    $d->rawQuery(
        "select name$lang, photo from #_news where type = ? and find_in_set('hienthi', status) order by numb,id desc",
        array('ket-qua')
    );
?>
<session class="ketqua-home">
    <div class="wrapper">
        <h2 class="title-home">
            Kết quả sau điều trị
        </h2>
        <p class="slogan-title-home">
            Kết quả sau điều trị tại trung tâm chúng tôi đảm bảo làn da khỏe mạnh, mịn màng, cải thiện rõ rệt các vấn đề về mụn, nám, tàn nhang, và sẹo rỗ, mang lại sự tự tin cho khách hàng.
        </p>
        <div class="flipster">
            <ul>
                <?php foreach ($ketqua as $k => $v) { ?>
                    <li class="ketqua-item">
                        <?= $func->getImage(['sizes' => '880x470x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $v['photo'], 'alt' => $v['name' . $lang]]) ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</session>