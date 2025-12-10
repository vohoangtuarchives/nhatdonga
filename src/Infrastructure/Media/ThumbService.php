<?php

namespace Tuezy\Infrastructure\Media;

use Tuezy\Domain\Media\ThumbService as ThumbServiceInterface;
use Tuezy\Helper\GlobalHelper;

class ThumbService implements ThumbServiceInterface
{
    public function generate(int $w, int $h, int $z, string $filePath, ?array $wtm = null, string $context = 'product'): void
    {
        GlobalHelper::func()->createThumb($w, $h, $z, $filePath, $wtm, $context);
    }
}

