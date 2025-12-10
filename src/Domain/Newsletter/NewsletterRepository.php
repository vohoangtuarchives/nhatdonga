<?php

namespace Tuezy\Domain\Newsletter;

interface NewsletterRepository
{
    public function createFromEntity(Subscription $subscription): bool;
    public function emailExists(string $email): bool;
}

