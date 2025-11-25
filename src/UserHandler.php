<?php

namespace Tuezy;

use Tuezy\SecurityHelper;
use Tuezy\Service\UserService;
use Tuezy\Repository\UserRepository;

/**
 * UserHandler - Handles user authentication and account management
 * Centralizes user-related operations
 * Now uses UserService for business logic
 */
class UserHandler
{
    private $d;
    private $func;
    private $flash;
    private ValidationHelper $validator;
    private UserService $userService;
    private string $configBase;
    private string $loginMember;
    private array $config;

    public function __construct($d, $func, $flash, ValidationHelper $validator, string $configBase, string $loginMember, array $config, $cache = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->flash = $flash;
        $this->validator = $validator;
        $this->configBase = $configBase;
        $this->loginMember = $loginMember;
        $this->config = $config;
        
        // Initialize UserService
        $userRepo = new UserRepository($d, $cache);
        $this->userService = new UserService($userRepo, $d);
    }

    /**
     * Login user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @param bool $remember Remember login
     * @return bool Success status
     */
    public function login(string $username, string $password, bool $remember = false): bool
    {
        if (empty($username) || empty($password)) {
            $this->flash->set('error', 'Vui lòng nhập đầy đủ thông tin');
            return false;
        }

        // Use UserService for login
        $user = $this->userService->login($username, $password);

        if (!$user) {
            $this->flash->set('error', 'Thông tin đăng nhập không chính xác');
            return false;
        }

        // Set session
        $_SESSION[$this->loginMember] = [
            'active' => true,
            'id' => $user['id'],
            'username' => $user['username'] ?? '',
            'login_session' => md5(sha1($user['password'] . ($user['username'] ?? ''))),
        ];

        // Remember me
        if ($remember) {
            setcookie('login_member_id', $user['id'], time() + 86400 * 30, '/');
            setcookie('login_member_session', md5(sha1($user['password'] . ($user['username'] ?? ''))), time() + 86400 * 30, '/');
        }

        return true;
    }

    /**
     * Register new user
     * 
     * @param array $data User data
     * @return bool Success status
     */
    public function register(array $data): bool
    {
        // Validate
        $this->validator->required($data['fullname'] ?? '', 'Họ tên không được trống');
        $this->validator->required($data['email'] ?? '', 'Email không được trống');
        if (!empty($data['email'])) {
            $this->validator->email($data['email'], 'Email không hợp lệ');
        }
        $this->validator->required($data['phone'] ?? '', 'Số điện thoại không được trống');
        if (!empty($data['phone'])) {
            $this->validator->phone($data['phone'], 'Số điện thoại không hợp lệ');
        }
        $this->validator->required($data['password'] ?? '', 'Mật khẩu không được trống');

        if (!empty($this->validator->getErrors())) {
            return false;
        }

        // Use UserService for registration
        $userId = $this->userService->register($data);

        if ($userId) {
            return true;
        }

        $this->flash->set('error', 'Đăng ký thất bại. Email có thể đã tồn tại.');
        return false;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        unset($_SESSION[$this->loginMember]);
        setcookie('login_member_id', "", -1, '/');
        setcookie('login_member_session', "", -1, '/');
    }

    /**
     * Update user info
     * 
     * @param int $userId User ID
     * @param array $data User data
     * @param string|null $oldPassword Old password (if changing password)
     * @param string|null $newPassword New password
     * @return bool Success status
     */
    public function updateInfo(int $userId, array $data, ?string $oldPassword = null, ?string $newPassword = null): bool
    {
        // Validate
        $this->validator->required($data['fullname'] ?? '', 'Họ tên không được trống');
        $this->validator->required($data['email'] ?? '', 'Email không được trống');
        if (!empty($data['email'])) {
            $this->validator->email($data['email'], 'Email không hợp lệ');
        }
        $this->validator->required($data['phone'] ?? '', 'Số điện thoại không được trống');
        if (!empty($data['phone'])) {
            $this->validator->phone($data['phone'], 'Số điện thoại không hợp lệ');
        }

        if (!empty($this->validator->getErrors())) {
            return false;
        }

        // Update profile using UserService
        if (!$this->userService->updateProfile($userId, $data)) {
            $this->flash->set('error', 'Cập nhật thông tin thất bại. Email có thể đã tồn tại.');
            return false;
        }

        // Update password if provided
        if ($oldPassword && $newPassword) {
            if (!$this->userService->updatePassword($userId, $oldPassword, $newPassword)) {
                $this->flash->set('error', 'Mật khẩu cũ không chính xác');
                return false;
            }
            // If password changed, logout
            $this->logout();
            $this->func->transfer("Cập nhật thông tin thành công", $this->configBase . "account/dang-nhap");
            return true;
        }

        return true;
    }

    /**
     * Forgot password
     * 
     * @param string $email Email address
     * @return bool Success status
     */
    public function forgotPassword(string $email): bool
    {
        if (empty($email)) {
            $this->flash->set('error', 'Email không được trống');
            return false;
        }

        if (!$this->validator->email($email, 'Email không hợp lệ')) {
            return false;
        }

        // Use UserService for forgot password
        $resetCode = $this->userService->forgotPassword($email);

        if (!$resetCode) {
            $this->flash->set('error', 'Email không tồn tại trong hệ thống');
            return false;
        }

        // Send email (implement email sending logic here)
        // TODO: Send email with reset code
        // ...

        return true;
    }
}

