<?php

namespace Tuezy\Controller;

use Tuezy\Repository\StaticRepository;
use Tuezy\Service\StaticService;

/**
 * StaticController - Handles static page requests
 */
class StaticController extends BaseController
{
    private StaticService $staticService;
    private StaticRepository $staticRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->staticRepo = new StaticRepository($db, $cache, $lang, $sluglang);
        $this->staticService = new StaticService($this->staticRepo);
    }

    /**
     * Display static page
     * 
     * @param string $type Static page type
     * @return array View data
     */
    public function index(string $type): array
    {
        $static = (new \Tuezy\Application\Static\GetStaticByType($this->staticRepo))->execute($type);
        
        if (!$static) {
            $lang = $_SESSION['lang'] ?? 'vi';
            $name = str_replace('-', ' ', ucfirst($type));
            $this->breadcrumbHelper->add($name, $type);
            return [
                'static' => null,
                'breadcrumbs' => $this->breadcrumbHelper->render(),
            ];
        }

        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';

        // SEO
        $seoDB = (new \Tuezy\Application\SEO\GetSeoByParent(new \Tuezy\Repository\SeoRepository($this->db)))->execute(0, 'static', 'update', $static['type']);
        $this->seo->set('h1', $static['name' . $lang]);

        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
        } else {
            $this->seo->set('title', $static['name' . $lang]);
        }

        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }

        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }

        $this->seo->set('url', $this->func->getPageURL());
        $this->seo->set('canonical', $this->func->getPageURL());
        $this->seo->set('robots', 'index,follow');

        // Breadcrumbs
        $sluglang = 'slugvi';
        $slug = $static[$sluglang] ?? $static['type'] ?? '';
        $this->breadcrumbHelper->add($static['name' . $lang], $slug);

        return [
            'static' => $static,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }
}

