<?php

namespace Tuezy\Application\Search;

use Tuezy\Domain\Content\ArticleRepository;
use Tuezy\Application\Content\DTO\ArticleListDTO;

class SearchArticles
{
    public function __construct(private ArticleRepository $repo) {}
    public function execute(string $type, string $keyword, int $page = 1, int $perPage = 12): array
    {
        $filters = ['keyword' => $keyword];
        $page = max($page, 1); $perPage = max($perPage, 1); $start = ($page - 1) * $perPage;
        $items = $this->repo->getNewsItems($type, $filters, $start, $perPage);
        $total = $this->repo->countNewsItems($type, $filters);
        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
            'dto' => new ArticleListDTO($items, $total, $perPage, $page, $start),
        ];
    }
}

