<?php if (!empty($productItems)) { ?>
	<div class="row row-product">
		<?php foreach ($productItems as $item) {
			echo $custom->products($item);
		} ?>
	</div>
	<?php if (!empty($paginationHtml)) { ?>
		<div class="pagination-ajax"><?= $paginationHtml ?></div>
	<?php } ?>
<?php } ?>

