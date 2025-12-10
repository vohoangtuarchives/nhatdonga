<?php

namespace Tuezy\Application\Newsletter;

use Tuezy\Domain\Newsletter\Subscription;
use Tuezy\Domain\Newsletter\NewsletterRepository;
use Tuezy\EmailTemplateHelper;
use Tuezy\ValidationHelper;

class SubscribeNewsletter
{
    public function __construct(
        private NewsletterRepository $repo,
        private $emailer,
        private ValidationHelper $validator,
        private string $siteName
    ) {}

    public function execute(array $input): bool
    {
        $email = $input['email'] ?? '';
        if (!ValidationHelper::isEmail($email)) {
            return false;
        }

        $subscription = new Subscription(
            email: $email,
            fullname: $input['fullname'] ?? null,
            phone: $input['phone'] ?? null,
            address: $input['address'] ?? null,
            subject: $input['subject'] ?? null,
            content: $input['content'] ?? null,
            type: $input['type'] ?? 'dangkynhantin'
        );

        $saved = $this->repo->createFromEntity($subscription);
        if (!$saved) {
            return false;
        }

        $helper = new EmailTemplateHelper($this->emailer);
        $prepared = $helper->prepareContactData([
            'fullname' => $subscription->fullname ?? '',
            'email' => $subscription->email,
            'phone' => $subscription->phone ?? '',
            'address' => $subscription->address ?? '',
            'subject' => $subscription->subject ?? '',
            'content' => $subscription->content ?? '',
        ]);
        $vars = $prepared['vars'];
        $vals = $prepared['vals'];
        $subject = 'Thư liên hệ từ ' . $this->siteName;

        $helper->sendToAdmin('newsletter/admin', $subject, $vars, $vals, 'file_attach');
        $helper->sendToCustomer('newsletter/customer', $subject, ['name' => $subscription->fullname ?? '', 'email' => $subscription->email], $vars, $vals, 'file_attach');

        return true;
    }
}

