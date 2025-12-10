<?php

namespace Tuezy;

/**
 * FormHandler - Handles form submissions (contact, newsletter, etc.)
 * Refactors repetitive form handling logic
 */
class FormHandler
{
    private $d;
    private $func;
    private $emailer;
    private $flash;
    private ValidationHelper $validator;
    private string $configBase;
    private string $lang;
    private array $setting;
    private \Tuezy\Application\Contact\SubmitContactHandler $submitHandler;

    public function __construct($d, $func, $emailer, $flash, ValidationHelper $validator, string $configBase, string $lang, array $setting)
    {
        $this->d = $d;
        $this->func = $func;
        $this->emailer = $emailer;
        $this->flash = $flash;
        $this->validator = $validator;
        $this->configBase = $configBase;
        $this->lang = $lang;
        $this->setting = $setting;
        $repo = new \Tuezy\Infrastructure\Persistence\Contact\PDODbContactRepository($this->d);
        $this->submitHandler = new \Tuezy\Application\Contact\SubmitContactHandler($repo, $this->validator, $this->emailer);
    }

    /**
     * Handle contact form submission
     * 
     * @param array $data Contact form data
     * @param string $recaptchaResponse Recaptcha response
     * @return bool Success status
     */
    public function handleContact(array $data, string $recaptchaResponse): bool
    {
        $command = new \Tuezy\Application\Contact\SubmitContactCommand($data, $recaptchaResponse);
        $result = $this->submitHandler->handle($command);

        if (!$result['success']) {
            $errors = $result['errors'] ?? [];
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $this->flash->set($k, $v);
                }
            }
            $response = [
                'status' => 'danger',
                'messages' => $errors
            ];
            $message = base64_encode(json_encode($response));
            $this->flash->set('message', $message);
            $this->func->redirect('lien-he');
            return false;
        }

        $idInsert = $this->d->getLastInsertId();
        if ($this->func->hasFile('file_attach')) {
            $this->handleFileUpload($idInsert, 'contact');
        }
        $this->func->transfer("Gửi liên hệ thành công", $this->configBase);
        return true;
    }

    /**
     * Handle newsletter form submission
     * 
     * @param array $data Newsletter form data
     * @param string $recaptchaResponse Recaptcha response
     * @return bool Success status
     */
    public function handleNewsletter(array $data, string $recaptchaResponse): bool
    {
        // Validate recaptcha
        if (!$this->validator->recaptcha($recaptchaResponse, 'Newsletter')) {
            $this->func->transfer("Thông tin không được gửi đi. Vui lòng thử lại sau.", $this->configBase, false);
            return false;
        }

        // Validate data
        if (!$this->validator->validateNewsletter($data)) {
            $error = $this->validator->getErrors()[0] ?? 'Email không hợp lệ';
            $this->flash->set('error', $error);
            $this->func->transfer($error, $this->configBase, false);
            return false;
        }

        // Prepare data for database
        $dbData = [
            'email' => htmlspecialchars($data['email']),
            'phone' => $data['phone'] ?? '',
            'fullname' => $data['fullname'] ?? '',
            'address' => $data['address'] ?? '',
            'subject' => $data['subject'] ?? '',
            'content' => $data['content'] ?? '',
            'date_created' => time(),
            'type' => $data['type'] ?? 'dangkynhantin',
        ];

        // Send emails first
        if ($this->sendNewsletterEmails($dbData)) {
            // Insert to database
            if ($this->d->insert('newsletter', $dbData)) {
                $idInsert = $this->d->getLastInsertId();

                // Handle file upload
                if ($this->func->hasFile("file_attach")) {
                    $this->handleFileUpload($idInsert, 'newsletter');
                }

                $this->func->transfer("Thông tin đã được gửi. Chúng tôi sẽ liên hệ với bạn sớm.", $this->configBase);
                return true;
            } else {
                $this->func->transfer("Đăng ký nhận tin thất bại. Vui lòng thử lại sau.", $this->configBase, false);
                return false;
            }
        }

        $this->func->transfer("Gửi liên hệ thất bại. Vui lòng thử lại sau.", $this->configBase, false);
        return false;
    }

    /**
     * Handle file upload
     * 
     * @param int $idInsert Inserted record ID
     * @param string $table Table name
     */
    private function handleFileUpload(int $idInsert, string $table): void
    {
        $fileUpdate = [];
        $file_name = $this->func->uploadName($_FILES['file_attach']["name"]);
        $allowedExtensions = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.DOC|.DOCX|.PDF|.RAR|.ZIP|.PPT|.PPTX|.xls|.xlsx|.jpg|.png|.gif|.JPG|.PNG|.GIF';
        
        $uploadPath = ($table === 'contact') ? UPLOAD_FILE_L : UPLOAD_FILE_L;
        
        if ($file_attach = $this->func->uploadImage("file_attach", $allowedExtensions, $uploadPath, $file_name)) {
            $fileUpdate['file_attach'] = $file_attach;
            $this->d->where('id', $idInsert);
            $this->d->update($table, $fileUpdate);
        }
    }

    /**
     * Send contact emails
     * 
     * @param array $data Contact data
     * @return bool
     */
    private function sendContactEmails(array $data): bool
    {
        $this->prepareEmailData($data);
        $emailVars = $this->getEmailVars();
        $emailVals = $this->getEmailVals();

        $subject = "Thư liên hệ từ " . $this->setting['name' . $this->lang];
        $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('contact/admin'));

        if ($this->emailer->send("admin", null, $subject, $message, 'file_attach')) {
            // Send to customer
            $arrayEmail = [
                "dataEmail" => [
                    "name" => $data['fullname'],
                    "email" => $data['email']
                ]
            ];
            $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('contact/customer'));
            return $this->emailer->send("customer", $arrayEmail, $subject, $message, 'file_attach');
        }

        return false;
    }

    /**
     * Send newsletter emails
     * 
     * @param array $data Newsletter data
     * @return bool
     */
    private function sendNewsletterEmails(array $data): bool
    {
        $this->prepareEmailData($data);
        $emailVars = $this->getEmailVars();
        $emailVals = $this->getEmailVals();

        $subject = "Thư liên hệ từ " . $this->setting['name' . $this->lang];
        $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('newsletter/admin'));

        if ($this->emailer->send("admin", null, $subject, $message, 'file_attach')) {
            // Send to customer
            $arrayEmail = [
                "dataEmail" => [
                    "name" => $data['fullname'] ?? '',
                    "email" => $data['email']
                ]
            ];
            $message = str_replace($emailVars, $emailVals, $this->emailer->markdown('newsletter/customer'));
            return $this->emailer->send("customer", $arrayEmail, $subject, $message, 'file_attach');
        }

        return false;
    }

    /**
     * Prepare email data
     * 
     * @param array $data Form data
     */
    private function prepareEmailData(array $data): void
    {
        $this->emailer->set('tennguoigui', $data['fullname'] ?? '');
        $this->emailer->set('emailnguoigui', $data['email'] ?? '');
        $this->emailer->set('dienthoainguoigui', $data['phone'] ?? '');
        $this->emailer->set('diachinguoigui', $data['address'] ?? '');
        $this->emailer->set('tieudelienhe', $data['subject'] ?? '');
        $this->emailer->set('noidunglienhe', $data['content'] ?? '');

        // Build info string
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
            $strThongtin .= 'Tel: ' . $this->emailer->get('dienthoainguoigui') . '';
        }

        $this->emailer->set('thongtin', $strThongtin);
    }

    /**
     * Get email variables
     * 
     * @return array
     */
    private function getEmailVars(): array
    {
        $emailDefaultAttrs = $this->emailer->defaultAttrs();
        $emailVars = [
            '{emailTitleSender}',
            '{emailInfoSender}',
            '{emailSubjectSender}',
            '{emailContentSender}'
        ];
        return $this->emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);
    }

    /**
     * Get email values
     * 
     * @return array
     */
    private function getEmailVals(): array
    {
        $emailDefaultAttrs = $this->emailer->defaultAttrs();
        $emailVals = [
            $this->emailer->get('tennguoigui'),
            $this->emailer->get('thongtin'),
            $this->emailer->get('tieudelienhe'),
            $this->emailer->get('noidunglienhe')
        ];
        return $this->emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);
    }
}

