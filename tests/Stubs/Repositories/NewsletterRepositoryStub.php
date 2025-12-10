<?php

use Tuezy\Domain\Newsletter\Subscription;
use Tuezy\Domain\Newsletter\NewsletterRepository;

class NewsletterRepositoryStub implements NewsletterRepository
{
    public array $saved = [];
    public function createFromEntity(Subscription $subscription): bool
    {
        $this->saved[] = $subscription;
        return true;
    }
    public function emailExists(string $email): bool
    {
        return false;
    }
}

