<?php

namespace Tuezy\API\Controller;

use Tuezy\Repository\NewsRepository;
use Tuezy\Application\Content\ListArticles;
use Tuezy\Application\Content\GetArticleDetail;

class NewsAPIController extends BaseAPIController
{
    private NewsRepository $newsRepo;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);
        $this->newsRepo = new NewsRepository($db, $lang, 'tin-tuc');
    }

    public function getList(): void
    {
        $perPage = (int)$this->get('perpage', 12);
        $page = (int)$this->get('p', 1);
        $filters = [];
        $listing = (new ListArticles($this->newsRepo))->execute('tin-tuc', $filters, $page, $perPage);
        $this->success([
            'news' => $listing['items'],
            'total' => $listing['total'],
            'dto' => $listing['dto'],
        ]);
    }

    public function getListByHierarchy(): void
    {
        $perPage = (int)$this->get('perpage', 12);
        $page = (int)$this->get('p', 1);
        $level = $this->get('level', 'list');
        $id = (int)$this->get('id', 0);
        if ($id <= 0) { $this->error('Invalid id'); return; }
        $listing = (new ListArticlesByHierarchy($this->newsRepo))->execute('tin-tuc', $level, $id, $page, $perPage);
        $this->success([
            'news' => $listing['items'],
            'total' => $listing['total'],
            'dto' => $listing['dto'],
        ]);
    }

    public function getDetail(int $id): void
    {
        if ($id <= 0) { $this->error('Invalid id'); return; }
        $detail = (new GetArticleDetail($this->newsRepo))->execute($id, 'tin-tuc', true);
        if (!$detail) { $this->error('Not found', 404); return; }
        $this->success([
            'detail' => $detail['detail'],
            'photos' => $detail['photos'],
            'related' => $detail['related'],
            'dto' => $detail['dto'],
        ]);
    }
}
