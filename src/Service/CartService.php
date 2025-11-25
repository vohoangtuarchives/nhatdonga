<?php

namespace Tuezy\Service;

/**
 * CartService - Shopping cart management
 * Refactored from class.Cart.php
 * 
 * Handles cart operations: add, remove, calculate totals
 */
class CartService
{
    private $db;
    private const CART_SESSION_KEY = 'cart';

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get product information
     * 
     * @param int $productId Product ID
     * @return array|null Product data or null
     */
    public function getProductInfo(int $productId): ?array
    {
        if ($productId <= 0) {
            return null;
        }

        return $this->db->rawQueryOne(
            "SELECT * FROM #_product WHERE id = ? LIMIT 0,1",
            [$productId]
        );
    }

    /**
     * Get product color name
     * 
     * @param int $colorId Color ID
     * @return string Color name
     */
    public function getProductColor(int $colorId): string
    {
        if ($colorId <= 0) {
            return '';
        }

        $row = $this->db->rawQueryOne(
            "SELECT namevi FROM #_color WHERE id = ? LIMIT 0,1",
            [$colorId]
        );

        return $row['namevi'] ?? '';
    }

    /**
     * Get product size name
     * 
     * @param int $sizeId Size ID
     * @return string Size name
     */
    public function getProductSize(int $sizeId): string
    {
        if ($sizeId <= 0) {
            return '';
        }

        $row = $this->db->rawQueryOne(
            "SELECT namevi FROM #_size WHERE id = ? LIMIT 0,1",
            [$sizeId]
        );

        return $row['namevi'] ?? '';
    }

    /**
     * Remove product from cart
     * 
     * @param string $codeOrder Order code (MD5 hash of product+color+size)
     */
    public function removeProduct(string $codeOrder): void
    {
        if (empty($_SESSION[self::CART_SESSION_KEY]) || empty($codeOrder)) {
            return;
        }

        $cart = $_SESSION[self::CART_SESSION_KEY];
        $max = count($cart);

        for ($i = 0; $i < $max; $i++) {
            if ($codeOrder === $cart[$i]['code']) {
                unset($cart[$i]);
                break;
            }
        }

        $_SESSION[self::CART_SESSION_KEY] = array_values($cart);
    }

    /**
     * Get price from size/color combination
     * 
     * @param int $productId Product ID
     * @param int $sizeId Size ID
     * @param int $colorId Color ID
     * @return int Price
     */
    public function getPriceSC(int $productId, int $sizeId = 0, int $colorId = 0): int
    {
        if ($productId <= 0) {
            return 0;
        }

        $rowSC = $this->db->rawQueryOne(
            "SELECT * FROM table_product_size_color 
             WHERE id_product = ? AND id_color = ? AND id_size = ? 
             LIMIT 0,1",
            [$productId, $colorId, $sizeId]
        );

        return (int)($rowSC['price'] ?? 0);
    }

    /**
     * Get product price (from size/color or default)
     * 
     * @param int $productId Product ID
     * @param int $sizeId Size ID
     * @param int $colorId Color ID
     * @return int Price
     */
    public function getPrice(int $productId, int $sizeId = 0, int $colorId = 0): int
    {
        if ($productId <= 0) {
            return 0;
        }

        // Try to get price from size/color combination
        $rowSC = $this->db->rawQueryOne(
            "SELECT * FROM table_product_size_color 
             WHERE id_product = ? AND id_color = ? AND id_size = ? 
             LIMIT 0,1",
            [$productId, $colorId, $sizeId]
        );

        if ($rowSC && !empty($rowSC['price'])) {
            return (int)$rowSC['price'];
        }

        // Fallback to product default price
        $product = $this->getProductInfo($productId);
        if (!$product) {
            return 0;
        }

        if (!empty($product['sale_price'])) {
            return (int)$product['sale_price'];
        }

        return (int)($product['regular_price'] ?? 0);
    }

    /**
     * Calculate order total
     * 
     * @return int Total amount
     */
    public function getOrderTotal(): int
    {
        $sum = 0;

        if (empty($_SESSION[self::CART_SESSION_KEY])) {
            return $sum;
        }

        $cart = $_SESSION[self::CART_SESSION_KEY];
        $max = count($cart);

        for ($i = 0; $i < $max; $i++) {
            $productId = (int)$cart[$i]['productid'];
            $qty = (int)$cart[$i]['qty'];
            $size = (int)$cart[$i]['size'];
            $color = (int)$cart[$i]['color'];

            $price = $this->getPrice($productId, $size, $color);
            $sum += ($price * $qty);
        }

        return $sum;
    }

    /**
     * Add product to cart
     * 
     * @param int $quantity Quantity
     * @param int $productId Product ID
     * @param int $colorId Color ID
     * @param int $sizeId Size ID
     */
    public function addToCart(int $quantity = 1, int $productId = 0, int $colorId = 0, int $sizeId = 0): void
    {
        if ($productId < 1 || $quantity < 1) {
            return;
        }

        $codeOrder = md5($productId . $colorId . $sizeId);

        if (!empty($_SESSION[self::CART_SESSION_KEY])) {
            if (!$this->productExists($codeOrder, $quantity)) {
                $cart = $_SESSION[self::CART_SESSION_KEY];
                $max = count($cart);
                
                $cart[$max] = [
                    'productid' => $productId,
                    'qty' => $quantity,
                    'color' => $colorId,
                    'size' => $sizeId,
                    'code' => $codeOrder,
                ];
                
                $_SESSION[self::CART_SESSION_KEY] = $cart;
            }
        } else {
            $_SESSION[self::CART_SESSION_KEY] = [
                [
                    'productid' => $productId,
                    'qty' => $quantity,
                    'color' => $colorId,
                    'size' => $sizeId,
                    'code' => $codeOrder,
                ]
            ];
        }
    }

    /**
     * Check if product exists in cart and update quantity
     * 
     * @param string $codeOrder Order code
     * @param int $quantity Quantity to add
     * @return bool True if product exists
     */
    private function productExists(string $codeOrder, int $quantity): bool
    {
        if (empty($_SESSION[self::CART_SESSION_KEY]) || empty($codeOrder)) {
            return false;
        }

        $quantity = max(1, $quantity);
        $cart = $_SESSION[self::CART_SESSION_KEY];
        $max = count($cart);

        for ($i = 0; $i < $max; $i++) {
            if ($codeOrder === $cart[$i]['code']) {
                $cart[$i]['qty'] += $quantity;
                $_SESSION[self::CART_SESSION_KEY] = $cart;
                return true;
            }
        }

        return false;
    }

    /**
     * Get cart items
     * 
     * @return array Cart items
     */
    public function getCartItems(): array
    {
        return $_SESSION[self::CART_SESSION_KEY] ?? [];
    }

    /**
     * Clear cart
     */
    public function clearCart(): void
    {
        unset($_SESSION[self::CART_SESSION_KEY]);
    }

    /**
     * Get cart count
     * 
     * @return int Number of items in cart
     */
    public function getCartCount(): int
    {
        return count($_SESSION[self::CART_SESSION_KEY] ?? []);
    }
}

