<div class="menu">

    <div class="wrap-content">


        <ul class="menu-main">

            <li><a class="<?php if ($com == 'index') echo 'active'; ?> transition" href=""
                   title="<?= trangchu ?>"><?= trangchu ?></a></li>

            <li class="menu-line"></li>

            <li><a class="<?php if ($com == 'gioi-thieu') echo 'active'; ?> transition" href="gioi-thieu"
                   title="<?= gioithieu ?>"><?= gioithieu ?></a></li>

            <li class="menu-line"></li>

            <li>

                <a class="has-child <?php if ($com == 'san-pham') echo 'active'; ?> transition" href="san-pham"

                   title="XÂY DỰNG">SẢN PHẨM</a>

                <?php if (count($splist)) { ?>
                    <ul>
                        <?php foreach ($splist as $klist => $vlist) {
                            $spcat = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>

                            <li>

                                <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>"
                                   href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>

                                <?php if (!empty($spcat)) { ?>

                                    <ul>

                                        <?php foreach ($spcat as $kcat => $vcat) {

                                            $spitem = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($vcat['id'])); ?>

                                            <li>

                                                <a class="has-child transition" title="<?= $vcat['name' . $lang] ?>"
                                                   href="<?= $vcat[$sluglang] ?>"><?= $vcat['name' . $lang] ?></a>

                                                <?php if (!empty($spitem)) { ?>

                                                    <ul>

                                                        <?php foreach ($spitem as $kitem => $vitem) {

                                                            $spsub = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>

                                                            <li>

                                                                <a class="has-child transition"
                                                                   title="<?= $vitem['name' . $lang] ?>"
                                                                   href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>

                                                                <?php if (!empty($spsub)) { ?>

                                                                    <ul>

                                                                        <?php foreach ($spsub as $ksub => $vsub) { ?>

                                                                            <li>

                                                                                <a class="transition"
                                                                                   title="<?= $vsub['name' . $lang] ?>"
                                                                                   href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>

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

            <li class="menu-line"></li>

            <li><a class="<?php if ($com == 'dich-vu') echo 'active'; ?> transition" href="dich-vu"
                   title="<?= dichvu ?>"><?= dichvu ?></a></li>

            <li class="menu-line"></li>

            <li><a class="<?php if ($com == 'thanh-toan') echo 'active'; ?> transition" href="thanh-toan"
                   title="<?= thanhtoan ?>"><?= thanhtoan ?></a></li>

            <li class="menu-line"></li>

            <li><a class="<?php if ($com == 'lien-he') echo 'active'; ?> transition" href="lien-he"
                   title="<?= lienhe ?>"><?= lienhe ?></a></li>

        </ul>

    </div>

</div>
