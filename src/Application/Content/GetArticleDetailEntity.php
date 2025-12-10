<?php

namespace Tuezy\Application\Content;

use Tuezy\Repository\NewsRepository;

class GetArticleDetailEntity
{
    public function __construct(private NewsRepository $repo) {}

    public function execute(int $id)
    {
        return $this->repo->getDetailEntity($id);
    }
}

