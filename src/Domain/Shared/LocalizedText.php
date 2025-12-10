<?php

namespace Tuezy\Domain\Shared;

class LocalizedText
{
    public function __construct(
        public string $vi,
        public string $en
    ) {}

    public function for(string $lang): string
    {
        return $lang === 'en' ? $this->en : $this->vi;
    }
}

