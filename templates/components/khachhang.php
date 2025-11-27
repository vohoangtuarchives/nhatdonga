<style>
    .logo-row {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .logo-item {
        width: 248px;
        height: 165px;
        border-radius: 30px;
        border: 2px solid #27ae60;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .logo-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>

<?php
$khachhang =
    $d->rawQuery("select name$lang,  desc$lang, date_created, slugvi, id, photo, career from #_news where type = ? and find_in_set('hienthi', status) order by numb,id desc",
    array('khach-hang'));

$pattern = [5, 4]; // 4 items row → 5 items row → repeat
$patternIndex = 0;
$offset = 0;
$items = $khachhang;




 if(count($items)) { ?>

    <div class="wrap-khachhang wrapper py-5">

        <div class="title-main">
            <div class="section-header-isp text-center">
                <h2 class="section-title-isp">KHÁCH HÀNG</h2>
            </div>
        </div>



        <?php while ($offset < count($items)):
            $take = $pattern[$patternIndex];
            $rowItems = array_slice($items, $offset, $take);
            $patternIndex = ($patternIndex + 1) % count($pattern);
            $offset += $take;
        ?>
            <div class="logo-row">
                <?php foreach ($rowItems as $item): ?>
                    <div class="logo-item">
                        <?=$func->getImage(['sizes' => '248x165x1', 'isWatermark' => false, 'prefix' => 'news', 'upload' => UPLOAD_NEWS_L, 'image' => $item['photo'], 'alt' => $item['name'.$lang]])?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>
        </div>
<?php } ?>

