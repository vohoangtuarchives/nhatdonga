<?php

namespace Tuezy\Service;

use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\CategoryRepository;

/**
 * NewsService - Business logic layer cho module tin tức
 * Tách business logic từ sources/news.php và admin/sources/news.php
 */
class NewsService
{
    public function __construct(
        private NewsRepository $news,
        private ?CategoryRepository $categories,
        private \PDODb $db,
        private string $lang,
        private string $sluglang
    ) {
    }

    /**
     * Lấy chi tiết bài viết với đầy đủ context (categories, gallery, related)
     * 
     * @param int $id News ID
     * @param string $type News type
     * @param bool $increaseView Có tăng lượt xem không
     * @return array|null
     */
    public function getDetailContext(int $id, string $type, bool $increaseView = true): ?array
    {
        $detail = $this->news->getNewsDetail($id, $type);

        if (!$detail) {
            return null;
        }

        if ($increaseView) {
            $this->news->updateNewsView($id, (int)($detail['view'] ?? 0));
        }

        return [
            'detail' => $detail,
            'list' => $this->categories?->getListById($detail['id_list'], $type),
            'cat' => $this->categories?->getCatById($detail['id_cat'], $type),
            'item' => $this->categories?->getItemById($detail['id_item'], $type),
            'sub' => $this->categories?->getSubById($detail['id_sub'], $type),
            'photos' => $this->news->getNewsGallery($id, $type),
            'related' => $this->getRelatedNews($id, $type, 8),
        ];
    }

    /**
     * Lấy danh sách bài viết với filters và pagination
     * 
     * @param string $type News type
     * @param array $filters Filters (id_list, id_cat, id_item, id_sub, keyword, status)
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array ['items' => array, 'total' => int, 'perPage' => int, 'page' => int, 'start' => int]
     */
    public function getListing(string $type, array $filters, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;

        $items = $this->news->getNewsItems($type, $filters, $start, $perPage);
        $total = $this->news->countNewsItems($type, $filters);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
        ];
    }

    /**
     * Lấy bài viết liên quan
     * 
     * @param int $excludeId News ID to exclude
     * @param string $type News type
     * @param int $limit Limit results
     * @return array
     */
    public function getRelatedNews(int $excludeId, string $type, int $limit = 8): array
    {
        // Get related by same category if available
        $detail = $this->news->getNewsDetail($excludeId, $type);
        if (!$detail) {
            return [];
        }

        $filters = [];
        if (!empty($detail['id_list'])) {
            $filters['id_list'] = $detail['id_list'];
        }

        $items = $this->news->getNewsItems($type, $filters, 0, $limit + 1);
        
        // Remove current item
        $items = array_filter($items, function($item) use ($excludeId) {
            return (int)$item['id'] !== $excludeId;
        });

        return array_slice($items, 0, $limit);
    }

    /**
     * Lấy category detail với SEO info
     * 
     * @param int $categoryId Category ID
     * @param string $categoryType Category type (list, cat, item, sub)
     * @param string $newsType News type
     * @return array|null
     */
    public function getCategoryDetail(int $categoryId, string $categoryType, string $newsType): ?array
    {
        if (!$this->categories) {
            return null;
        }

        $method = 'get' . ucfirst($categoryType) . 'ById';
        if (!method_exists($this->categories, $method)) {
            return null;
        }

        return $this->categories->$method($categoryId, $newsType);
    }
}

