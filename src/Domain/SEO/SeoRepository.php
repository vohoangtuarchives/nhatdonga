<?php

namespace Tuezy\Domain\SEO;

interface SeoRepository
{
    public function getByParent(int $idParent, string $com, string $act, string $type): ?array;
    public function saveMeta(int $idParent, string $com, string $act, string $type, array $data): bool;
}

