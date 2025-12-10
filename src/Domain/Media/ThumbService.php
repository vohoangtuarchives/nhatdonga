<?php

namespace Tuezy\Domain\Media;

interface ThumbService
{
    public function generate(int $w, int $h, int $z, string $filePath, ?array $wtm = null, string $context = 'product'): void;
}

