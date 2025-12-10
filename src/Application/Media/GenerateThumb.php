<?php

namespace Tuezy\Application\Media;

use Tuezy\Helper\GlobalHelper;

class GenerateThumb
{
    public function execute(int $w, int $h, int $z, string $filePath, ?array $wtm = null, string $context = 'product'): void
    {
        $func = GlobalHelper::func();
        $func->createThumb($w, $h, $z, $filePath, $wtm, $context);
    }
}

