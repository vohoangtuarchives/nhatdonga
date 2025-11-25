<?php

namespace Tuezy\Helper;

use Tuezy\Repository\CategoryRepository;

/**
 * CategoryHelper - Helper for category operations
 * Provides utilities for category hierarchy, breadcrumbs, etc.
 */
class CategoryHelper
{
    private CategoryRepository $categoryRepo;
    private string $lang;
    private string $sluglang;
    private string $configBase;

    public function __construct(CategoryRepository $categoryRepo, string $lang, string $sluglang, string $configBase)
    {
        $this->categoryRepo = $categoryRepo;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
        $this->configBase = $configBase;
    }

    /**
     * Get category breadcrumbs
     * 
     * @param int|null $listId List ID
     * @param int|null $catId Cat ID
     * @param int|null $itemId Item ID
     * @param string $type Category type
     * @return array Breadcrumb items
     */
    public function getBreadcrumbs(?int $listId = null, ?int $catId = null, ?int $itemId = null, string $type = ''): array
    {
        $breadcrumbs = [];
        $hierarchy = $this->categoryRepo->getHierarchy($listId, $catId, $itemId, $type);

        if (!empty($hierarchy['list'])) {
            $breadcrumbs[] = [
                'name' => $hierarchy['list']['name' . $this->lang],
                'url' => $this->configBase . $hierarchy['list'][$this->sluglang],
            ];
        }

        if (!empty($hierarchy['cat'])) {
            $breadcrumbs[] = [
                'name' => $hierarchy['cat']['name' . $this->lang],
                'url' => $this->configBase . $hierarchy['cat'][$this->sluglang],
            ];
        }

        if (!empty($hierarchy['item'])) {
            $breadcrumbs[] = [
                'name' => $hierarchy['item']['name' . $this->lang],
                'url' => $this->configBase . $hierarchy['item'][$this->sluglang],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Get category tree
     * 
     * @param string $type Category type
     * @return array Category tree
     */
    public function getCategoryTree(string $type): array
    {
        $lists = $this->categoryRepo->getLists($type, true, false);
        $tree = [];

        foreach ($lists as $list) {
            $listItem = [
                'id' => $list['id'],
                'name' => $list['name' . $this->lang],
                'slug' => $list[$this->sluglang],
                'photo' => $list['photo'] ?? '',
                'children' => [],
            ];

            $cats = $this->categoryRepo->getCats($type, $list['id'], true);
            foreach ($cats as $cat) {
                $catItem = [
                    'id' => $cat['id'],
                    'name' => $cat['name' . $this->lang],
                    'slug' => $cat[$this->sluglang],
                    'photo' => $cat['photo'] ?? '',
                    'children' => [],
                ];

                $items = $this->categoryRepo->getItems($type, $cat['id'], true);
                foreach ($items as $item) {
                    $itemItem = [
                        'id' => $item['id'],
                        'name' => $item['name' . $this->lang],
                        'slug' => $item[$this->sluglang],
                        'photo' => $item['photo'] ?? '',
                        'children' => [],
                    ];

                    $subs = $this->categoryRepo->getSubs($type, $item['id'], true);
                    foreach ($subs as $sub) {
                        $itemItem['children'][] = [
                            'id' => $sub['id'],
                            'name' => $sub['name' . $this->lang],
                            'slug' => $sub[$this->sluglang],
                            'photo' => $sub['photo'] ?? '',
                        ];
                    }

                    $catItem['children'][] = $itemItem;
                }

                $listItem['children'][] = $catItem;
            }

            $tree[] = $listItem;
        }

        return $tree;
    }

    /**
     * Get category path (list > cat > item > sub)
     * 
     * @param int|null $listId List ID
     * @param int|null $catId Cat ID
     * @param int|null $itemId Item ID
     * @param int|null $subId Sub ID
     * @param string $type Category type
     * @return string Category path
     */
    public function getCategoryPath(?int $listId = null, ?int $catId = null, ?int $itemId = null, ?int $subId = null, string $type = ''): string
    {
        $path = [];
        $hierarchy = $this->categoryRepo->getHierarchy($listId, $catId, $itemId, $type);

        if (!empty($hierarchy['list'])) {
            $path[] = $hierarchy['list']['name' . $this->lang];
        }
        if (!empty($hierarchy['cat'])) {
            $path[] = $hierarchy['cat']['name' . $this->lang];
        }
        if (!empty($hierarchy['item'])) {
            $path[] = $hierarchy['item']['name' . $this->lang];
        }

        return implode(' > ', $path);
    }
}

