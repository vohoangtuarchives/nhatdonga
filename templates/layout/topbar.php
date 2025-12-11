<div id="topbar">
    <div class="header-topbar">
        <div class="wrapper">
            <div class="w-100 d-flex justify-content-between align-items-center">
                <div>

                </div>
                <div class="d-flex">
                    <div class="col-auto d-flex align-items-center me-3">
                        <span class="icon icon-45"><i class="fa fa-home"></i></span>
                        <div>
                            <p><?= $optsetting["email"] ?></p>
                        </div>
                    </div>

                    <div class="col-auto d-flex align-items-center">
                        <span class="icon icon-45"><i class="fa fa-phone"></i></span>
                        <div>
                            <p><?= $optsetting["phone"] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="section-logo">
    <div class="wrapper d-flex justify-content-between align-items-center">
        <a href="<?= $config_base ?>">
            <img class="img-fluid" onerror="this.src='thumbs/128x55x1/assets/images/noimage.png';"
                 src="thumbs/124x50x1/upload/photo/<?= $logo['photo'] ?? '' ?>" alt="<?= $logo['photo'] ?? '' ?>">
        </a>
        <div class="section-menu d-lg-block d-none">
            <?php include __DIR__."/menu.php" ?>
        </div>
        <!-- Mobile Menu Button -->
        <div class="mobile-menu-toggle d-lg-none d-block">
            <a id="mobile-hamburger" href="#mobile-menu" class="hamburger-btn" title="Menu">
                <span></span>
                <span></span>
                <span></span>
            </a>
        </div>
    </div>
</div>
<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
<!-- Mobile Menu Navigation -->
<nav id="mobile-menu" class="mobile-menu-nav">
    <ul>
        <li><a class="<?php if ($com == '' || $com == 'index') echo 'active'; ?> transition" href="" title="<?= trangchu ?>"><?= trangchu ?></a></li>
        <li class="line"></li>
        <li><a class="<?php if ($com == 'gioi-thieu') echo 'active'; ?> transition" href="gioi-thieu" title="<?= gioithieu ?>"><?= gioithieu ?></a></li>
        <li class="line"></li>
        <li>
            <a class="has-child <?php if ($com == 'san-pham') echo 'active'; ?> transition" href="san-pham" title="<?= sanpham ?>"><?= sanpham ?></a>
            <?php if (!empty($splist) && count($splist)) { ?>
                <ul>
                    <?php foreach ($splist as $klist => $vlist) {
                        $spcat = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                        <li>
                            <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                            <?php if (!empty($spcat)) { ?>
                                <ul>
                                    <?php foreach ($spcat as $kcat => $vcat) {
                                        $spitem = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($vcat['id'])); ?>
                                        <li>
                                            <a class="has-child transition" title="<?= $vcat['name' . $lang] ?>" href="<?= $vcat[$sluglang] ?>"><?= $vcat['name' . $lang] ?></a>
                                            <?php if (!empty($spitem)) { ?>
                                                <ul>
                                                    <?php foreach ($spitem as $kitem => $vitem) {
                                                        $spsub = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>
                                                        <li>
                                                            <a class="has-child transition" title="<?= $vitem['name' . $lang] ?>" href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
                                                            <?php if (!empty($spsub)) { ?>
                                                                <ul>
                                                                    <?php foreach ($spsub as $ksub => $vsub) { ?>
                                                                        <li>
                                                                            <a class="transition" title="<?= $vsub['name' . $lang] ?>" href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            <?php } ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li class="line"></li>
        <li><a class="<?php if ($com == 'kien-thuc') echo 'active'; ?> transition" href="kien-thuc" title="Kiến thức">Kiến Thức</a></li>
        <li class="line"></li>
        <li>
            <a class="has-child <?php if ($com == 'tin-tuc') echo 'active'; ?> transition" href="tin-tuc" title="<?= tintuc ?>"><?= tintuc ?></a>
            <?php if (!empty($ttlist) && count($ttlist)) { ?>
                <ul>
                    <?php foreach ($ttlist as $klist => $vlist) {
                        $ttcat = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                        <li>
                            <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                            <?php if (!empty($ttcat)) { ?>
                                <ul>
                                    <?php foreach ($ttcat as $kcat => $vcat) {
                                        $ttitem = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($vcat['id'])); ?>
                                        <li>
                                            <a class="has-child transition" title="<?= $vcat['name' . $lang] ?>" href="<?= $vcat[$sluglang] ?>"><?= $vcat['name' . $lang] ?></a>
                                            <?php if (!empty($ttitem)) { ?>
                                                <ul>
                                                    <?php foreach ($ttitem as $kitem => $vitem) {
                                                        $ttsub = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>
                                                        <li>
                                                            <a class="has-child transition" title="<?= $vitem['name' . $lang] ?>" href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
                                                            <?php if (!empty($ttsub)) { ?>
                                                                <ul>
                                                                    <?php foreach ($ttsub as $ksub => $vsub) { ?>
                                                                        <li>
                                                                            <a class="transition" title="<?= $vsub['name' . $lang] ?>" href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            <?php } ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li class="line"></li>
        <li><a class="<?php if ($com == 'lien-he') echo 'active'; ?> transition" href="lien-he" title="<?= lienhe ?>"><?= lienhe ?></a></li>
    </ul>
</nav>