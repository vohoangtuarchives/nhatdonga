<?php

namespace Tuezy\Domain\Tags;

interface TagsRepository
{
    public function getByProduct(int $productId, string $type): array;
    public function getByNews(int $newsId, string $type): array;
}

