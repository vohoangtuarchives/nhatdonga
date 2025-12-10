<?php

namespace Tuezy\Application\Tags;

use Tuezy\Domain\Tags\TagsRepository;

class GetTagsByNews
{
    public function __construct(private TagsRepository $repo) {}
    public function execute(int $newsId, string $type): array
    {
        return $this->repo->getByNews($newsId, $type);
    }
}

