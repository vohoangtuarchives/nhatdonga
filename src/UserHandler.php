<?php

namespace Tuezy;

use Tuezy\SecurityHelper;

/**
 * UserHandler - Handles user authentication and account management
 * Centralizes user-related operations
 */
class UserHandler
{
    private $d;
    private $func;
    private $flash;
    private ValidationHelper $validator;
    private string $configBase;
    private string $loginMember;
    private array $config;

    public function __construct($d, $func, $flash, ValidationHelper $validator, string $configBase, string $loginMember, array $config)
    {
        $this->d = $d;
        $this->func = $func;
        $this->flash = $flash;
        $this->validator = $validator;
        $this->configBase = $configBase;
        $this->loginMember = $loginMember;
        $this->config = $config;
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

        $passwordMD5 = md5($password);
        $row = $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE (username = ? OR email = ?) AND password = ? AND find_in_set('hienthi',status) LIMIT 0,1",
            [$username, $username, $passwordMD5]
        );

        if (empty($row)) {
            $this->flash->set('error', 'Thông tin đăng nhập không chính xác');
            return false;
        }

        // Set session
        $_SESSION[$this->loginMember] = [
            'active' => true,
            'id' => $row['id'],
            'username' => $row['username'],
            'login_session' => md5(sha1($row['password'] . $row['username'])),
        ];

        // Remember me
        if ($remember) {
            setcookie('login_member_id', $row['id'], time() + 86400 * 30, '/');
            setcookie('login_member_session', md5(sha1($row['password'] . $row['username'])), time() + 86400 * 30, '/');
        }

        // Update last login
        $this->d->where('id', $row['id']);
        $this->d->update('member', ['lastlogin' => time()]);

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
            if ($this->func->checkAccount($data['email'], 'email', 'member')) {
                $this->validator->getErrors(); // Add error
                $this->flash->set('error', 'Email đã tồn tại');
                return false;
            }
        }
        $this->validator->required($data['phone'] ?? '', 'Số điện thoại không được trống');
        if (!empty($data['phone'])) {
            $this->validator->phone($data['phone'], 'Số điện thoại không hợp lệ');
        }
        $this->validator->required($data['password'] ?? '', 'Mật khẩu không được trống');

        if (!empty($this->validator->getErrors())) {
            return false;
        }

        // Prepare data
        $userData = [
            'fullname' => htmlspecialchars($data['fullname']),
            'email' => htmlspecialchars($data['email']),
            'phone' => htmlspecialchars($data['phone'] ?? ''),
            'address' => htmlspecialchars($data['address'] ?? ''),
            'password' => md5($data['password']),
            'date_created' => time(),
            'status' => 'hienthi',
            'numb' => 0,
        ];

        // Create user
        if ($this->d->insert('member', $userData)) {
            return true;
        }

        $this->flash->set('error', 'Đăng ký thất bại. Vui lòng thử lại sau.');
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
            if ($this->func->checkAccount($data['email'], 'email', 'member', $userId)) {
                $this->flash->set('error', 'Email đã tồn tại');
                return false;
            }
        }
        $this->validator->required($data['phone'] ?? '', 'Số điện thoại không được trống');
        if (!empty($data['phone'])) {
            $this->validator->phone($data['phone'], 'Số điện thoại không hợp lệ');
        }

        // Check old password if changing password
        if ($oldPassword && $newPassword) {
            $user = $this->d->rawQueryOne("SELECT password FROM #_member WHERE id = ? LIMIT 0,1", [$userId]);
            if ($user['password'] != md5($oldPassword)) {
                $this->flash->set('error', 'Mật khẩu cũ không chính xác');
                return false;
            }
        }

        if (!empty($this->validator->getErrors())) {
            return false;
        }

        // Prepare update data
        $updateData = [
            'fullname' => htmlspecialchars($data['fullname']),
            'email' => htmlspecialchars($data['email']),
            'phone' => htmlspecialchars($data['phone']),
            'address' => htmlspecialchars($data['address'] ?? ''),
        ];

        if ($newPassword) {
            $updateData['password'] = md5($newPassword);
        }

        // Update
        $this->d->where('id', $userId);
        if ($this->d->update('member', $updateData)) {
            // If password changed, logout
            if ($newPassword) {
                $this->logout();
                $this->func->transfer("Cập nhật thông tin thành công", $this->configBase . "account/dang-nhap");
                return true;
            }
            return true;
        }

        $this->flash->set('error', 'Cập nhật thông tin thất bại');
        return false;
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

        $row = $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE email = ? AND find_in_set('hienthi',status) LIMIT 0,1",
            [$email]
        );

        if (empty($row)) {
            $this->flash->set('error', 'Email không tồn tại trong hệ thống');
            return false;
        }

        // Generate reset code
        $resetCode = $this->func->stringRandom(32);
        $this->d->where('id', $row['id']);
        $this->d->update('member', ['reset_code' => $resetCode, 'reset_time' => time()]);

        // Send email (implement email sending logic here)
        // ...

        return true;
    }
}

