<?php

namespace Tuezy;

/**
 * ValidationHelper - Centralized validation logic
 * Refactors repetitive validation code in form handlers
 */
class ValidationHelper
{
    private array $errors = [];
    private $func;

    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * Validate required field
     * 
     * @param mixed $value Field value
     * @param string $fieldName Field name for error message
     * @param string $errorMessage Custom error message
     * @return bool
     */
    public function required($value, string $fieldName, ?string $errorMessage = null): bool
    {
        if (empty($value)) {
            $this->errors[] = $errorMessage ?? "$fieldName không được trống";
            return false;
        }
        return true;
    }

    /**
     * Validate email
     * 
     * @param string $email Email address
     * @param string $errorMessage Custom error message
     * @return bool
     */
    public function email(string $email, ?string $errorMessage = null): bool
    {
        if (!empty($email) && !$this->func->isEmail($email)) {
            $this->errors[] = $errorMessage ?? 'Email không hợp lệ';
            return false;
        }
        return true;
    }

    /**
     * Validate phone
     * 
     * @param string $phone Phone number
     * @param string $errorMessage Custom error message
     * @return bool
     */
    public function phone(string $phone, ?string $errorMessage = null): bool
    {
        if (!empty($phone) && !$this->func->isPhone($phone)) {
            $this->errors[] = $errorMessage ?? 'Số điện thoại không hợp lệ';
            return false;
        }
        return true;
    }

    /**
     * Validate recaptcha
     * 
     * @param string $response Recaptcha response
     * @param string $action Expected action
     * @param float $minScore Minimum score
     * @return bool
     */
    public function recaptcha(string $response, string $action, float $minScore = 0.5): bool
    {
        $result = $this->func->checkRecaptcha($response);
        $score = $result['score'] ?? 0;
        $resultAction = $result['action'] ?? '';
        $test = $result['test'] ?? false;

        // Allow test mode
        if ($test) {
            return true;
        }

        return ($score >= $minScore && $resultAction === $action);
    }

    /**
     * Get all errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if validation passed
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Clear errors
     */
    public function clear(): void
    {
        $this->errors = [];
    }

    /**
     * Validate contact form data
     * 
     * @param array $data Contact data
     * @return bool
     */
    public function validateContact(array $data): bool
    {
        $this->clear();
        
        $this->required($data['fullname'] ?? null, 'Họ tên');
        $this->required($data['phone'] ?? null, 'Số điện thoại');
        $this->phone($data['phone'] ?? '');
        $this->required($data['address'] ?? null, 'Địa chỉ');
        $this->required($data['email'] ?? null, 'Email');
        $this->email($data['email'] ?? '');
        $this->required($data['subject'] ?? null, 'Chủ đề');
        $this->required($data['content'] ?? null, 'Nội dung');

        return $this->isValid();
    }

    /**
     * Validate newsletter form data
     * 
     * @param array $data Newsletter data
     * @return bool
     */
    public function validateNewsletter(array $data): bool
    {
        $this->clear();
        
        $this->required($data['email'] ?? null, 'Email');
        $this->email($data['email'] ?? '');

        return $this->isValid();
    }
}

