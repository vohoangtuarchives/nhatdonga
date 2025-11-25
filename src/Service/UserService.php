<?php

namespace Tuezy\Service;

use Tuezy\Repository\UserRepository;

/**
 * UserService - Business logic layer for users
 */
class UserService
{
    public function __construct(
        private UserRepository $users,
        private \PDODb $db
    ) {
    }

    /**
     * Register new user
     * 
     * @param array $data User data (fullname, email, phone, password, address)
     * @return int|false User ID on success, false on failure
     */
    public function register(array $data): int|false
    {
        // Check if email exists
        if ($this->users->emailExists($data['email'] ?? '')) {
            return false;
        }

        // Prepare user data
        $userData = [
            'fullname' => htmlspecialchars($data['fullname'] ?? ''),
            'email' => htmlspecialchars($data['email'] ?? ''),
            'phone' => htmlspecialchars($data['phone'] ?? ''),
            'address' => htmlspecialchars($data['address'] ?? ''),
            'password' => md5($data['password'] ?? ''),
            'status' => 'hienthi',
        ];

        return $this->users->create($userData);
    }

    /**
     * Login user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array|null User data on success, null on failure
     */
    public function login(string $username, string $password): ?array
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        $user = $this->users->getByUsernameOrEmail($username);
        
        if (!$user || $user['password'] !== md5($password)) {
            return null;
        }

        // Update last login
        $this->users->updateLastLogin($user['id']);

        return $user;
    }

    /**
     * Update user profile
     * 
     * @param int $id User ID
     * @param array $data User data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool
    {
        // Check if email exists (excluding current user)
        if (!empty($data['email']) && $this->users->emailExists($data['email'], $id)) {
            return false;
        }

        // Prepare update data
        $updateData = [];
        if (isset($data['fullname'])) {
            $updateData['fullname'] = htmlspecialchars($data['fullname']);
        }
        if (isset($data['email'])) {
            $updateData['email'] = htmlspecialchars($data['email']);
        }
        if (isset($data['phone'])) {
            $updateData['phone'] = htmlspecialchars($data['phone']);
        }
        if (isset($data['address'])) {
            $updateData['address'] = htmlspecialchars($data['address']);
        }

        return $this->users->update($id, $updateData);
    }

    /**
     * Update password
     * 
     * @param int $id User ID
     * @param string $oldPassword Old password
     * @param string $newPassword New password
     * @return bool
     */
    public function updatePassword(int $id, string $oldPassword, string $newPassword): bool
    {
        $user = $this->users->getById($id);
        
        if (!$user || $user['password'] !== md5($oldPassword)) {
            return false;
        }

        return $this->users->updatePassword($id, $newPassword);
    }

    /**
     * Forgot password - generate reset code
     * 
     * @param string $email Email address
     * @return string|false Reset code on success, false on failure
     */
    public function forgotPassword(string $email): string|false
    {
        $user = $this->users->getByEmail($email);
        
        if (!$user) {
            return false;
        }

        // Generate reset code
        $resetCode = bin2hex(random_bytes(16));
        
        $this->users->update($user['id'], [
            'reset_code' => $resetCode,
            'reset_time' => time(),
        ]);

        return $resetCode;
    }

    /**
     * Reset password using reset code
     * 
     * @param string $code Reset code
     * @param string $password New password
     * @return bool
     */
    public function resetPassword(string $code, string $password): bool
    {
        $user = $this->db->rawQueryOne(
            "SELECT * FROM #_member WHERE reset_code = ? AND reset_time > ? LIMIT 0,1",
            [$code, time() - 3600] // Code valid for 1 hour
        );

        if (!$user) {
            return false;
        }

        // Update password and clear reset code
        $this->users->update($user['id'], [
            'password' => md5($password),
            'reset_code' => '',
            'reset_time' => 0,
        ]);

        return true;
    }

    /**
     * Get user listing with filters and pagination
     * 
     * @param array $filters Filters
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function getListing(array $filters, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;

        $items = $this->users->getAll($filters, $start, $perPage);
        $total = $this->users->count($filters);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
        ];
    }
}

