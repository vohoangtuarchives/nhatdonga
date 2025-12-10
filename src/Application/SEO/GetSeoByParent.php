<?php

namespace Tuezy\Application\SEO;

use Tuezy\Domain\SEO\SeoRepository;

class GetSeoByParent
{
    public function __construct(private SeoRepository $repo) {}

    public function execute(int $idParent, string $com, string $act, string $type): ?array
    {
        return $this->repo->getByParent($idParent, $com, $act, $type);
    }
}

