<?php

namespace Tuezy;

use Tuezy\Repository\OrderRepository;
use Tuezy\Service\OrderService;

/**
 * OrderHandler - Handles order processing
 * Centralizes order creation, validation, and email sending
 */
class OrderHandler
{
    private $d;
    private $func;
    private $cart;
    private $emailer;
    private $flash;
    private ValidationHelper $validator;
    private OrderRepository $orderRepo;
    private OrderService $orderService;
    private string $configBase;
    private string $lang;
    private array $setting;
    private array $config;

    public function __construct($d, $func, $cart, $emailer, $flash, ValidationHelper $validator, OrderRepository $orderRepo, string $configBase, string $lang, array $setting, array $config)
    {
        $this->d = $d;
        $this->func = $func;
        $this->cart = $cart;
        $this->emailer = $emailer;
        $this->flash = $flash;
        $this->validator = $validator;
        $this->orderRepo = $orderRepo;
        $this->orderService = new OrderService($orderRepo, $d);
        $this->configBase = $configBase;
        $this->lang = $lang;
        $this->setting = $setting;
        $this->config = $config;
    }

    /**
     * Handle order submission
     * 
     * @param array $dataOrder Order data
     * @return bool Success status
     */
    public function handleOrder(array $dataOrder): bool
    {
        // Check cart
        if (empty($_SESSION['cart'])) {
            $this->func->transfer("Đơn hàng không hợp lệ. Vui lòng thử lại sau.", $this->configBase, false);
            return false;
        }

        // Validate order data
        if (!$this->validateOrder($dataOrder)) {
            $errors = $this->validator->getErrors();
            foreach ($dataOrder as $k => $v) {
                if (!empty($v)) {
                    $this->flash->set($k, $v);
                }
            }
            $response = ['status' => 'danger', 'messages' => $errors];
            $message = base64_encode(json_encode($response));
            $this->flash->set("message", $message);
            $this->func->redirect("gio-hang");
            return false;
        }

        // Prepare order data
        $orderData = $this->prepareOrderData($dataOrder);

        // Create order using OrderService
        $orderId = $this->orderService->createOrder($orderData);
        
        if (!$orderId) {
            $this->func->transfer("Đặt hàng thất bại. Vui lòng thử lại sau.", $this->configBase, false);
            return false;
        }

        // Save order details
        $orderItems = $this->prepareOrderItems();
        $this->orderService->saveOrderDetails($orderId, $orderItems);

        // Send emails
        if ($this->sendOrderEmails($orderData, $orderId)) {
            // Clear cart
            unset($_SESSION['cart']);
            $this->func->transfer("Đặt hàng thành công. Chúng tôi sẽ liên hệ với bạn sớm.", $this->configBase);
            return true;
        }

        $this->func->transfer("Đặt hàng thất bại. Vui lòng thử lại sau.", $this->configBase, false);
        return false;
    }

    /**
     * Validate order data
     * 
     * @param array $data Order data
     * @return bool
     */
    private function validateOrder(array $data): bool
    {
        $this->validator->required($data['payments'] ?? '', 'Chưa chọn hình thức thanh toán');
        $this->validator->required($data['fullname'] ?? '', 'Họ tên không được trống');
        $this->validator->required($data['phone'] ?? '', 'Số điện thoại không được trống');
        if (!empty($data['phone'])) {
            $this->validator->phone($data['phone'], 'Số điện thoại không hợp lệ');
        }
        $this->validator->required($data['city'] ?? '', 'Chưa chọn tỉnh/thành phố');
        $this->validator->required($data['district'] ?? '', 'Chưa chọn quận/huyện');
        $this->validator->required($data['ward'] ?? '', 'Chưa chọn phường/xã');
        $this->validator->required($data['address'] ?? '', 'Địa chỉ không được trống');
        $this->validator->required($data['email'] ?? '', 'Email không được trống');
        if (!empty($data['email'])) {
            $this->validator->email($data['email'], 'Email không hợp lệ');
        }

        return empty($this->validator->getErrors());
    }

    /**
     * Prepare order data
     * 
     * @param array $dataOrder Order data
     * @return array Prepared order data
     */
    private function prepareOrderData(array $dataOrder): array
    {
        $code = strtoupper($this->func->stringRandom(6));
        $order_date = time();

        // Get place info
        $city_text = $this->func->getInfoDetail('name', "city", $dataOrder['city']);
        $district_text = $this->func->getInfoDetail('name', "district", $dataOrder['district']);
        $ward_text = $this->func->getInfoDetail('name', "ward", $dataOrder['ward']);
        $address = htmlspecialchars($dataOrder['address']) . ', ' . $ward_text['name'] . ', ' . $district_text['name'] . ', ' . $city_text['name'];

        // Get payment info
        $order_payment_data = $this->func->getInfoDetail('namevi', 'news', $dataOrder['payments']);
        $order_payment_text = $order_payment_data['namevi'] ?? '';

        // Calculate shipping
        $ship_price = 0;
        if (!empty($this->config['order']['ship'])) {
            $ship_data = (!empty($dataOrder['ward'])) ? $this->func->getInfoDetail('ship_price', "ward", $dataOrder['ward']) : [];
            $ship_price = $ship_data['ship_price'] ?? 0;
        }

        // Calculate total
        $temp_price = $this->cart->getOrderTotal();
        $total_price = $temp_price + $ship_price;

        // Prepare order detail
        $order_detail = $this->prepareOrderDetail();

        return [
            'code' => $code,
            'date_created' => $order_date,
            'fullname' => htmlspecialchars($dataOrder['fullname']),
            'email' => htmlspecialchars($dataOrder['email']),
            'phone' => htmlspecialchars($dataOrder['phone']),
            'address' => $address,
            'city' => (int)$dataOrder['city'],
            'district' => (int)$dataOrder['district'],
            'ward' => (int)$dataOrder['ward'],
            'payments' => (int)$dataOrder['payments'],
            'payments_text' => $order_payment_text,
            'ship_price' => $ship_price,
            'temp_price' => $temp_price,
            'total_price' => $total_price,
            'order_detail' => $order_detail,
            'requirements' => htmlspecialchars($dataOrder['requirements'] ?? ''),
            'status' => 'dathang',
            'numb' => 0,
        ];
    }

    /**
     * Prepare order detail from cart
     * 
     * @return string Order detail JSON
     */
    private function prepareOrderDetail(): string
    {
        $order_detail = [];
        $max = count($_SESSION['cart'] ?? []);

        for ($i = 0; $i < $max; $i++) {
            $pid = $_SESSION['cart'][$i]['productid'];
            $q = $_SESSION['cart'][$i]['qty'];
            $color = $_SESSION['cart'][$i]['color'] ?? 0;
            $size = $_SESSION['cart'][$i]['size'] ?? 0;
            $code = $_SESSION['cart'][$i]['code'] ?? '';

            $proinfo = $this->cart->getProductInfo($pid);
            $price = $this->cart->getPriceSC($pid, $size, $color);

            $order_detail[] = [
                'productid' => $pid,
                'quantity' => $q,
                'color' => $color,
                'size' => $size,
                'code' => $code,
                'price' => $price,
                'product_name' => $proinfo['name' . $this->lang] ?? '',
            ];
        }

        return json_encode($order_detail);
    }

    /**
     * Prepare order items from cart
     * 
     * @return array Order items
     */
    private function prepareOrderItems(): array
    {
        $items = [];
        $max = count($_SESSION['cart'] ?? []);

        for ($i = 0; $i < $max; $i++) {
            $pid = $_SESSION['cart'][$i]['productid'];
            $q = $_SESSION['cart'][$i]['qty'];
            $color = $_SESSION['cart'][$i]['color'] ?? 0;
            $size = $_SESSION['cart'][$i]['size'] ?? 0;

            $proinfo = $this->cart->getProductInfo($pid);
            $price = $this->cart->getPriceSC($pid, $size, $color);

            $items[] = [
                'id_product' => $pid,
                'quantity' => $q,
                'price' => $price,
                'total' => $price * $q,
                'id_color' => $color,
                'id_size' => $size,
                'name' => $proinfo['name' . $this->lang] ?? '',
                'photo' => $proinfo['photo'] ?? '',
            ];
        }

        return $items;
    }

    /**
     * Send order emails
     * 
     * @param array $orderData Order data
     * @param int $orderId Order ID
     * @return bool
     */
    private function sendOrderEmails(array $orderData, int $orderId): bool
    {
        // Prepare email data
        $this->emailer->set('tennguoimua', $orderData['fullname']);
        $this->emailer->set('emailnguoimua', $orderData['email']);
        $this->emailer->set('dienthoainguoimua', $orderData['phone']);
        $this->emailer->set('diachinguoimua', $orderData['address']);
        $this->emailer->set('mahoadon', $orderData['code']);
        $this->emailer->set('ngaydathang', date('d/m/Y H:i', $orderData['date_created']));
        $this->emailer->set('hinhthucthanhtoan', $orderData['payments_text']);
        $this->emailer->set('phiship', $this->func->formatMoney($orderData['ship_price']));
        $this->emailer->set('tongtien', $this->func->formatMoney($orderData['total_price']));
        $this->emailer->set('chitietdonhang', $this->formatOrderDetail($orderData['order_detail']));

        $emailDefaultAttrs = $this->emailer->defaultAttrs();
        $emailVars = $this->emailer->addAttrs([], $emailDefaultAttrs['vars']);
        $emailVals = $this->emailer->addAttrs([], $emailDefaultAttrs['vals']);

        $subject = "Đơn hàng mới từ " . $this->setting['name' . $this->lang];
        $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('order/admin'));

        // Send to admin
        if ($this->emailer->send("admin", null, $subject, $message)) {
            // Send to customer
            $arrayEmail = [
                "dataEmail" => [
                    "name" => $orderData['fullname'],
                    "email" => $orderData['email']
                ]
            ];
            $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('order/customer'));
            return $this->emailer->send("customer", $arrayEmail, $subject, $message);
        }

        return false;
    }

    /**
     * Format order detail for email
     * 
     * @param string $orderDetailJson Order detail JSON
     * @return string Formatted HTML
     */
    private function formatOrderDetail(string $orderDetailJson): string
    {
        $details = json_decode($orderDetailJson, true);
        $html = '<table border="1" cellpadding="5"><tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th></tr>';
        
        foreach ($details as $detail) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($detail['product_name']) . '</td>';
            $html .= '<td>' . $detail['quantity'] . '</td>';
            $html .= '<td>' . $this->func->formatMoney($detail['price'] * $detail['quantity']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        return $html;
    }
}

