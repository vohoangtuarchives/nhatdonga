<?php

namespace Tuezy\Application\Content\DTO;

class ArticleListDTO
{
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $page,
        public int $start
    ) {}
}

