<?php

namespace Tuezy\Domain\Catalog;

interface ProductRepository
{
    public function getProductDetail(int $id, ?string $type = null, bool $activeOnly = true): ?array;
    public function updateProductView(int $id, int $currentView): void;
    public function getProducts(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12, string $sortBy = 'default', string $sortOrder = 'desc'): array;
    public function countProducts(?string $type = null, array $filters = []): int;
    public function getProductColors(int $productId, ?string $type = null): array;
    public function getProductSizes(int $productId, ?string $type = null): array;
    public function getProductGallery(int $productId, ?string $type = null): array;
    public function getRelatedProducts(int $productId, ?int $listId, ?string $type = null, int $limit = 8): array;
}

