<?php

namespace Tuezy\Application\Content;

use Tuezy\Domain\Content\ArticleRepository;
use Tuezy\Application\Content\DTO\ArticleDetailDTO;

class GetArticleDetail
{
    public function __construct(private ArticleRepository $repo) {}

    public function execute(int $id, string $type, bool $increaseView = true): ?array
    {
        $detail = $this->repo->getNewsDetail($id, $type);
        if (!$detail) return null;
        if ($increaseView) {
            $this->repo->updateNewsView($id, (int)($detail['view'] ?? 0));
        }
        $photos = $this->repo->getNewsGallery($id, $type);
        $related = $this->repo->getRelated($id, 1, 8);
        return [
            'detail' => $detail,
            'photos' => $photos,
            'related' => $related,
            'dto' => new ArticleDetailDTO(detail: $detail, photos: $photos, related: $related),
        ];
    }
}
