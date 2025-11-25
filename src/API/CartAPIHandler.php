<?php

namespace Tuezy\API;

/**
 * CartAPIHandler - Handles cart API requests
 * Refactors api/cart.php to use OOP pattern
 */
class CartAPIHandler extends APIHandler
{
    private $cart;

    public function __construct($d, $cache, $func, $custom, $config, $lang, $sluglang, $setting, $cart)
    {
        parent::__construct($d, $cache, $func, $custom, $config, $lang, $sluglang, $setting);
        $this->cart = $cart;
    }

    /**
     * Handle cart API request
     */
    public function handle(): void
    {
        $cmd = $this->post('cmd');
        $id = (int)$this->post('id', 0);
        $color = (int)$this->post('color', 0);
        $size = (int)$this->post('size', 0);
        $quantity = (int)$this->post('quantity', 1);
        $code = $this->post('code');
        $ward = (int)$this->post('ward', 0);

        switch ($cmd) {
            case 'add-cart':
                $this->handleAddCart($id, $quantity, $color, $size);
                break;

            case 'update-cart':
                $this->handleUpdateCart($id, $code, $quantity, $ward);
                break;

            case 'delete-cart':
                $this->handleDeleteCart($code, $ward);
                break;

            case 'ship-cart':
                $this->handleShipCart($id);
                break;

            case 'popup-cart':
                $this->handlePopupCart();
                break;

            case 'get-price':
                $this->handleGetPrice($id, $size, $color);
                break;

            default:
                $this->error('Invalid command');
        }
    }

    /**
     * Handle add to cart
     */
    private function handleAddCart(int $id, int $quantity, int $color, int $size): void
    {
        if ($id <= 0) {
            $this->error('Invalid product ID');
            return;
        }

        $this->cart->addToCart($quantity, $id, $color, $size);
        $max = !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

        $this->success(['max' => $max]);
    }

    /**
     * Handle update cart
     */
    private function handleUpdateCart(int $id, string $code, int $quantity, int $ward): void
    {
        if ($id <= 0 || empty($code)) {
            $this->error('Invalid parameters');
            return;
        }

        // Update cart item
        if (!empty($_SESSION['cart'])) {
            $max = count($_SESSION['cart']);
            for ($i = 0; $i < $max; $i++) {
                if ($code === $_SESSION['cart'][$i]['code']) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$i]['qty'] = $quantity;
                    }
                    break;
                }
            }
        }

        // Calculate shipping
        $ship = $this->calculateShipping($ward);

        // Get product info and prices
        $proinfo = $this->cart->getProductInfo($id);
        $price = $this->cart->getPriceSC($id, $size, $color);
        $priceFormatted = $this->func->formatMoney($price * $quantity);
        $regularPriceFormatted = $this->func->formatMoney($proinfo['regular_price'] * $quantity);
        $salePriceFormatted = $this->func->formatMoney($proinfo['sale_price'] * $quantity);

        // Calculate totals
        $temp = $this->cart->getOrderTotal();
        $tempText = $this->func->formatMoney($temp);
        $total = $temp + $ship;
        $totalText = $this->func->formatMoney($total);

        $this->success([
            'regularPrice' => $regularPriceFormatted,
            'salePrice' => $salePriceFormatted,
            'tempText' => $tempText,
            'totalText' => $totalText,
            'price' => $priceFormatted
        ]);
    }

    /**
     * Handle delete from cart
     */
    private function handleDeleteCart(string $code, int $ward): void
    {
        if (empty($code)) {
            $this->error('Invalid code');
            return;
        }

        $this->cart->removeProduct($code);
        $max = !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

        // Calculate shipping
        $ship = $this->calculateShipping($ward);

        // Calculate totals
        $temp = $this->cart->getOrderTotal();
        $tempText = $this->func->formatMoney($temp);
        $total = $temp + $ship;
        $totalText = $this->func->formatMoney($total);

        $this->success([
            'max' => $max,
            'tempText' => $tempText,
            'totalText' => $totalText
        ]);
    }

    /**
     * Handle shipping calculation
     */
    private function handleShipCart(int $wardId): void
    {
        $shipData = [];
        $shipPrice = 0;
        $shipText = '0đ';
        $total = 0;

        if ($wardId > 0) {
            $shipData = $this->func->getInfoDetail('ship_price', "ward", $wardId);
        }

        $total = $this->cart->getOrderTotal();

        if (!empty($shipData['ship_price'])) {
            $total += $shipData['ship_price'];
            $shipText = $this->func->formatMoney($shipData['ship_price']);
            $shipPrice = $shipData['ship_price'];
        }

        $totalText = $this->func->formatMoney($total);

        $this->success([
            'shipText' => $shipText,
            'ship' => $shipPrice,
            'totalText' => $totalText
        ]);
    }

    /**
     * Handle popup cart (returns HTML)
     */
    private function handlePopupCart(): void
    {
        // This returns HTML, not JSON
        // Keep original implementation but can be refactored to use template
        include __DIR__ . '/../../templates/components/cart_popup.php';
    }

    /**
     * Handle get price
     */
    private function handleGetPrice(int $id, int $size, int $color): void
    {
        $price = $this->cart->getPriceSC($id, $size, $color);
        $priceFormatted = $price ? $this->func->formatMoney($price) : 'Liên hệ';

        echo '<span class="price-new-pro-detail">' . $priceFormatted . '</span>';
        exit;
    }

    /**
     * Calculate shipping cost
     * 
     * @param int $ward Ward ID
     * @return float Shipping cost
     */
    private function calculateShipping(int $ward): float
    {
        if (!$this->config->get('order.ship', false)) {
            return 0;
        }

        if ($ward <= 0) {
            return 0;
        }

        $shipData = $this->func->getInfoDetail('ship_price', "ward", $ward);
        return !empty($shipData['ship_price']) ? (float)$shipData['ship_price'] : 0;
    }
}

