<?php

use Tuezy\ValidationHelper;

class ValidatorStub extends ValidationHelper
{
    public function __construct(){ }
    public function recaptcha(string $response, string $action, float $minScore = 0.5): bool
    {
        return true;
    }
}

