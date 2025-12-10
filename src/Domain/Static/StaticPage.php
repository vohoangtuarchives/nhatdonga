<?php

namespace Tuezy\Domain\Static;

class StaticPage
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $slug,
        public ?string $content = null,
        public ?string $photo = null
    ) {}
}

