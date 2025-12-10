<?php

namespace Tuezy\Domain\Content;

interface ArticleRepository
{
    public function getNewsDetail(int $id, ?string $type = null): ?array;
    public function updateNewsView(int $id, int $currentView): void;
    public function getNewsGallery(int $newsId, ?string $type = null): array;
    public function getRelated(int $excludeId, int $page = 1, int $perPage = 12): array;
    public function getNewsItems(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12): array;
    public function countNewsItems(?string $type = null, array $filters = []): int;
}

