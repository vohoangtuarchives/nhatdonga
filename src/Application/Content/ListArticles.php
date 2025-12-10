<?php

namespace Tuezy\Application\Content;

use Tuezy\Domain\Content\ArticleRepository;
use Tuezy\Application\Content\DTO\ArticleListDTO;

class ListArticles
{
    public function __construct(private ArticleRepository $repo) {}

    public function execute(string $type, array $filters, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;
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
