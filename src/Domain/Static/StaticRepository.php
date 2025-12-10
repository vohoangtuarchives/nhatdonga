<?php

namespace Tuezy\Domain\Static;

interface StaticRepository
{
    public function getByType(string $type): ?array;
    public function getById(int $id): ?array;
    public function getAllByType(string $type, bool $active = true): array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

