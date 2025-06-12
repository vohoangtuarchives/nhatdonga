<?php
include "config.php";

$cmd = (!empty($_POST['cmd'])) ? htmlspecialchars($_POST['cmd']) : '';
$id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
$color = (!empty($_POST['color'])) ? htmlspecialchars($_POST['color']) : 0;
$size = (!empty($_POST['size'])) ? htmlspecialchars($_POST['size']) : 0;
$quantity = (!empty($_POST['quantity'])) ? htmlspecialchars($_POST['quantity']) : 1;
$code = (!empty($_POST['code'])) ? htmlspecialchars($_POST['code']) : '';
$ward = (!empty($_POST['ward'])) ? htmlspecialchars($_POST['ward']) : 0;

if ($cmd == 'add-cart' && $id > 0) {
	$cart->addToCart($quantity, $id, $color, $size);
	$max = (!empty($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
	$data = array('max' => $max);

	echo json_encode($data);
} else if ($cmd == 'update-cart' && $id > 0 && $code != '') {
	if (!empty($_SESSION['cart'])) {
		$max = count($_SESSION['cart']);
		for ($i = 0; $i < $max; $i++) {
			if ($code == $_SESSION['cart'][$i]['code']) {
				if ($quantity) {
					$size = $_SESSION['cart'][$i]['size'];
					$color = $_SESSION['cart'][$i]['color'];
					$_SESSION['cart'][$i]['qty'] = $quantity;
				}
				break;
			}
		}
	}

	if (!empty($config['order']['ship'])) {
		$shipData = (!empty($ward)) ? $func->getInfoDetail('ship_price', "ward", $ward) : array();
		$ship = (!empty($shipData)) ? $shipData['ship_price'] : 0;
	}

	$proinfo = $cart->getProductInfo($id);
	$price = $cart->getPriceSC($id, $size, $color);
	$price = $func->formatMoney($price * $quantity);
	$regular_price = $func->formatMoney($proinfo['regular_price'] * $quantity);
	$sale_price = $func->formatMoney($proinfo['sale_price'] * $quantity);
	$temp = $cart->getOrderTotal();
	$tempText = $func->formatMoney($temp);
	$total = $cart->getOrderTotal();
	if (!empty($ship)) $total += $ship;
	$totalText = $func->formatMoney($total);
	$data = array('regularPrice' => $regular_price, 'salePrice' => $sale_price, 'tempText' => $tempText, 'totalText' => $totalText, 'price' => $price);

	echo json_encode($data);
} else if ($cmd == 'delete-cart' && $code != '') {
	if (!empty($config['order']['ship'])) {
		$shipData = (!empty($ward)) ? $func->getInfoDetail('ship_price', "ward", $ward) : array();
		$ship = (!empty($shipData)) ? $shipData['ship_price'] : 0;
	}

	$cart->removeProduct($code);
	$max = (!empty($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
	$temp = $cart->getOrderTotal();
	$tempText = $func->formatMoney($temp);
	$total = $cart->getOrderTotal();
	if (!empty($ship)) $total += $ship;
	$totalText = $func->formatMoney($total);
	$data = array('max' => $max, 'tempText' => $tempText, 'totalText' => $totalText);

	echo json_encode($data);
} else if ($cmd == 'ship-cart') {
	$shipData = array();
	$shipPrice = 0;
	$shipText = '0đ';
	$total = 0;
	$totalText = '';

	if ($id) $shipData = $func->getInfoDetail('ship_price', "ward", $id);

	$total = $cart->getOrderTotal();
	if (!empty($shipData['ship_price'])) {
		$total += $shipData['ship_price'];
		$shipText = $func->formatMoney($shipData['ship_price']);
	}
	$totalText = $func->formatMoney($total);
	$shipPrice = (!empty($shipData['ship_price'])) ? $shipData['ship_price'] : 0;
	$data = array('shipText' => $shipText, 'ship' => $shipPrice, 'totalText' => $totalText);

	echo json_encode($data);
} else if ($cmd == 'popup-cart') { ?>
	<form class="form-cart" method="post" action="" enctype="multipart/form-data">
		<div class="wrap-cart">
			<div class="top-cart border-right-0">
				<div class="list-procart">
					<div class="procart procart-label">
						<div class="form-row">
							<div class="pic-procart col-3 col-md-2"><?= hinhanh ?></div>
							<div class="info-procart col-6 col-md-5"><?= tensanpham ?></div>
							<div class="quantity-procart col-3 col-md-2">
								<p><?= soluong ?></p>
								<p><?= thanhtien ?></p>
							</div>
							<div class="price-procart col-3 col-md-3"><?= thanhtien ?></div>
						</div>
					</div>
					<?php $max = count($_SESSION['cart']);
					for ($i = 0; $i < $max; $i++) {
						$pid = $_SESSION['cart'][$i]['productid'];
						$quantity = $_SESSION['cart'][$i]['qty'];
						$color = ($_SESSION['cart'][$i]['color']) ? $_SESSION['cart'][$i]['color'] : 0;
						$size = ($_SESSION['cart'][$i]['size']) ? $_SESSION['cart'][$i]['size'] : 0;
						$code = ($_SESSION['cart'][$i]['code']) ? $_SESSION['cart'][$i]['code'] : '';
						$proinfo = $cart->getProductInfo($pid);
						$price = $cart->getPriceSC($pid, $size, $color);
						$price_qty = $price * $quantity;
						$pro_price = $proinfo['regular_price'];
						$pro_price_new = $proinfo['sale_price'];
						$pro_price_qty = $pro_price * $quantity;
						$pro_price_new_qty = $pro_price_new * $quantity; ?>
						<div class="procart procart-<?= $code ?>">
							<div class="form-row">
								<div class="pic-procart col-3 col-md-2">
									<a class="text-decoration-none" href="<?= $proinfo[$sluglang] ?>" target="_blank" title="<?= $proinfo['name' . $lang] ?>"><?= $func->getImage(['sizes' => '85x85x2', 'upload' => UPLOAD_PRODUCT_L, 'image' => $proinfo['photo'], 'alt' => $proinfo['name' . $lang]]) ?></a>
									<a class="del-procart text-decoration-none" data-code="<?= $code ?>">
										<i class="fa fa-times-circle"></i>
										<span><?= xoa ?></span>
									</a>
								</div>
								<div class="info-procart col-6 col-md-5">
									<h3 class="name-procart"><a class="text-decoration-none" href="<?= $proinfo[$sluglang] ?>" target="_blank" title="<?= $proinfo['name' . $lang] ?>"><?= $proinfo['name' . $lang] ?></a></h3>
									<div class="properties-procart">
										<?php if ($color) {
											$color_detail = $d->rawQueryOne("select name$lang from #_color where type = ? and id = ? limit 0,1", array($proinfo['type'], $color)); ?>
											<p>Màu: <strong><?= $color_detail['name' . $lang] ?></strong></p>
										<?php } ?>
										<?php if ($size) {
											$size_detail = $d->rawQueryOne("select name$lang from #_size where type = ? and id = ? limit 0,1", array($proinfo['type'], $size)); ?>
											<p>Size: <strong><?= $size_detail['name' . $lang] ?></strong></p>
										<?php } ?>
									</div>
								</div>
								<div class="quantity-procart col-3 col-md-2">
									<div class="price-procart price-procart-rp">
										<?php if ($price) {
											echo '<p class="price-cart load-prices-' . $code . '">' . $func->formatMoney($price_qty) . '</p>';
										} else { ?>
											<?php if ($proinfo['sale_price']) { ?>
												<p class="price-new-cart load-price-new-<?= $code ?>">
													<?= $func->formatMoney($pro_price_new_qty) ?>
												</p>
												<p class="price-old-cart load-price-<?= $code ?>">
													<?= $func->formatMoney($pro_price_qty) ?>
												</p>
											<?php } else { ?>
												<p class="price-new-cart load-price-<?= $code ?>">
													<?= $func->formatMoney($pro_price_qty) ?>
												</p>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="quantity-counter-procart quantity-counter-procart-<?= $code ?> d-flex align-items-stretch justify-content-between">
										<span class="counter-procart-minus counter-procart">-</span>
										<input type="number" class="quantity-procart" min="1" value="<?= $quantity ?>" data-pid="<?= $pid ?>" data-code="<?= $code ?>" />
										<span class="counter-procart-plus counter-procart">+</span>
									</div>
								</div>
								<div class="price-procart col-3 col-md-3">
									<?php if ($price) {
										echo '<p class="price-cart load-prices-' . $code . '">' . $func->formatMoney($price_qty) . '</p>';
									} else { ?>
										<?php if ($proinfo['sale_price']) { ?>
											<p class="price-new-cart load-price-new-<?= $code ?>">
												<?= $func->formatMoney($pro_price_new_qty) ?>
											</p>
											<p class="price-old-cart load-price-<?= $code ?>">
												<?= $func->formatMoney($pro_price_qty) ?>
											</p>
										<?php } else { ?>
											<p class="price-new-cart load-price-<?= $code ?>">
												<?= $func->formatMoney($pro_price_qty) ?>
											</p>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="money-procart">
					<div class="total-procart d-flex align-items-center justify-content-between">
						<p><?= tamtinh ?>:</p>
						<p class="total-price load-price-temp"><?= $func->formatMoney($cart->getOrderTotal()) ?></p>
					</div>
				</div>
				<div class="modal-footer d-flex align-items-center justify-content-between">
					<a href="san-pham" class="buymore-cart text-decoration-none" title="<?= tieptucmuahang ?>">
						<i class="fa fa-angle-double-left"></i>
						<span><?= tieptucmuahang ?></span>
					</a>
					<a class="btn-cart btn btn-primary" href="gio-hang" title="<?= thanhtoan ?>"><?= thanhtoan ?></a>
				</div>
			</div>
		</div>
	</form>
<?php } elseif ($cmd = 'get-price') {
	$price = $cart->getPriceSC($id, $size, $color);
	$price = $func->formatMoney($price);
	if (!$price) {
		$price = 'Liên hệ';
	}
	echo '<span class="price-new-pro-detail">' . $price . '</span>';
}
?>