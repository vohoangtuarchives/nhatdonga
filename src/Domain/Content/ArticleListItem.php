<?php

namespace Tuezy\Domain\Content;

class ArticleListItem
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $photo = null
    ) {}
}

