<?php

namespace Tuezy\Application\SEO;

use Tuezy\Domain\SEO\SeoRepository;

class SaveSeoMeta
{
    public function __construct(private SeoRepository $repo) {}

    public function execute(int $idParent, string $com, string $act, string $type, array $data): bool
    {
        return $this->repo->saveMeta($idParent, $com, $act, $type, $data);
    }
}

