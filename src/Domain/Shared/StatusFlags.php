<?php

namespace Tuezy\Domain\Shared;

class StatusFlags
{
    private array $flags;

    public function __construct(string $status)
    {
        $parts = array_filter(array_map('trim', explode(',', $status)));
        $this->flags = array_values(array_unique($parts));
    }

    public function has(string $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }
}

