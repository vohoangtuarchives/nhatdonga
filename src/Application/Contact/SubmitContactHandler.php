<?php

namespace Tuezy\Application\Contact;

use Tuezy\Domain\Contact\Contact;
use Tuezy\Domain\Contact\ContactRepository as DomainContactRepository;
use Tuezy\ValidationHelper;

class SubmitContactHandler
{
    private DomainContactRepository $repo;
    private ValidationHelper $validator;
    private $emailer;

    public function __construct(DomainContactRepository $repo, ValidationHelper $validator, $emailer)
    {
        $this->repo = $repo;
        $this->validator = $validator;
        $this->emailer = $emailer;
    }

    public function handle(SubmitContactCommand $cmd): array
    {
        if (!$this->validator->recaptcha($cmd->recaptchaResponse, 'contact')) {
            return ['success' => false, 'errors' => ['recaptcha']];
        }

        if (!$this->validator->validateContact($cmd->data)) {
            return ['success' => false, 'errors' => $this->validator->getErrors()];
        }

        $sanitized = [
            'fullname' => htmlspecialchars($cmd->data['fullname'] ?? ''),
            'email' => htmlspecialchars($cmd->data['email'] ?? ''),
            'phone' => htmlspecialchars($cmd->data['phone'] ?? ''),
            'address' => htmlspecialchars($cmd->data['address'] ?? ''),
            'subject' => htmlspecialchars($cmd->data['subject'] ?? ''),
            'content' => htmlspecialchars($cmd->data['content'] ?? ''),
            'date_created' => time(),
            'numb' => 1,
        ];

        $entity = Contact::fromArray($sanitized);
        if (!$this->repo->create($entity)) {
            return ['success' => false, 'errors' => ['persist']];
        }

        $emailVars = $this->getEmailVars($sanitized);
        $emailVals = $this->getEmailVals($sanitized, $emailVars['default']);

        $subject = 'Thư liên hệ';
        $message = str_replace($emailVars['vars'], $emailVals['vals'], $this->emailer->markdown('contact/admin'));

        if ($this->emailer->send('admin', null, $subject, $message, 'file_attach')) {
            $arrayEmail = [
                'dataEmail' => [
                    'name' => $sanitized['fullname'],
                    'email' => $sanitized['email'],
                ],
            ];
            $message = str_replace($emailVars['vars'], $emailVals['vals'], $this->emailer->markdown('contact/customer'));
            $sent = $this->emailer->send('customer', $arrayEmail, $subject, $message, 'file_attach');
            if ($sent) {
                return ['success' => true, 'id' => $this->emailer->get('last_insert_id') ?? null];
            }
        }

        return ['success' => false, 'errors' => ['email']];
    }

    private function getEmailVars(array $data): array
    {
        $this->emailer->set('tennguoigui', $data['fullname'] ?? '');
        $this->emailer->set('emailnguoigui', $data['email'] ?? '');
        $this->emailer->set('dienthoainguoigui', $data['phone'] ?? '');
        $this->emailer->set('diachinguoigui', $data['address'] ?? '');
        $this->emailer->set('tieudelienhe', $data['subject'] ?? '');
        $this->emailer->set('noidunglienhe', $data['content'] ?? '');

        $strThongtin = '';
        if ($this->emailer->get('tennguoigui')) {
            $strThongtin .= '<span style="text-transform:capitalize">' . $this->emailer->get('tennguoigui') . '</span><br>';
        }
        if ($this->emailer->get('emailnguoigui')) {
            $strThongtin .= '<a href="mailto:' . $this->emailer->get('emailnguoigui') . '" target="_blank">' . $this->emailer->get('emailnguoigui') . '</a><br>';
        }
        if ($this->emailer->get('diachinguoigui')) {
            $strThongtin .= '' . $this->emailer->get('diachinguoigui') . '<br>';
        }
        if ($this->emailer->get('dienthoainguoigui')) {
            $strThongtin .= 'Tel: ' . $this->emailer->get('dienthoainguoigui');
        }
        $this->emailer->set('thongtin', $strThongtin);

        $default = $this->emailer->defaultAttrs();
        $vars = ['{emailTitleSender}','{emailInfoSender}','{emailSubjectSender}','{emailContentSender}'];
        return ['vars' => $this->emailer->addAttrs($vars, $default['vars']), 'default' => $default];
    }

    private function getEmailVals(array $data, array $default): array
    {
        $vals = [
            $this->emailer->get('tennguoigui'),
            $this->emailer->get('thongtin'),
            $this->emailer->get('tieudelienhe'),
            $this->emailer->get('noidunglienhe'),
        ];
        return ['vals' => $this->emailer->addAttrs($vals, $default['vals'])];
    }
}
