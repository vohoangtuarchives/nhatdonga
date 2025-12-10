<?php

namespace Tuezy\Application\Catalog;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Application\Catalog\DTO\ProductDetailDTO;

class GetProductDetail
{
    public function __construct(
        private ProductRepository $products,
        private ?CategoryRepository $categories,
        private ?TagsRepository $tags,
        private \PDODb $db,
        private string $lang
    ) {}

    public function execute(int $id, string $type, bool $increaseView = true, bool $activeOnly = true): ?array
    {
        $detail = $this->products->getProductDetail($id, $type, $activeOnly);
        if (!$detail) return null;

        if ($increaseView) {
            $this->products->updateProductView($id, (int)($detail['view'] ?? 0));
        }

        $list = $this->categories?->getListById($detail['id_list'], $type);
        $cat = $this->categories?->getCatById($detail['id_cat'], $type);
        $item = $this->categories?->getItemById($detail['id_item'], $type);
        $sub = $this->categories?->getSubById($detail['id_sub'], $type);

        $brand = null;
        $brandId = (int)($detail['id_brand'] ?? 0);
        if ($brandId > 0) {
            $brand = $this->db->rawQueryOne(
                "select name{$this->lang}, slugvi, slugen, id from #_product_brand where id = ? and type = ? and find_in_set('hienthi',status) limit 0,1",
                [$brandId, $type]
            ) ?: null;
        }

        return [
            'detail' => $detail,
            'tags' => $this->tags?->getByProduct($id, $type) ?? [],
            'colors' => $this->products->getProductColors($id, $type),
            'sizes' => $this->products->getProductSizes($id, $type),
            'list' => $list,
            'cat' => $cat,
            'item' => $item,
            'sub' => $sub,
            'brand' => $brand,
            'photos' => $this->products->getProductGallery($id, $type),
            'related' => $this->products->getRelatedProducts($id, $detail['id_list'], $type, 8),
            'dto' => new ProductDetailDTO(
                detail: $detail,
                tags: $this->tags?->getByProduct($id, $type) ?? [],
                colors: $this->products->getProductColors($id, $type),
                sizes: $this->products->getProductSizes($id, $type),
                list: $list,
                cat: $cat,
                item: $item,
                sub: $sub,
                brand: $brand,
                photos: $this->products->getProductGallery($id, $type),
                related: $this->products->getRelatedProducts($id, $detail['id_list'], $type, 8)
            ),
        ];
    }
}
