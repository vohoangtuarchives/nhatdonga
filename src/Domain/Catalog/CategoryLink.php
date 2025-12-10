<?php

namespace Tuezy\Domain\Catalog;

class CategoryLink
{
    public function __construct(
        public string $name,
        public string $slug
    ) {}
}

