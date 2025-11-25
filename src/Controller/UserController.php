<?php

namespace Tuezy\Controller;

use Tuezy\UserHandler;
use Tuezy\ValidationHelper;
use Tuezy\SecurityHelper;

/**
 * UserController - Handles user account requests
 */
class UserController extends BaseController
{
    private UserHandler $userHandler;
    private ValidationHelper $validator;
    private string $loginMember;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        $flash,
        string $loginMember
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $configBase = $config['database']['url'] ?? '';
        $this->loginMember = $loginMember;
        $this->validator = new ValidationHelper($func, $config);
        $this->userHandler = new UserHandler($db, $func, $flash, $this->validator, $configBase, $loginMember, $config, $cache ?? null);
    }

    /**
     * Handle login
     * 
     * @return array View data
     */
    public function login(): array
    {
        $configBase = $this->config['database']['url'] ?? '';

        if (!empty($_SESSION[$this->loginMember]['active'])) {
            $this->func->transfer("Trang không tồn tại", $configBase, false);
        }

        if (!empty($_POST['login-user'])) {
            $username = SecurityHelper::sanitizePost('username');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);

            if ($this->userHandler->login($username, $password, $remember)) {
                $this->redirect($configBase);
            } else {
                $this->redirect($configBase . "account/dang-nhap");
            }
        }

        return [
            'titleMain' => 'dangnhap',
        ];
    }

    /**
     * Handle registration
     * 
     * @return array View data
     */
    public function register(): array
    {
        $configBase = $this->config['database']['url'] ?? '';

        if (!empty($_SESSION[$this->loginMember]['active'])) {
            $this->func->transfer("Trang không tồn tại", $configBase, false);
        }

        if (!empty($_POST['register-user'])) {
            $dataUser = $_POST['dataUser'] ?? [];
            $recaptchaResponse = $_POST['recaptcha_response_register'] ?? '';

            if ($this->userHandler->register($dataUser, $recaptchaResponse)) {
                $this->redirect($configBase . "account/dang-nhap");
            }
        }

        return [
            'titleMain' => 'dangky',
        ];
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        $this->userHandler->logout();
        $configBase = $this->config['database']['url'] ?? '';
        $this->redirect($configBase);
    }

    /**
     * Display user profile
     * 
     * @return array View data
     */
    public function profile(): array
    {
        $configBase = $this->config['database']['url'] ?? '';

        if (empty($_SESSION[$this->loginMember]['active'])) {
            $this->redirect($configBase . "account/dang-nhap");
        }

        $user = $this->userHandler->getCurrentUser();

        if (!empty($_POST['update-profile'])) {
            $dataUser = $_POST['dataUser'] ?? [];
            if ($this->userHandler->updateProfile($dataUser)) {
                $this->redirect($configBase . "account/thong-tin");
            }
        }

        return [
            'user' => $user,
            'titleMain' => 'thongtin',
        ];
    }
}

