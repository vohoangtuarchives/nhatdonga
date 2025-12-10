<?php
namespace Tuezy\Libraries;

class Cart
{
    private $d;

    function __construct($d)
    {
        $this->d = $d;
    }

    public function getProductInfo($pid = 0)
    {
        $row = null;
        if ($pid) {
            $row = $this->d->rawQueryOne("select * from #_product where id = ? limit 0,1", array($pid));
        }
        return $row;
    }

    public function getProductColor($color = 0)
    {
        $str = '';
        if ($color) {
            $row = $this->d->rawQueryOne("select namevi from #_color where id = ? limit 0,1", array($color));
            $str = $row['namevi'];
        }
        return $str;
    }

    public function getProductSize($size = 0)
    {
        $str = '';
        if ($size) {
            $row = $this->d->rawQueryOne("select namevi from #_size where id = ? limit 0,1", array($size));
            $str = $row['namevi'];
        }
        return $str;
    }

    public function removeProduct($code_order = '')
    {
        if (!empty($_SESSION['cart']) && $code_order != '') {
            $max = count($_SESSION['cart']);
            for ($i = 0; $i < $max; $i++) {
                if ($code_order == $_SESSION['cart'][$i]['code']) {
                    unset($_SESSION['cart'][$i]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }

    public function getPriceSC($pid = 0, $size = 0, $color = 0){
        $price = null;
        if ($pid) {
            $rowSC = $this->d->rawQueryOne("select * from table_product_size_color where id_product = ? and id_color=? and id_size=? limit 0,1", array($pid, $color, $size));
            $price = $rowSC['price'];
        }
        return (int)$price;
    }

    public function getPrice($pid = 0, $size = 0, $color = 0)
    {
        $price = null;
        if ($pid) {
            $rowSC = $this->d->rawQueryOne("select * from table_product_size_color where id_product = ? and id_color=? and id_size=? limit 0,1", array($pid, $color, $size));
            if ($rowSC) {
                $price = $rowSC['price'];
            } else {
                $row = $this->d->rawQueryOne("select * from #_product where id = ? limit 0,1", array($pid));
                if ($row['sale_price']) {
                    $price = $row['sale_price'];
                } else {
                    $price = $row['regular_price'];
                }
            }
        }
        return (int)$price;
    }

    public function getOrderTotal()
    {
        $sum = 0;
        if (!empty($_SESSION['cart'])) {
            $max = count($_SESSION['cart']);
            for ($i = 0; $i < $max; $i++) {
                $pid = $_SESSION['cart'][$i]['productid'];
                $q = $_SESSION['cart'][$i]['qty'];
                $size = $_SESSION['cart'][$i]['size'];
                $color = $_SESSION['cart'][$i]['color'];
                $proinfo = $this->getProductInfo($pid);
                $price = $this->getPrice($pid, $size, $color);
                $sum += ($price * $q);
            }
        }
        return $sum;
    }

    public function addToCart($q = 1, $pid = 0, $color = 0, $size = 0)
    {
        if ($pid < 1 or $q < 1) return;
        $code_order = md5($pid . $color . $size);
        if (!empty($_SESSION['cart'])) {
            if (!$this->productExists($code_order, $q)) {
                $max = count($_SESSION['cart']);
                $_SESSION['cart'][$max]['productid'] = $pid;
                $_SESSION['cart'][$max]['qty'] = $q;
                $_SESSION['cart'][$max]['color'] = $color;
                $_SESSION['cart'][$max]['size'] = $size;
                $_SESSION['cart'][$max]['code'] = $code_order;
            }
        } else {
            $_SESSION['cart'] = array();
            $_SESSION['cart'][0]['productid'] = $pid;
            $_SESSION['cart'][0]['qty'] = $q;
            $_SESSION['cart'][0]['color'] = $color;
            $_SESSION['cart'][0]['size'] = $size;
            $_SESSION['cart'][0]['code'] = $code_order;
        }
    }

    private function productExists($code_order = '', $q = 1)
    {
        $flag = 0;
        if (!empty($_SESSION['cart']) && $code_order != '') {
            $q = ($q > 1) ? $q : 1;
            $max = count($_SESSION['cart']);
            for ($i = 0; $i < $max; $i++) {
                if ($code_order == $_SESSION['cart'][$i]['code']) {
                    $_SESSION['cart'][$i]['qty'] += $q;
                    $flag = 1;
                }
            }
        }
        return $flag;
    }
}

