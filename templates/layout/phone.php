<?php if ($config['cart']['active'] == true) { ?>
    <a class="cart-fixed text-decoration-none" href="gio-hang" title="Giỏ hàng">
        <i class="fas fa-shopping-bag"></i>
        <span class="count-cart"><?= (!empty($_SESSION['cart'])) ? count($_SESSION['cart']) : 0 ?></span>
        <div class="animated infinite zoomIn kenit-alo-circle"></div>
        <div class="animated infinite pulse kenit-alo-circle-fill"></div>
    </a>
<?php } ?>
<div class="social_fixed">
    <div class="support-online">
        <div class="support-content vibration-icon">
            <a href="tel:<?php echo $optsetting['hotline'] ?>" class="call-now" rel="nofollow" target="_blank">
                <i class="fa fa-fas fa-phone"></i>
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
            </a>
        </div>
    </div>
    <div class="support-online">
        <div class="support-content vibration-icon">
            <a href="https://zalo.me/<?php echo preg_replace('/[^0-9]/', '', $optsetting['zalo']); ?>" class="call-now" rel="nofollow" target="_blank">
                <img src="assets/images/zalo_icon_03.png">
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
            </a>
        </div>
    </div>
    <div class="support-online">
        <div class="support-content vibration-icon">
            <a href="<?php echo $optsetting['fanpage'] ?>" class="call-now" rel="nofollow" target="_blank">
                <i class="fab fa-facebook-messenger"></i>
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
            </a>
        </div>
    </div>
    <div class="support-online">
        <div class="support-content vibration-icon">
            <a href="<?php echo $optsetting['coords'] ?>" class="call-now" rel="nofollow" target="_blank">
                <i class="fa fa-map-marker-alt"></i>
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
            </a>
        </div>
    </div>
</div>
<div class="social_fixed-phone">
    <ul>
        <li>
            <a class="blink_me" href="tel:<?php echo preg_replace('/[^0-9]/', '', $optsetting['hotline']) ?>">
                <i class="fa fa-phone"></i>
                <span class="mt-1">Hotline</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $optsetting['fanpage'] ?>">
                <i class="fab fa-facebook-messenger"></i>
                <span class="mt-1">Messenger</span>
            </a>
        </li>
        <li>
            <a href="https://zalo.me/<?php echo preg_replace('/[^0-9]/', '', $optsetting['zalo']) ?>" target="_blank">
                <img src="assets/images/zalo3.png" alt="Zalo" style="filter: brightness(100);">
                <span class="mt-1">Zalo</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $optsetting['coords'] ?>" target="_blank">
                <i class="fas fa-map-marker-alt"></i>
                <span class="mt-1">Chỉ đường</span>
            </a>
        </li>
    </ul>
</div>