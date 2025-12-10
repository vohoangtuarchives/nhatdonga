<?php

namespace Tuezy\Domain\Content;

class Article
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $slug,
        public ?string $photo = null,
        public ?int $view = null
    ) {}
}

