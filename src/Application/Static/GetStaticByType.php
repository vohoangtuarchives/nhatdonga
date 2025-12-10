<?php

namespace Tuezy\Application\Static;

use Tuezy\Domain\Static\StaticRepository;

class GetStaticByType
{
    public function __construct(private StaticRepository $repo) {}

    public function execute(string $type): ?array
    {
        return $this->repo->getByType($type);
    }
}

