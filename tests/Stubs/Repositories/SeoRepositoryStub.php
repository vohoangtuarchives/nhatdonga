<?php

use Tuezy\Domain\SEO\SeoRepository;

class SeoRepositoryStub implements SeoRepository
{
    public int $savedCount = 0;
    public function getByParent(int $idParent, string $com, string $act, string $type): ?array
    {
        return null;
    }
    public function saveMeta(int $idParent, string $com, string $act, string $type, array $data): bool
    {
        $this->savedCount++;
        return true;
    }
}

