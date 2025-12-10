<?php

namespace Tuezy\Application\Catalog\DTO;

class ProductDetailDTO
{
    public function __construct(
        public array $detail,
        public array $tags = [],
        public array $colors = [],
        public array $sizes = [],
        public ?array $list = null,
        public ?array $cat = null,
        public ?array $item = null,
        public ?array $sub = null,
        public ?array $brand = null,
        public array $photos = [],
        public array $related = []
    ) {}
}

