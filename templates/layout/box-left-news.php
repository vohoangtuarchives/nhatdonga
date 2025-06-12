<?php $list_new = $d->rawQuery("SELECT id, name$lang, slug$lang from table_news where type=? and find_in_set('hienthi',status) order by numb", array($type)) ?>
<div class="col-sticky">
    <div class="box-list">
        <h3><i class="fas fa-list mr-2"></i>DANH MỤC</h3>
        <ul class="list-category">
            <?php foreach ($list_new as $v) { ?>
                <?php $cat_new = $d->rawQuery("SELECT id, name$lang, slug$lang from table_news_cat where type=? and id_list=? and find_in_set('hienthi',status) order by numb", array($type, $v['id'])); ?>
                <li class="plus-1"><a href="<?= $v['slug' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    <?= (count($cat_new) > 0) ? '<i class="fas fa-caret-down plus-list"></i>' : '' ?>
                    <?php if ($cat_new) { ?>
                        <ul class="cat-category">
                            <?php foreach ($cat_new as $v2) { ?>
                                <?php $item_new = $d->rawQuery("SELECT id, name$lang, slug$lang from table_news_item where type=? and id_cat=? and find_in_set('hienthi',status) order by numb", array($type, $v2['id'])); ?>
                                <li class="plus-2">
                                    <a href="<?= $v2['slug' . $lang] ?>"><?= $v2['name' . $lang] ?></a>
                                    <?= (count($item_new) > 0) ? '<i class="fas fa-caret-down cat-list"></i>' : '' ?>
                                    <?php if ($item_new) { ?>
                                        <ul class="item-category">
                                            <?php foreach ($item_new as $v3) { ?>
                                                <li><a href="<?= $v3['slug' . $lang] ?>"><?= $v3['name' . $lang] ?></a></li>
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
    .box-list>h3 {
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
        line-height: 35px;
        position: relative;
    }

    .list-category ul {
        margin: 0 0 0 20px;
        padding: 0;
        list-style: none;
        display: none;
    }

    .plus-list,
    .cat-list {
        position: absolute;
        top: 8.5px;
        right: 15px;
        cursor: pointer;
        width: 15px;
        height: 15px;
        text-align: center;
    }
</style>
<script>
    $(".plus-list").click(function() {
        $(this).parents(".plus-1").find(".cat-category").slideToggle("fast");
    });
    $(".cat-list").click(function() {
        $(this).parents(".plus-2").find(".item-category").slideToggle("fast");
    });
</script>