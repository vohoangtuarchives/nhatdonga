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
     * Check if email is valid (static method for convenience)
     * 
     * @param string $email Email address
     * @return bool
     */
    public static function isEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
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

    /**
     * Validate string length
     * 
     * @param string $value Value to validate
     * @param int $min Minimum length
     * @param int $max Maximum length
     * @param string $fieldName Field name for error message
     * @return bool
     */
    public function length(string $value, int $min, int $max, string $fieldName): bool
    {
        $len = mb_strlen($value, 'UTF-8');
        if ($len < $min || $len > $max) {
            $this->errors[] = "{$fieldName} phải có độ dài từ {$min} đến {$max} ký tự";
            return false;
        }
        return true;
    }

    /**
     * Validate numeric value
     * 
     * @param mixed $value Value to validate
     * @param string $fieldName Field name for error message
     * @param float|null $min Minimum value
     * @param float|null $max Maximum value
     * @return bool
     */
    public function numeric($value, string $fieldName, ?float $min = null, ?float $max = null): bool
    {
        if (!is_numeric($value)) {
            $this->errors[] = "{$fieldName} phải là số";
            return false;
        }

        $num = (float)$value;
        if ($min !== null && $num < $min) {
            $this->errors[] = "{$fieldName} phải lớn hơn hoặc bằng {$min}";
            return false;
        }
        if ($max !== null && $num > $max) {
            $this->errors[] = "{$fieldName} phải nhỏ hơn hoặc bằng {$max}";
            return false;
        }

        return true;
    }

    /**
     * Validate URL
     * 
     * @param string $url URL to validate
     * @param string $errorMessage Custom error message
     * @return bool
     */
    public function url(string $url, ?string $errorMessage = null): bool
    {
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->errors[] = $errorMessage ?? 'URL không hợp lệ';
            return false;
        }
        return true;
    }

    /**
     * Validate date format
     * 
     * @param string $date Date string
     * @param string $format Date format (default: Y-m-d)
     * @param string $errorMessage Custom error message
     * @return bool
     */
    public function date(string $date, string $format = 'Y-m-d', ?string $errorMessage = null): bool
    {
        if (!empty($date)) {
            $d = \DateTime::createFromFormat($format, $date);
            if (!$d || $d->format($format) !== $date) {
                $this->errors[] = $errorMessage ?? 'Định dạng ngày không hợp lệ';
                return false;
            }
        }
        return true;
    }

    /**
     * Validate password strength
     * 
     * @param string $password Password to validate
     * @param int $minLength Minimum length
     * @param bool $requireUppercase Require uppercase letter
     * @param bool $requireNumber Require number
     * @return bool
     */
    public function password(string $password, int $minLength = 6, bool $requireUppercase = false, bool $requireNumber = false): bool
    {
        if (mb_strlen($password, 'UTF-8') < $minLength) {
            $this->errors[] = "Mật khẩu phải có ít nhất {$minLength} ký tự";
            return false;
        }

        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "Mật khẩu phải có ít nhất một chữ hoa";
            return false;
        }

        if ($requireNumber && !preg_match('/[0-9]/', $password)) {
            $this->errors[] = "Mật khẩu phải có ít nhất một số";
            return false;
        }

        return true;
    }

    /**
     * Validate array of values
     * 
     * @param array $values Values to validate
     * @param callable $validator Validator function
     * @param string $fieldName Field name for error message
     * @return bool
     */
    public function array(array $values, callable $validator, string $fieldName): bool
    {
        foreach ($values as $index => $value) {
            if (!$validator($value)) {
                $this->errors[] = "{$fieldName}[{$index}] không hợp lệ";
                return false;
            }
        }
        return true;
    }

    /**
     * Validate order data
     * 
     * @param array $data Order data
     * @return bool
     */
    public function validateOrder(array $data): bool
    {
        $this->clear();
        
        $this->required($data['payments'] ?? null, 'Hình thức thanh toán');
        $this->required($data['fullname'] ?? null, 'Họ tên');
        $this->required($data['phone'] ?? null, 'Số điện thoại');
        $this->phone($data['phone'] ?? '');
        $this->required($data['city'] ?? null, 'Tỉnh/thành phố');
        $this->required($data['district'] ?? null, 'Quận/huyện');
        $this->required($data['ward'] ?? null, 'Phường/xã');
        $this->required($data['address'] ?? null, 'Địa chỉ');
        $this->required($data['email'] ?? null, 'Email');
        $this->email($data['email'] ?? '');

        return $this->isValid();
    }

    /**
     * Validate user registration data
     * 
     * @param array $data User data
     * @return bool
     */
    public function validateUserRegistration(array $data): bool
    {
        $this->clear();
        
        $this->required($data['fullname'] ?? null, 'Họ tên');
        $this->required($data['email'] ?? null, 'Email');
        $this->email($data['email'] ?? '');
        $this->required($data['phone'] ?? null, 'Số điện thoại');
        $this->phone($data['phone'] ?? '');
        $this->required($data['password'] ?? null, 'Mật khẩu');
        $this->password($data['password'] ?? '', 6);

        return $this->isValid();
    }
}

