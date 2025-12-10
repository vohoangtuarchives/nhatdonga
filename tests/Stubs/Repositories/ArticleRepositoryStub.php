<?php

use Tuezy\Domain\Content\ArticleRepository;

class ArticleRepositoryStub implements ArticleRepository
{
    public function getNewsDetail(int $id, ?string $type = null): ?array { return null; }
    public function updateNewsView(int $id, int $currentView): void {}
    public function getNewsGallery(int $newsId, ?string $type = null): array { return []; }
    public function getRelated(int $excludeId, int $page = 1, int $perPage = 12): array { return []; }
    public function getNewsItems(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12): array
    {
        return [
            ['id' => 1, 'namevi' => 'N1', 'type' => 'tin-tuc'],
            ['id' => 2, 'namevi' => 'N2', 'type' => 'tin-tuc'],
            ['id' => 3, 'namevi' => 'N3', 'type' => 'tin-tuc'],
        ];
    }
    public function countNewsItems(?string $type = null, array $filters = []): int { return 3; }
}

