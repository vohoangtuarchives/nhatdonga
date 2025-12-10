<?php

namespace Tuezy\Domain\Contact;

interface ContactRepository
{
    public function getById(int $id): ?Contact;
    public function getAll(array $filters = [], int $start = 0, int $limit = 20): array;
    public function count(array $filters = []): int;
    public function create(Contact $contact): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function markAsRead(int $id): bool;
}

