<?php

namespace Tuezy\Service;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;

class ProductService
{
    public function __construct(
        private ProductRepository $products,
        private ?CategoryRepository $categories,
        private ?TagsRepository $tags,
        private \PDODb $db,
        private string $lang
    ) {
    }

    public function getDetailContext(int $id, string $type, bool $increaseView = true): ?array
    {
        $detail = $this->products->getProductDetail($id, $type);

        if (!$detail) {
            return null;
        }

        if ($increaseView) {
            $this->products->updateProductView($id, (int)($detail['view'] ?? 0));
        }

        return [
            'detail' => $detail,
            'tags' => $this->tags?->getByProduct($id, $type) ?? [],
            'colors' => $this->products->getProductColors($id, $type),
            'sizes' => $this->products->getProductSizes($id, $type),
            'list' => $this->categories?->getListById($detail['id_list'], $type),
            'cat' => $this->categories?->getCatById($detail['id_cat'], $type),
            'item' => $this->categories?->getItemById($detail['id_item'], $type),
            'sub' => $this->categories?->getSubById($detail['id_sub'], $type),
            'brand' => $this->getBrandById((int)$detail['id_brand'], $type),
            'photos' => $this->products->getProductGallery($id, $type),
            'related' => $this->products->getRelatedProducts($id, $detail['id_list'], $type, 8),
        ];
    }

    public function getListing(string $type, array $filters, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;

        $items = $this->products->getProducts($type, $filters, $start, $perPage);
        $total = $this->products->countProducts($type, $filters);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
        ];
    }

    private function getBrandById(int $brandId, string $type): ?array
    {
        if (!$brandId) {
            return null;
        }

        return $this->db->rawQueryOne(
            "select name{$this->lang}, slugvi, slugen, id
             from #_product_brand
             where id = ? and type = ? and find_in_set('hienthi',status)
             limit 0,1",
            [$brandId, $type]
        ) ?: null;
    }

    public function removeSizeColorCombination(int $productId, int $colorId, int $sizeId): void
    {
        if (!$productId) {
            return;
        }

        $this->db->rawQuery(
            "delete from table_product_size_color where id_product = ? and id_color = ? and id_size = ?",
            [$productId, $colorId, $sizeId]
        );

        $this->db->rawQuery(
            "delete from table_product_sale where id_parent = ? and id_color = ? and id_size = ?",
            [$productId, $colorId, $sizeId]
        );
    }

    /**
     * Lưu tags cho product
     * 
     * @param int $productId Product ID
     * @param array $tagIds Array of tag IDs
     * @param string $type Product type
     */
    public function saveProductTags(int $productId, array $tagIds, string $type): void
    {
        if (!$productId || !$this->tags) {
            return;
        }

        // Xóa tags cũ
        $this->db->rawQuery(
            "DELETE FROM #_product_tags WHERE id_parent = ?",
            [$productId]
        );

        // Thêm tags mới
        foreach ($tagIds as $tagId) {
            $tagId = (int)$tagId;
            if ($tagId > 0) {
                $this->db->rawQuery(
                    "INSERT INTO #_product_tags (id_parent, id_tags) VALUES (?, ?) 
                     ON DUPLICATE KEY UPDATE id_tags = id_tags",
                    [$productId, $tagId]
                );
            }
        }
    }

    /**
     * Lưu size/color combinations cho product
     * 
     * @param int $productId Product ID
     * @param array $dataSC Array of size/color combinations [['size' => id, 'color' => id, 'price' => price], ...]
     * @param string $type Product type
     */
    public function saveProductSizeColor(int $productId, array $dataSC, string $type): void
    {
        if (!$productId || empty($dataSC)) {
            return;
        }

        foreach ($dataSC as $item) {
            $sizeId = !empty($item['size']) ? (int)$item['size'] : 0;
            $colorId = !empty($item['color']) ? (int)$item['color'] : 0;
            $price = !empty($item['price']) ? str_replace(',', '', $item['price']) : 0;
            $price = (float)$price;

            if ($sizeId <= 0 && $colorId <= 0) {
                continue;
            }

            $scId = !empty($item['id']) ? (int)$item['id'] : 0;

            // Lưu vào table_product_size_color
            if ($scId > 0) {
                // Update existing
                $this->db->rawQuery(
                    "UPDATE table_product_size_color 
                     SET id_size = ?, id_color = ?, price = ? 
                     WHERE id = ? AND id_product = ?",
                    [$sizeId, $colorId, $price, $scId, $productId]
                );
            } else {
                // Insert new
                $this->db->rawQuery(
                    "INSERT INTO table_product_size_color (id_product, id_size, id_color, price) 
                     VALUES (?, ?, ?, ?)",
                    [$productId, $sizeId, $colorId, $price]
                );
                $scId = $this->db->getLastInsertId();
            }

            // Lưu vào table_product_sale nếu có size hoặc color
            if ($scId > 0) {
                // Xóa cũ
                $this->db->rawQuery(
                    "DELETE FROM table_product_sale 
                     WHERE id_parent = ? AND id_size = ? AND id_color = ?",
                    [$productId, $sizeId, $colorId]
                );

                // Thêm mới
                if ($sizeId > 0 || $colorId > 0) {
                    $this->db->rawQuery(
                        "INSERT INTO table_product_sale (id_parent, id_size, id_color) 
                         VALUES (?, ?, ?)",
                        [$productId, $sizeId, $colorId]
                    );
                }
            }
        }
    }

    /**
     * Lưu product với đầy đủ dữ liệu liên quan (tags, size/color)
     * 
     * @param array $data Product data
     * @param int|null $id Product ID (null for insert)
     * @param array $dataSC Size/Color combinations
     * @param array $dataTags Tag IDs
     * @param string $type Product type
     * @return int|false Product ID on success, false on failure
     */
    public function saveProduct(array $data, ?int $id, array $dataSC = [], array $dataTags = [], string $type = 'san-pham'): int|false
    {
        // Set type
        $data['type'] = $type;

        // Save main product
        if ($id) {
            $this->db->where('id', $id);
            if (!$this->db->update('product', $data)) {
                return false;
            }
            $productId = $id;
        } else {
            if (!isset($data['date_created'])) {
                $data['date_created'] = time();
            }
            if (!isset($data['numb'])) {
                $data['numb'] = 0;
            }
            if (!$this->db->insert('product', $data)) {
                return false;
            }
            $productId = $this->db->getLastInsertId();
        }

        if (!$productId) {
            return false;
        }

        // Save tags
        if (!empty($dataTags)) {
            $this->saveProductTags($productId, $dataTags, $type);
        }

        // Save size/color combinations
        if (!empty($dataSC)) {
            $this->saveProductSizeColor($productId, $dataSC, $type);
        }

        return $productId;
    }
}

