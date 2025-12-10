<?php

namespace Tuezy\API\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\Application\Search\SearchProducts;
use Tuezy\Application\Search\SearchArticles;
use Tuezy\Repository\ProductRepository as ProductRepo;

class SearchAPIController extends BaseAPIController
{
    private ProductRepository $productRepo;
    private NewsRepository $newsRepo;
    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);
        $this->productRepo = new ProductRepo($db, $cache, $lang, $sluglang, 'san-pham');
        $this->newsRepo = new NewsRepository($db, $lang, 'tin-tuc');
    }

    public function products(): void
    {
        $keyword = $this->get('keyword', '');
        $page = (int)$this->get('p', 1);
        $perPage = (int)$this->get('perpage', 12);
        $result = (new SearchProducts($this->productRepo))->execute('san-pham', $keyword, $page, $perPage);
        $this->success($result);
    }

    public function suggestProducts(): void
    {
        $prefix = $this->get('prefix', '');
        $limit = (int)$this->get('limit', 10);
        $result = $this->productRepo->getSuggestions('san-pham', $prefix, $limit);
        $this->success(['suggestions' => $result]);
    }

    public function articles(): void
    {
        $keyword = $this->get('keyword', '');
        $page = (int)$this->get('p', 1);
        $perPage = (int)$this->get('perpage', 12);
        $result = (new SearchArticles($this->newsRepo))->execute('tin-tuc', $keyword, $page, $perPage);
        $this->success($result);
    }
}
