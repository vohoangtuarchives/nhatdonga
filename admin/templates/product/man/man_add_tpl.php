<?php
if ($act == "add") $labelAct = "Thêm mới";
else if ($act == "edit") $labelAct = "Chỉnh sửa";
else if ($act == "copy")  $labelAct = "Sao chép";

$linkMan = "index.php?com=product&act=man&type=" . $type;
if ($act == 'add') $linkFilter = "index.php?com=product&act=add&type=" . $type;
else if ($act == 'edit') $linkFilter = "index.php?com=product&act=edit&type=" . $type . "&id=" . $id;
if ($act == "copy") $linkSave = "index.php?com=product&act=save_copy&type=" . $type;
else $linkSave = "index.php?com=product&act=save&type=" . $type;

/* Check cols */
if (isset($config['product'][$type]['gallery']) && count($config['product'][$type]['gallery']) > 0) {
	foreach ($config['product'][$type]['gallery'] as $key => $value) {
		if ($key == $type) {
			$keyGallery = $key;
			$flagGallery = true;
			break;
		}
	}
}

if (
	(isset($config['product'][$type]['dropdown']) && $config['product'][$type]['dropdown'] == true) ||
	(isset($config['product'][$type]['brand']) && $config['product'][$type]['brand'] == true) ||
	(isset($config['product'][$type]['tags']) && $config['product'][$type]['tags'] == true) ||
	(isset($config['product'][$type]['color']) && $config['product'][$type]['color'] == true) ||
	(isset($config['product'][$type]['size']) && $config['product'][$type]['size'] == true) ||
	(isset($config['product'][$type]['images']) && $config['product'][$type]['images'] == true) ||
	(isset($config['product'][$type]['filter']) && count($config['product'][$type]['filter']) > 0)
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
				<li class="breadcrumb-item active"><?= $labelAct ?> <?= $config['product'][$type]['title_main'] ?></li>
			</ol>
		</div>
	</div>
</section>

<!-- Main content -->
<section class="content">
	<form class="validation-form" novalidate method="post" action="<?= $linkSave ?>" enctype="multipart/form-data">
		<div class="card-footer text-sm sticky-top">
			<button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i>Lưu</button>
			<button type="submit" class="btn btn-sm bg-gradient-success submit-check" name="save-here" disabled><i class="far fa-save mr-2"></i>Lưu tại trang</button>
			<button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i>Làm lại</button>
			<a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="Thoát"><i class="fas fa-sign-out-alt mr-2"></i>Thoát</a>
		</div>

		<?= $flash->getMessages('admin') ?>

		<div class="row">
			<div class="<?= $colLeft ?>">
				<?php
				if (isset($config['product'][$type]['slug']) && $config['product'][$type]['slug'] == true) {
					$slugchange = ($act == 'edit') ? 1 : 0;
					$copy = ($act != 'copy') ? 0 : 1;
					include TEMPLATE . LAYOUT . "slug.php";
				}
				?>
				<div class="card card-primary card-outline text-sm">
					<div class="card-header">
						<h3 class="card-title">Nội dung <?= $config['product'][$type]['title_main'] ?></h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body">
						<div class="card card-primary card-outline card-outline-tabs">
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
												<input type="text" class="form-control for-seo text-sm" name="data[name<?= $k ?>]" id="name<?= $k ?>" placeholder="Tiêu đề (<?= $k ?>)" value="<?= (!empty($flash->has('name' . $k))) ? $flash->get('name' . $k) : @$item['name' . $k] ?>" required>
											</div>
											<?php if (isset($config['product'][$type]['desc']) && $config['product'][$type]['desc'] == true) { ?>
												<div class="form-group">
													<label for="desc<?= $k ?>">Mô tả (<?= $k ?>):</label>
													<textarea class="form-control for-seo text-sm <?= (isset($config['product'][$type]['desc_cke']) && $config['product'][$type]['desc_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[desc<?= $k ?>]" id="desc<?= $k ?>" rows="5" placeholder="Mô tả (<?= $k ?>)"><?= htmlspecialchars_decode((!empty($flash->has('desc' . $k))) ? $flash->get('desc' . $k) : @$item['desc' . $k]) ?></textarea>
												</div>
											<?php } ?>
											<?php if (isset($config['product'][$type]['content']) && $config['product'][$type]['content'] == true) { ?>
												<div class="form-group">
													<label for="content<?= $k ?>">Nội dung (<?= $k ?>):</label>
													<textarea class="form-control for-seo text-sm <?= (isset($config['product'][$type]['content_cke']) && $config['product'][$type]['content_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[content<?= $k ?>]" id="content<?= $k ?>" rows="5" placeholder="Nội dung (<?= $k ?>)"><?= htmlspecialchars_decode((!empty($flash->has('content' . $k))) ? $flash->get('content' . $k) : @$item['content' . $k]) ?></textarea>
												</div>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="<?= $colRight ?>">
				<?php if (
					(isset($config['product'][$type]['dropdown']) && $config['product'][$type]['dropdown'] == true) ||
					(isset($config['product'][$type]['brand']) && $config['product'][$type]['brand'] == true) ||
					(isset($config['product'][$type]['tags']) && $config['product'][$type]['tags'] == true)
				) { ?>
					<div class="card card-primary card-outline text-sm">
						<div class="card-header">
							<h3 class="card-title">Danh mục <?= $config['product'][$type]['title_main'] ?></h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body">
							<div class="form-group-category row">
								<?php if (isset($config['product'][$type]['dropdown']) && $config['product'][$type]['dropdown'] == true) { ?>
									<?php if (isset($config['product'][$type]['list']) && $config['product'][$type]['list'] == true) { ?>
										<div class="form-group col-xl-6 col-sm-4">
											<label class="d-block" for="id_list">Danh mục cấp 1:</label>
											<?= $func->getAjaxCategory('product', 'list', $type) ?>
										</div>
									<?php } ?>
									<?php if (isset($config['product'][$type]['cat']) && $config['product'][$type]['cat'] == true) { ?>
										<div class="form-group col-xl-6 col-sm-4">
											<label class="d-block" for="id_cat">Danh mục cấp 2:</label>
											<?= $func->getAjaxCategory('product', 'cat', $type) ?>
										</div>
									<?php } ?>
									<?php if (isset($config['product'][$type]['item']) && $config['product'][$type]['item'] == true) { ?>
										<div class="form-group col-xl-6 col-sm-4">
											<label class="d-block" for="id_item">Danh mục cấp 3:</label>
											<?= $func->getAjaxCategory('product', 'item', $type) ?>
										</div>
									<?php } ?>
									<?php if (isset($config['product'][$type]['sub']) && $config['product'][$type]['sub'] == true) { ?>
										<div class="form-group col-xl-6 col-sm-4">
											<label class="d-block" for="id_sub">Danh mục cấp 4:</label>
											<?= $func->getAjaxCategory('product', 'sub', $type) ?>
										</div>
									<?php } ?>
								<?php } ?>
								<?php if (isset($config['product'][$type]['brand']) && $config['product'][$type]['brand'] == true) { ?>
									<div class="form-group col-xl-6 col-sm-4">
										<label class="d-block" for="id_brand">Danh mục hãng:</label>
										<?= $func->getAjaxCategory('product', 'brand', $type, 'Chọn hãng') ?>
									</div>
								<?php } ?>
								<?php if (isset($config['product'][$type]['tags']) && $config['product'][$type]['tags'] == true) { ?>
									<div class="form-group col-xl-6 col-sm-4">
										<label class="d-block" for="id_tags">Danh mục tags:</label>
										<?= $func->getTags(@$item['id'], 'dataTags', 'product_tags', $type) ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (isset($config['product'][$type]['filter']) && count($config['product'][$type]['filter']) > 0) { ?>
					<div class="card card-primary card-outline text-sm">
						<div class="card-header">
							<h3 class="card-title">Bộ lọc <?= $config['product'][$type]['title_main'] ?></h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body row">
							<?php foreach ($config['product'][$type]['filter'] as $k => $v) { ?>
								<div class="form-group col-xl-6 col-sm-4">
									<label class="d-block" for="<?= $config['filter'][$v]['column'] ?>"><?= $config['filter'][$v]['title_main'] ?>:</label>
									<select name="dataFilter[]" id="<?= $config['filter'][$v]['column'] ?>" class="select2 form-control">
										<?php $filter = $d->rawQuery("SELECT id,namevi from table_filter where type=? and find_in_set('hienthi',status) order by namevi", array($v)); ?>
										<option value="">Chọn <?= $config['filter'][$v]['title_main'] ?></option>
										<?php foreach ($filter as $value) { ?>
											<?php $activeFilter = $d->rawQuery("SELECT id from table_filter_relation where com='product' and type=? and id_filter=? and id_parents=?",array($type,$value['id'],$item['id'])); ?>
											<option value="<?= $value['id'] ?>" <?=(!empty($activeFilter) && count($activeFilter)> 0) ? 'selected="selected"':'' ?> ><?= $value['namevi'] ?></option>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ((isset($config['product'][$type]['size']) && $config['product'][$type]['size'] == true) || (isset($config['product'][$type]['color']) && $config['product'][$type]['color'] == true)) { ?>
					<?php if (isset($config['product'][$type]['price_size_color']) && count($config['product'][$type]['price_size_color'])) {
						echo '';
					} else { ?>
						<div class="card card-primary card-outline text-sm">
							<div class="card-header">
								<h3 class="card-title">Kích thước, màu sắc: <?= $config['product'][$type]['title_main'] ?></h3>
								<div class="card-tools">
									<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
								</div>
							</div>
							<div class="card-body">
								<?php if (isset($config['product'][$type]['color']) && $config['product'][$type]['color'] == true) { ?>
									<div class="form-group">
										<label class="d-block" for="id_color">Danh mục màu sắc:</label>
										<?= $func->getColor(@$item['id']) ?>
									</div>
								<?php } ?>
								<?php if (isset($config['product'][$type]['size']) && $config['product'][$type]['size'] == true) { ?>
									<div class="form-group">
										<label class="d-block" for="id_size">Danh mục kích thước:</label>
										<?= $func->getSize(@$item['id']) ?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if (isset($config['product'][$type]['images']) && $config['product'][$type]['images'] == true) { ?>
					<div class="card card-primary card-outline text-sm">
						<div class="card-header">
							<h3 class="card-title">Hình ảnh <?= $config['product'][$type]['title_main'] ?></h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body">
							<?php
							/* Photo detail */
							$photoDetail = array();
							$photoDetail['upload'] = UPLOAD_PRODUCT_L;
							$photoDetail['image'] = (!empty($item) && $act != 'copy') ? $item['photo'] : '';
							$photoDetail['dimension'] = "Width: " . $config['product'][$type]['width'] . " px - Height: " . $config['product'][$type]['height'] . " px (" . $config['product'][$type]['img_type'] . ")";

							/* Image */
							include TEMPLATE . LAYOUT . "image.php";
							?>
						</div>
					</div>
				<?php } ?>
				<?php if (isset($config['product'][$type]['images2']) && $config['product'][$type]['images2'] == true) { ?>
					<div class="card card-primary card-outline text-sm">
						<div class="card-header">
							<h3 class="card-title">Hình ảnh 2 <?= $config['product'][$type]['title_main'] ?></h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body">
							<?php
							/* Photo detail */
							$photoDetail = array();
							$photoDetail['upload'] = UPLOAD_PRODUCT_L;
							$photoDetail['image2'] = (!empty($item) && $act != 'copy') ? $item['photo2'] : '';
							$photoDetail['dimension'] = "Width: " . $config['product'][$type]['width2'] . " px - Height: " . $config['product'][$type]['height2'] . " px (" . $config['product'][$type]['img_type'] . ")";

							/* Image */
							include TEMPLATE . LAYOUT . "image2.php";
							?>
						</div>
					</div>
				<?php } ?>
				<?php if (isset($config['product'][$type]['images3']) && $config['product'][$type]['images3'] == true) { ?>
					<div class="card card-primary card-outline text-sm">
						<div class="card-header">
							<h3 class="card-title">Hình ảnh 3 <?= $config['product'][$type]['title_main'] ?></h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body">
							<?php
							/* Photo detail */
							$photoDetail = array();
							$photoDetail['upload'] = UPLOAD_PRODUCT_L;
							$photoDetail['image3'] = (!empty($item) && $act != 'copy') ? $item['photo3'] : '';
							$photoDetail['dimension'] = "Width: " . $config['product'][$type]['width3'] . " px - Height: " . $config['product'][$type]['height3'] . " px (" . $config['product'][$type]['img_type'] . ")";

							/* Image */
							include TEMPLATE . LAYOUT . "image3.php";
							?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php if (isset($config['product'][$type]['price_size_color']) && count($config['product'][$type]['price_size_color']) > 0) { ?>
			<div class="card card-primary card-outline text-sm">
				<div class="card-header">
					<h3 class="card-title">Danh mục <?= implode(",", $config['product'][$type]['price_size_color']) ?>:</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					</div>
				</div>
				<div class="card-body">
					<div class="append-size-color">
						<?php $group_size_color = $d->rawQuery("SELECT * from table_product_size_color where id_product=? order by id", array($item['id']));
						if (count($group_size_color) > 0) { ?>
							<?php foreach ($group_size_color as $k => $v) { ?>
								<div class="group-size-color mb-3">
									<div class="row row-size-color">
										<?php if (in_array("size", $config['product'][$type]['price_size_color'])) { ?>
											<div class="form-group col-md-4">
												<label class="form-label">Danh mục kích thước:</label>
												<div class="form-custom">
													<?= $func->getSizeSC($v['id_size'], 'dataSC[' . ($k + 1) . '][size]', $type) ?>
												</div>
											</div>
										<?php } ?>
										<?php if (in_array("color", $config['product'][$type]['price_size_color'])) { ?>
											<div class="form-group col-md-4">
												<label class="form-label">Danh mục màu sắc:</label>
												<div class="form-custom">
													<?= $func->getColorSC($v['id_color'], 'dataSC[' . ($k + 1) . '][color]', $type) ?>
												</div>
											</div>
										<?php } ?>
										<div class="form-group col-md-4">
											<label class="form-label">Giá:</label>
											<div class="input-group">
												<input type="text" class="form-control format-price price-size-color" name="dataSC[<?= ($k + 1) ?>][price]" value="<?= $v['price'] ?>" placeholder="Giá">
												<div class="input-group-append">
													<div class="input-group-text"><strong>VNĐ</strong></div>
												</div>
											</div>
										</div>
									</div>
									<input type="hidden" name="dataSC[<?= ($k + 1) ?>][id]" value="<?= $v['id'] ?>">
									<?php /*
									<button type="button" class="btn btn-success save-size-color" data-id="<?= $item['id'] ?>"><i class="fas fa-save mr-2"></i>Lưu</button>
									*/ ?>
									<button type="button" class="btn btn-danger cancel-size-color" data-id="<?= $item['id'] ?>"><i class="fas fa-minus-circle mr-2"></i>Xóa</button>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<div class="card-footer">
					<button type="button" class="btn btn-primary add-size-color" data-type="<?= $type ?>" data-target="<?= implode(',', $config['product'][$type]['price_size_color']) ?>" data-id="<?= $item['id'] ?>"><i class="fas fa-plus-circle mr-2"></i> Thêm</button>
				</div>
			</div>
		<?php } ?>
		<div class="card card-primary card-outline text-sm">
			<div class="card-header">
				<h3 class="card-title">Thông tin <?= $config['product'][$type]['title_main'] ?></h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="form-group">
					<?php $status_array = (!empty($item['status'])) ? explode(',', $item['status']) : array(); ?>
					<?php if (isset($config['product'][$type]['check'])) {
						foreach ($config['product'][$type]['check'] as $key => $value) { ?>
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
				<div class="form-group">
					<label for="numb" class="d-inline-block align-middle mb-0 mr-2">Số thứ tự:</label>
					<input type="number" class="form-control form-control-mini d-inline-block align-middle text-sm" min="0" name="data[numb]" id="numb" placeholder="Số thứ tự" value="<?= isset($item['numb']) ? $item['numb'] : 1 ?>">
				</div>
				<div class="row">
					<?php if (isset($config['product'][$type]['code']) && $config['product'][$type]['code'] == true) { ?>
						<div class="form-group col-md-4">
							<label class="d-block" for="code">Mã sản phẩm:</label>
							<input type="text" class="form-control text-sm" name="data[code]" id="code" placeholder="Mã sản phẩm" value="<?= (!empty($flash->has('code'))) ? $flash->get('code') : @$item['code'] ?>">
						</div>
					<?php } ?>
					<?php if (isset($config['product'][$type]['regular_price']) && $config['product'][$type]['regular_price'] == true) { ?>
						<div class="form-group col-md-4">
							<label class="d-block" for="regular_price">Giá bán:</label>
							<div class="input-group">
								<input type="text" class="form-control format-price regular_price text-sm" name="data[regular_price]" id="regular_price" placeholder="Giá bán" value="<?= (!empty($flash->has('regular_price'))) ? $flash->get('regular_price') : @$item['regular_price'] ?>">
								<div class="input-group-append">
									<div class="input-group-text"><strong>VNĐ</strong></div>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($config['product'][$type]['sale_price']) && $config['product'][$type]['sale_price'] == true) { ?>
						<div class="form-group col-md-4">
							<label class="d-block" for="sale_price">Giá mới:</label>
							<div class="input-group">
								<input type="text" class="form-control format-price sale_price text-sm" name="data[sale_price]" id="sale_price" placeholder="Giá mới" value="<?= (!empty($flash->has('sale_price'))) ? $flash->get('sale_price') : @$item['sale_price'] ?>">
								<div class="input-group-append">
									<div class="input-group-text"><strong>VNĐ</strong></div>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($config['product'][$type]['discount']) && $config['product'][$type]['discount'] == true) { ?>
						<div class="form-group col-md-4">
							<label class="d-block" for="discount">Chiết khấu:</label>
							<div class="input-group">
								<input type="text" class="form-control discount text-sm" name="data[discount]" id="discount" placeholder="Chiết khấu" value="<?= (!empty($flash->has('discount'))) ? $flash->get('discount') : @$item['discount'] ?>" maxlength="3" readonly>
								<div class="input-group-append">
									<div class="input-group-text"><strong>%</strong></div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php if (isset($flagGallery) && $flagGallery == true) { ?>
			<div class="card card-primary card-outline text-sm">
				<div class="card-header">
					<h3 class="card-title">Bộ sưu tập <?= $config['product'][$type]['title_main'] ?></h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					</div>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="filer-gallery" class="label-filer-gallery mb-3">Album hình: (<?= $config['product'][$type]['gallery'][$keyGallery]['img_type_photo'] ?>)</label>
						<input type="file" name="files[]" id="filer-gallery" multiple="multiple">
						<input type="hidden" class="col-filer" value="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-6">
						<input type="hidden" class="act-filer" value="man">
						<input type="hidden" class="folder-filer" value="product">
					</div>
					<?php if (isset($gallery) && count($gallery) > 0) { ?>
						<div class="form-group form-group-gallery">
							<label class="label-filer">Album hiện tại:</label>
							<div class="action-filer mb-3">
								<a class="btn btn-sm bg-gradient-primary text-white check-all-filer mr-1"><i class="far fa-square mr-2"></i>Chọn tất cả</a>
								<button type="button" class="btn btn-sm bg-gradient-success text-white sort-filer mr-1"><i class="fas fa-random mr-2"></i>Sắp xếp</button>
								<a class="btn btn-sm bg-gradient-danger text-white delete-all-filer"><i class="far fa-trash-alt mr-2"></i>Xóa tất cả</a>
							</div>
							<div class="alert my-alert alert-sort-filer alert-info text-sm text-white bg-gradient-info"><i class="fas fa-info-circle mr-2"></i>Có thể chọn nhiều hình để di chuyển</div>
							<div class="jFiler-items my-jFiler-items jFiler-row">
								<ul class="jFiler-items-list jFiler-items-grid row scroll-bar" id="jFilerSortable">
									<?php foreach ($gallery as $v) echo $func->galleryFiler($v['numb'], $v['id'], $v['photo'], $v['namevi'], 'product', 'col-xl-2 col-lg-3 col-md-3 col-sm-4 col-6'); ?>
								</ul>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if (isset($config['product'][$type]['seo']) && $config['product'][$type]['seo'] == true) { ?>
			<div class="card card-primary card-outline text-sm">
				<div class="card-header">
					<h3 class="card-title">Nội dung SEO</h3>
					<a class="btn btn-sm bg-gradient-success d-inline-block text-white float-right create-seo" title="Tạo SEO">Tạo SEO</a>
				</div>
				<div class="card-body">
					<?php
					$seoDB = $seo->getOnDB($id, $com, 'man', $type);
					include TEMPLATE . LAYOUT . "seo.php";
					?>
				</div>
			</div>
		<?php } ?>
		<?php if (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true) { ?>
			<div class="card card-primary card-outline text-sm">
				<div class="card-header">
					<h3 class="card-title">Schema JSON Product <a href="https://developers.google.com/search/docs/advanced/structured-data/search-gallery" target="_blank">(Tài liệu tham khảo)</a></h3>
					<button type="submit" class="btn btn-sm bg-gradient-success float-right submit-check" name="build-schema"><i class="far fa-save mr-2"></i>Lưu và tạo tự động Schema</button>
				</div>
				<div class="card-body">
					<?php
					$seoDB = $seo->getOnDB($id, $com, 'man', $type);
					include TEMPLATE . LAYOUT . "schema.php";
					?>
					<input type="hidden" id="schema-type" value="product">
				</div>
			</div>
		<?php } ?>
		<div class="card-footer text-sm">
			<button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i>Lưu</button>
			<button type="submit" class="btn btn-sm bg-gradient-success submit-check" name="save-here" disabled><i class="far fa-save mr-2"></i>Lưu tại trang</button>
			<button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i>Làm lại</button>
			<a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="Thoát"><i class="fas fa-sign-out-alt mr-2"></i>Thoát</a>
			<input type="hidden" name="id" value="<?= (isset($item['id']) && $item['id'] > 0) ? $item['id'] : '' ?>">
		</div>
	</form>
</section>