<?php

namespace Tuezy\Domain\Newsletter;

class Subscription
{
    public function __construct(
        public string $email,
        public ?string $fullname = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $subject = null,
        public ?string $content = null,
        public string $type = 'dangkynhantin',
        public ?int $dateCreated = null
    ) {
        $this->email = trim($email);
        $this->fullname = $fullname ? trim($fullname) : null;
        $this->phone = $phone ? trim($phone) : null;
        $this->address = $address ? trim($address) : null;
        $this->subject = $subject ? trim($subject) : null;
        $this->content = $content ? trim($content) : null;
        $this->type = trim($type);
        $this->dateCreated = $dateCreated ?? time();
    }
}

