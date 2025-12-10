<?php

namespace Tuezy\Application\Content\DTO;

class ArticleDetailDTO
{
    public function __construct(
        public array $detail,
        public array $photos = [],
        public array $related = [],
        public ?array $list = null,
        public ?array $cat = null,
        public ?array $item = null,
        public ?array $sub = null
    ) {}
}

