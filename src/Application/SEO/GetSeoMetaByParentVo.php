<?php

namespace Tuezy\Application\SEO;

use Tuezy\Repository\SeoRepository;

class GetSeoMetaByParentVo
{
    public function __construct(private SeoRepository $repo) {}

    public function execute(int $idParent, string $com, string $act, string $type, string $seolang = 'vi')
    {
        return $this->repo->getMetaVoByParent($idParent, $com, $act, $type, $seolang);
    }
}

