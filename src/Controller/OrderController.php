<?php

namespace Tuezy\Controller;

use Tuezy\OrderHandler;
use Tuezy\ValidationHelper;
use Tuezy\Repository\OrderRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\LocationRepository;

class OrderController extends BaseController
{
    private OrderHandler $orderHandler;
    private OrderRepository $orderRepo;
    private NewsRepository $newsRepo;
    private LocationRepository $locationRepo;
    private ValidationHelper $validator;
    private $cart;
    private $emailer;
    private $flash;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        $cart,
        $emailer,
        $flash
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);
        
        $this->cart = $cart;
        $this->emailer = $emailer;
        $this->flash = $flash;

        $lang = $_SESSION['lang'] ?? 'vi';
        
        $this->orderRepo = new OrderRepository($db, $cache);
        $this->newsRepo = new NewsRepository($db, $lang, 'tin-tuc');
        $this->locationRepo = new LocationRepository($db, $cache);
        $this->validator = new ValidationHelper($func, $config);
        
        // OrderHandler logic
        $configBase = $config['database']['url'] ?? '';
        $setting = $GLOBALS['setting'] ?? null; // Should ideally be injected
        
        $this->orderHandler = new OrderHandler(
            $db,
            $func,
            $cart,
            $emailer,
            $flash,
            $this->validator,
            $this->orderRepo,
            $configBase,
            $lang,
            $setting,
            $config
        );
    }

    public function index(): array
    {
        $titleMain = 'Giỏ hàng';
        $lang = $_SESSION['lang'] ?? 'vi';

        // SEO
        $this->seo->set('title', $titleMain);

        // Breadcrumbs
        $this->breadcrumbHelper->add($titleMain, '/gio-hang');

        // Layout data
        $city = $this->locationRepo->getCities();
        $payments_info = $this->newsRepo->getNewsItems('hinh-thuc-thanh-toan', [], 0, 0);

        // Handle Order Submission
        if (!empty($_POST['thanhtoan'])) {
            $dataOrder = $_POST['dataOrder'] ?? [];
            $this->orderHandler->handleOrder($dataOrder);
        }

        return [
            'city' => $city,
            'payments_info' => $payments_info,
            'titleMain' => $titleMain,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'action' => 'process' // Signal to view that order processing might have happened
        ];
    }
}
