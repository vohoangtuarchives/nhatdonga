<?php

namespace Tuezy;

/**
 * EmailTemplateHelper - Email template management
 * Simplifies email template operations
 */
class EmailTemplateHelper
{
    private $emailer;
    private array $defaultVars = [];
    private array $defaultVals = [];

    public function __construct($emailer)
    {
        $this->emailer = $emailer;
        $this->loadDefaults();
    }

    /**
     * Load default email attributes
     */
    private function loadDefaults(): void
    {
        $defaultAttrs = $this->emailer->defaultAttrs();
        $this->defaultVars = $defaultAttrs['vars'] ?? [];
        $this->defaultVals = $defaultAttrs['vals'] ?? [];
    }

    /**
     * Render email template
     * 
     * @param string $template Template name (e.g., 'contact/admin')
     * @param array $vars Additional variables
     * @param array $vals Additional values
     * @return string Rendered email content
     */
    public function render(string $template, array $vars = [], array $vals = []): string
    {
        $allVars = $this->emailer->addAttrs($vars, $this->defaultVars);
        $allVals = $this->emailer->addAttrs($vals, $this->defaultVals);
        
        $content = $this->emailer->markdown($template);
        return str_replace($allVars, $allVals, $content);
    }

    /**
     * Send email to admin
     * 
     * @param string $template Template name
     * @param string $subject Email subject
     * @param array $vars Variables
     * @param array $vals Values
     * @param string $fileAttachment File attachment field name
     * @return bool Success status
     */
    public function sendToAdmin(string $template, string $subject, array $vars = [], array $vals = [], string $fileAttachment = ''): bool
    {
        $message = $this->render($template, $vars, $vals);
        return $this->emailer->send('admin', null, $subject, $message, $fileAttachment);
    }

    /**
     * Send email to customer
     * 
     * @param string $template Template name
     * @param string $subject Email subject
     * @param array $customerData Customer data ['name' => '', 'email' => '']
     * @param array $vars Variables
     * @param array $vals Values
     * @param string $fileAttachment File attachment field name
     * @return bool Success status
     */
    public function sendToCustomer(string $template, string $subject, array $customerData, array $vars = [], array $vals = [], string $fileAttachment = ''): bool
    {
        $message = $this->render($template, $vars, $vals);
        $arrayEmail = [
            'dataEmail' => $customerData,
        ];
        return $this->emailer->send('customer', $arrayEmail, $subject, $message, $fileAttachment);
    }

    /**
     * Set email data
     * 
     * @param string $key Data key
     * @param mixed $value Data value
     */
    public function setData(string $key, $value): void
    {
        $this->emailer->set($key, $value);
    }

    /**
     * Get email data
     * 
     * @param string $key Data key
     * @return mixed
     */
    public function getData(string $key)
    {
        return $this->emailer->get($key);
    }

    /**
     * Prepare contact form data
     * 
     * @param array $data Contact form data
     * @return array ['vars' => array, 'vals' => array]
     */
    public function prepareContactData(array $data): array
    {
        $this->setData('tennguoigui', $data['fullname'] ?? '');
        $this->setData('emailnguoigui', $data['email'] ?? '');
        $this->setData('dienthoainguoigui', $data['phone'] ?? '');
        $this->setData('diachinguoigui', $data['address'] ?? '');
        $this->setData('tieudelienhe', $data['subject'] ?? '');
        $this->setData('noidunglienhe', $data['content'] ?? '');

        // Build info string
        $strThongtin = '';
        if ($this->getData('tennguoigui')) {
            $strThongtin .= '<span style="text-transform:capitalize">' . $this->getData('tennguoigui') . '</span><br>';
        }
        if ($this->getData('emailnguoigui')) {
            $strThongtin .= '<a href="mailto:' . $this->getData('emailnguoigui') . '" target="_blank">' . $this->getData('emailnguoigui') . '</a><br>';
        }
        if ($this->getData('diachinguoigui')) {
            $strThongtin .= '' . $this->getData('diachinguoigui') . '<br>';
        }
        if ($this->getData('dienthoainguoigui')) {
            $strThongtin .= 'Tel: ' . $this->getData('dienthoainguoigui') . '';
        }
        $this->setData('thongtin', $strThongtin);

        return [
            'vars' => [
                '{emailTitleSender}',
                '{emailInfoSender}',
                '{emailSubjectSender}',
                '{emailContentSender}',
            ],
            'vals' => [
                $this->getData('tennguoigui'),
                $this->getData('thongtin'),
                $this->getData('tieudelienhe'),
                $this->getData('noidunglienhe'),
            ],
        ];
    }
}

