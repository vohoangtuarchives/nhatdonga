<?php

namespace Tuezy\Domain\SEO;

class SeoMeta
{
    public function __construct(
        public ?string $title = null,
        public ?string $keywords = null,
        public ?string $description = null,
        public ?string $schema = null
    ) {}
}

