<?php

namespace Tuezy\Application\Tags;

use Tuezy\Domain\Tags\TagsRepository;

class GetTagsByProduct
{
    public function __construct(private TagsRepository $repo) {}
    public function execute(int $productId, string $type): array
    {
        return $this->repo->getByProduct($productId, $type);
    }
}

