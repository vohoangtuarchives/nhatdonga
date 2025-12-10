<?php

namespace Tuezy\Application\Contact;

use Tuezy\Domain\Contact\ContactRepository as DomainContactRepository;

class MarkContactAsReadHandler
{
    private DomainContactRepository $repo;

    public function __construct(DomainContactRepository $repo)
    {
        $this->repo = $repo;
    }

    public function handle(int $id): bool
    {
        return $this->repo->markAsRead($id);
    }
}

