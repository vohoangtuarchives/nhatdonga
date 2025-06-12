<?php $list_pro = $d->rawQuery("SELECT id, name$lang, slug$lang from table_product_list where type=? and find_in_set('hienthi',status) order by numb", array($type)) ?>
<div class="col-sticky">
    <div class="box-category">
        <h3><i class="fas fa-list mr-2"></i>DANH MỤC</h3>
        <ul class="list-category">
            <?php foreach ($list_pro as $v) { ?>
                <?php $cat_pro = $d->rawQuery("SELECT id, name$lang, slug$lang from table_product_cat where type=? and id_list=? and find_in_set('hienthi',status) order by numb", array($type, $v['id'])); ?>
                <li class="plus-1 <?= (isset($productList) && $productList['id'] == $v['id']) ? 'active' : '' ?>"><a href="<?= $v['slug' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    <?= (count($cat_pro) > 0) ? '<i class="fas fa-caret-down plus-toggle"></i>' : '' ?>
                    <?php if ($cat_pro) { ?>
                        <ul class="cat-category">
                            <?php foreach ($cat_pro as $v2) { ?>
                                <?php $item_pro = $d->rawQuery("SELECT id, name$lang, slug$lang from table_product_item where type=? and id_cat=? and find_in_set('hienthi',status) order by numb", array($type, $v2['id'])); ?>
                                <li class="plus-2 <?= (isset($productCat) && $productCat['id'] == $v2['id']) ? 'active' : '' ?>">
                                    <a href="<?= $v2['slug' . $lang] ?>"><?= $v2['name' . $lang] ?></a>
                                    <?= (count($item_pro) > 0) ? '<i class="fas fa-caret-down cat-toggle"></i>' : '' ?>
                                    <?php if ($item_pro) { ?>
                                        <ul class="item-category">
                                            <?php foreach ($item_pro as $v3) { ?>
                                                <li <?= (isset($productItem) && $productItem['id'] == $v3['id']) ? 'class="active"' : '' ?>><a href="<?= $v3['slug' . $lang] ?>"><?= $v3['name' . $lang] ?></a></li>
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
    </div>
</div>
<style>
    .box-category>h3 {
        font-size: 17px;
        padding: 5px 0;
        font-weight: bold;
    }

    .list-category {
        margin: 5px 0 0 5px;
        padding: 0;
        list-style: none;
        text-transform: capitalize;
    }

    .list-category li {
        position: relative;
    }

    .list-category ul {
        margin: 0 0 0 20px;
        padding: 0;
        list-style: none;
        display: none;
    }

    .plus-toggle,
    .cat-toggle {
        position: absolute;
        top: 8.5px;
        right: 15px;
        cursor: pointer;
        width: 15px;
        height: 15px;
        text-align: center;
    }

    .list-category li.active>a {
        color: #f00;
    }
</style>
<script>
    $(".plus-toggle").click(function() {
        $(this).parents(".plus-1").find(".cat-category").slideToggle("fast");
    });
    $(".cat-toggle").click(function() {
        $(this).parents(".plus-2").find(".item-category").slideToggle("fast");
    });
    if ($(".plus-1").length) {
        $(".plus-1").each(function() {
            if ($(this).hasClass("active")) {
                $(this).find(".cat-category").slideDown("fast");
            }
        });
    }
    if ($(".plus-2").length) {
        $(".plus-2").each(function() {
            if ($(this).hasClass("active")) {
                $(this).find(".item-category").slideDown("fast");
            }
        });
    }
</script>