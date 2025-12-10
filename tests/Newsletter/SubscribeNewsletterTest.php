<?php

require __DIR__ . '/../Stubs/Repositories/NewsletterRepositoryStub.php';
require __DIR__ . '/../Stubs/EmailerStub.php';
require __DIR__ . '/../Stubs/ValidatorStub.php';

use Tuezy\Application\Newsletter\SubscribeNewsletter;

$repo = new NewsletterRepositoryStub();
$emailer = new EmailerStub();
$validator = new ValidatorStub();

$useCase = new SubscribeNewsletter($repo, $emailer, $validator, 'SiteName');

$ok = $useCase->execute([
    'email' => 'user@example.com',
    'fullname' => 'User',
    'subject' => 'Tư vấn',
]);

echo $ok && count($repo->saved) === 1 ? "PASS: SubscribeNewsletter\n" : "FAIL: SubscribeNewsletter\n";
echo count($emailer->sent) >= 2 ? "PASS: EmailsSent\n" : "FAIL: EmailsSent\n";

