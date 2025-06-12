<?php
$linkMan = "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage;
$linkSave = "index.php?com=filter&act=save&type=" . $type . "&p=" . $curPage;
if (
    (isset($config['filter'][$type]['images']) && $config['filter'][$type]['images'] == true)
) {
    $colLeft = "col-xl-8";
    $colRight = "col-xl-4";
} else {
    $colLeft = "col-12";
    $colRight = "d-none";
}
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="Bảng điều khiển">Bảng điều khiển</a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>" title="<?= $config['filter'][$type]['title_main'] ?>">Quản lý <?= $config['filter'][$type]['title_main'] ?></a></li>
                <li class="breadcrumb-item active"><?= ($act == "edit") ? "Cập nhật" : "Thêm mới"; ?> <?= $config['filter'][$type]['title_main'] ?></li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <form class="validation-form" novalidate method="post" action="<?= $linkSave ?>" enctype="multipart/form-data">
        <div class="card-footer text-sm sticky-top">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check"><i class="far fa-save mr-2"></i>Lưu</button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i>Làm lại</button>
            <a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="Thoát"><i class="fas fa-sign-out-alt mr-2"></i>Thoát</a>
        </div>
        <div class="row">
            <div class="<?= $colLeft ?>">
                <?php
                if (isset($config['filter'][$type]['slug']) && $config['filter'][$type]['slug'] == true) {
                    $slugchange = ($act == 'edit') ? 1 : 0;
                    $copy = ($act != 'copy') ? 0 : 1;
                    include TEMPLATE . LAYOUT . "slug.php";
                }
                ?>
                <div class="card card-primary card-outline text-sm">
                    <div class="card-header">
                        <h3 class="card-title">Nội dung <?= $config['filter'][$type]['title_main'] ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="card card-primary card-outline card-outline-tabs text-sm">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-three-tab-lang" role="tablist">
                                    <?php foreach ($config['website']['lang'] as $k => $v) { ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-lang" data-toggle="pill" href="#tabs-lang-<?= $k ?>" role="tab" aria-controls="tabs-lang-<?= $k ?>" aria-selected="true"><?= $v ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="card-body card-article">
                                <div class="tab-content" id="custom-tabs-three-tabContent-lang">
                                    <?php foreach ($config['website']['lang'] as $k => $v) { ?>
                                        <div class="tab-pane fade show <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-lang-<?= $k ?>" role="tabpanel" aria-labelledby="tabs-lang">
                                            <div class="form-group">
                                                <label for="name<?= $k ?>">Tiêu đề (<?= $k ?>):</label>
                                                <input type="text" class="form-control for-seo" name="data[name<?= $k ?>]" id="name<?= $k ?>" placeholder="Tiêu đề (<?= $k ?>)" value="<?= @$item['name' . $k] ?>" <?= ($k == 'vi') ? 'required' : '' ?>>
                                            </div>
                                            <?php if (isset($config['filter'][$type]['desc']) && $config['filter'][$type]['desc'] == true) { ?>
                                                <div class="form-group">
                                                    <label for="desc<?= $k ?>">Mô tả (<?= $k ?>):</label>
                                                    <textarea class="form-control for-seo <?= (isset($config['filter'][$type]['desc_cke']) && $config['filter'][$type]['desc_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[desc<?= $k ?>]" id="desc<?= $k ?>" rows="5" placeholder="Mô tả (<?= $k ?>)"><?= htmlspecialchars_decode(@$item['desc' . $k]) ?></textarea>
                                                </div>
                                            <?php } ?>
                                            <?php if (isset($config['filter'][$type]['content']) && $config['filter'][$type]['content'] == true) { ?>
                                                <div class="form-group">
                                                    <label for="content<?= $k ?>">Nội dung (<?= $k ?>):</label>
                                                    <textarea class="form-control for-seo <?= (isset($config['filter'][$type]['content_cke']) && $config['filter'][$type]['content_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[content<?= $k ?>]" id="content<?= $k ?>" rows="5" placeholder="Nội dung (<?= $k ?>)"><?= htmlspecialchars_decode(@$item['content' . $k]) ?></textarea>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card card-primary card-outline text-sm">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin <?= $config['filter'][$type]['title_main'] ?></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="numb" class="d-inline-block align-middle mb-0 mr-2">Số thứ tự:</label>
                            <input type="number" class="form-control form-control-mini d-inline-block align-middle" min="0" name="data[numb]" id="numb" placeholder="Số thứ tự" value="<?= isset($item['numb']) ? $item['numb'] : 1 ?>">
                        </div>
                        <div class="form-group">
                            <?php $status_array = (!empty($item['status'])) ? explode(',', $item['status']) : array(); ?>
                            <?php if (isset($config['filter'][$type]['check'])) {
                                foreach ($config['filter'][$type]['check'] as $key => $value) { ?>
                                    <div class="form-group d-inline-block mb-2 mr-2">
                                        <label for="<?= $key ?>-checkbox" class="d-inline-block align-middle mb-0 mr-2"><?= $value ?>:</label>
                                        <div class="custom-control custom-checkbox d-inline-block align-middle">
                                            <input type="checkbox" class="custom-control-input <?= $key ?>-checkbox" name="status[<?= $key ?>]" id="<?= $key ?>-checkbox" <?= (empty($status_array) && empty($item['id']) ? 'checked' : in_array($key, $status_array)) ? 'checked' : '' ?> value="<?= $key ?>">
                                            <label for="<?= $key ?>-checkbox" class="custom-control-label"></label>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                        <?php if (isset($config['filter'][$type]['value']) && $config['filter'][$type]['value'] == true) { ?>
                            <div class="form-group">
                                <?php $priceFormat = (isset($config['filter'][$type]['price']) && $config['filter'][$type]['price'] == true) ? 'format-price' : '';
                                $spacePrice = explode(",", $item['value']); ?>
                                <label for="value">Khoảng giá trị:</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input type="text" name="min-value" required placeholder="Nhỏ nhất" class="form-control <?= $priceFormat ?> " value="<?= (!empty($spacePrice)) ? $spacePrice[0] : '' ?>">
                                            <?php if ((isset($config['filter'][$type]['price']) && $config['filter'][$type]['price'] == true)) { ?>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><strong>VNĐ</strong></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input type="text" name="max-value" required placeholder="Lớn nhất" class="form-control <?= $priceFormat ?>" value="<?= (!empty($spacePrice)) ? $spacePrice[1] : '' ?>">
                                            <?php if ((isset($config['filter'][$type]['price']) && $config['filter'][$type]['price'] == true)) { ?>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><strong>VNĐ</strong></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="<?= $colRight ?>">
                <?php if (isset($config['filter'][$type]['images']) && $config['filter'][$type]['images'] == true) { ?>
                    <div class="card card-primary card-outline text-sm">
                        <div class="card-header">
                            <h3 class="card-title">Hình ảnh <?= $config['filter'][$type]['title_main'] ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            /* Photo detail */
                            $photoDetail = array();
                            $photoDetail['upload'] = UPLOAD_FILTER_L;
                            $photoDetail['image'] = (!empty($item) && $act != 'copy') ? $item['photo'] : '';
                            $photoDetail['dimension'] = "Width: " . $config['filter'][$type]['width'] . " px - Height: " . $config['filter'][$type]['height'] . " px (" . $config['filter'][$type]['img_type'] . ")";

                            /* Image */
                            include TEMPLATE . LAYOUT . "image.php";
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="card-footer text-sm">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check"><i class="far fa-save mr-2"></i>Lưu</button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i>Làm lại</button>
            <a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="Thoát"><i class="fas fa-sign-out-alt mr-2"></i>Thoát</a>
            <input type="hidden" name="id" value="<?= @$item['id'] ?>">
        </div>
    </form>
</section>