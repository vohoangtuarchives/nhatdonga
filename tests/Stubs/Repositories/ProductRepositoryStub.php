<?php

use Tuezy\Domain\Catalog\ProductRepository;

class ProductRepositoryStub implements ProductRepository
{
    public function getProductDetail(int $id, ?string $type = null, bool $activeOnly = true): ?array { return null; }
    public function updateProductView(int $id, int $currentView): void {}
    public function getProducts(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12, string $sortBy = 'default', string $sortOrder = 'desc'): array
    {
        return [
            ['id' => 1, 'namevi' => 'A', 'type' => 'san-pham'],
            ['id' => 2, 'namevi' => 'B', 'type' => 'san-pham'],
        ];
    }
    public function countProducts(?string $type = null, array $filters = []): int { return 2; }
    public function getProductColors(int $productId, ?string $type = null): array { return []; }
    public function getProductSizes(int $productId, ?string $type = null): array { return []; }
    public function getProductGallery(int $productId, ?string $type = null): array { return []; }
    public function getRelatedProducts(int $productId, ?int $listId, ?string $type = null, int $limit = 8): array { return []; }
}

