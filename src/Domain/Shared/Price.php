<?php

namespace Tuezy\Domain\Shared;

class Price
{
    public function __construct(
        public float $regular,
        public float $sale = 0.0,
        public float $discount = 0.0
    ) {}

    public function effective(): float
    {
        return $this->sale > 0 ? $this->sale : $this->regular;
    }
}

