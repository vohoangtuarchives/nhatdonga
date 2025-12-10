<?php

namespace Tuezy\Application\Contact;

class SubmitContactCommand
{
    public array $data;
    public string $recaptchaResponse;

    public function __construct(array $data, string $recaptchaResponse)
    {
        $this->data = $data;
        $this->recaptchaResponse = $recaptchaResponse;
    }
}

