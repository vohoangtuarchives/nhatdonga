<?php

namespace Tuezy\Application\Content;

use Tuezy\Domain\Content\ArticleRepository;
use Tuezy\Application\Content\DTO\ArticleListDTO;

class ListArticlesByHierarchy
{
    public function __construct(private ArticleRepository $repo) {}

    public function execute(string $type, string $level, int $id, int $page, int $perPage): array
    {
        $filters = [];
        switch ($level) {
            case 'list':
                $filters['id_list'] = $id; break;
            case 'cat':
                $filters['id_cat'] = $id; break;
            case 'item':
                $filters['id_item'] = $id; break;
            case 'sub':
                $filters['id_sub'] = $id; break;
            default:
                // no-op
        }
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

