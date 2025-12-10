<?php
namespace Tuezy\Service;

class AuthService
{
    public function __construct(private $d, private $cache)
    {
    }

    public function checkLoginAdmin(): bool
    {
        global $loginAdmin;
        $token = (!empty($_SESSION[$loginAdmin]['token'])) ? $_SESSION[$loginAdmin]['token'] : '';
        $row = $this->d->rawQuery("select secret_key from #_user where secret_key = ? and find_in_set('hienthi',status)", array($token));
        if (count($row) == 1 && $row[0]['secret_key'] != '') {
            return true;
        } else {
            if (!empty($_SESSION[\TOKEN])) unset($_SESSION[\TOKEN]);
            unset($_SESSION[$loginAdmin]);
            return false;
        }
    }

    public function encryptPassword($secret = '', $str = '', $salt = ''): string
    {
        return md5($secret . $str . $salt);
    }

    public function checkPermission($com = '', $act = '', $type = '', $array = null, $case = ''): bool
    {
        global $loginAdmin;
        $str = $com;
        if ($act) $str .= '_' . $act;
        if ($case == 'phrase-1') {
            if ($type != '') $str .= '_' . $type;
            if (!in_array($str, $_SESSION[$loginAdmin]['permissions'])) return true;
            else return false;
        } else if ($case == 'phrase-2') {
            $count = 0;
            if ($array) {
                foreach ($array as $key => $value) {
                    if (!empty($value['dropdown'])) {
                        unset($array[$key]);
                    }
                }
                foreach ($array as $key => $value) {
                    if (!in_array($str . "_" . $key, $_SESSION[$loginAdmin]['permissions'])) $count++;
                }
                if ($count == count($array)) return true;
            } else return false;
        }
        return false;
    }

    public function checkRole($config = null, $loginAdmin = null): bool
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        if ($loginAdmin === null) {
            if (function_exists('Tuezy\\config')) {
                $loginAdmin = $config['login']['admin'] ?? 'login_admin';
            } else {
                global $loginAdmin;
            }
        }
        if ((!empty($_SESSION[$loginAdmin]['role']) && $_SESSION[$loginAdmin]['role'] == 3) || !empty($config['website']['debug-developer'])) return false;
        else return true;
    }

    public function checkLoginMember($configBase = null, $loginMember = null)
    {
        if ($configBase === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
                $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
                $configUrl = $config['database']['server-name'] . $config['database']['url'];
                $configBase = $http . $configUrl;
            } else {
                global $configBase;
            }
        }
        if ($loginMember === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
                $loginMember = $config['login']['member'] ?? 'login_member';
            } else {
                global $loginMember;
            }
        }
        if (!empty($_SESSION[$loginMember]) || !empty($_COOKIE['login_member_id'])) {
            $flag = true;
            $iduser = (!empty($_COOKIE['login_member_id'])) ? $_COOKIE['login_member_id'] : $_SESSION[$loginMember]['id'];
            if ($iduser) {
                $row = $this->d->rawQueryOne("select login_session, id, username, phone, address, email, fullname from #_member where id = ? and find_in_set('hienthi',status)", array($iduser));
                if (!empty($row['id'])) {
                    $login_session = (!empty($_COOKIE['login_member_session'])) ? $_COOKIE['login_member_session'] : $_SESSION[$loginMember]['login_session'];
                    if ($login_session == $row['login_session']) {
                        $_SESSION[$loginMember]['active'] = true;
                        $_SESSION[$loginMember]['id'] = $row['id'];
                        $_SESSION[$loginMember]['username'] = $row['username'];
                        $_SESSION[$loginMember]['phone'] = $row['phone'];
                        $_SESSION[$loginMember]['address'] = $row['address'];
                        $_SESSION[$loginMember]['email'] = $row['email'];
                        $_SESSION[$loginMember]['fullname'] = $row['fullname'];
                    } else $flag = false;
                } else $flag = false;
                if (!$flag) {
                    unset($_SESSION[$loginMember]);
                    setcookie('login_member_id', "", -1, '/');
                    setcookie('login_member_session', "", -1, '/');
                    echo '<script type="text/javascript">alert("Tài khoản của bạn đã hết hạn đăng nhập hoặc đã đăng nhập trên thiết bị khác");window.location.href="' . $configBase . '";</script>';
                    exit();
                }
            }
        }
    }
}

