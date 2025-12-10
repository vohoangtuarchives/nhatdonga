<?php
namespace Tuezy\Service;

class UrlService
{
    public function baseUrl($configBase = null): string
    {
        if ($configBase !== null) return $configBase;
        if (function_exists('Tuezy\\config')) {
            $config = \Tuezy\config();
            $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
            $configUrl = $config['database']['server-name'] . $config['database']['url'];
            return $http . $configUrl;
        }
        global $configBase;
        return $configBase;
    }

    public function checkURL(bool $index = false, ?string $configBase = null)
    {
        $configBase = $this->baseUrl($configBase);
        $urls = array('index', 'index.html', 'trang-chu', 'trang-chu.html');
        $last = '';
        if (array_key_exists('REDIRECT_URL', $_SERVER)) $last = explode('/', $_SERVER['REDIRECT_URL']);
        else $last = explode('/', $_SERVER['REQUEST_URI']);
        if (is_array($last)) {
            $last = $last[count($last) - 1];
            if (strpos($last, '?')) {
                $last = explode('?', $last)[0];
            }
        }
        if ($index) $urls[] = 'index.php'; else $urls = array_diff($urls, ['index.php']);
        if (in_array($last, $urls)) {
            header('location:' . $configBase, true, 301);
            exit();
        }
    }

    public function checkHTTP(string $http, array $arrayDomain, string &$configBase, string $configUrl)
    {
        if (count($arrayDomain) == 0 && $http == 'https://') {
            $configBase = 'http://' . $configUrl;
        }
    }

    public function getPageURL(): string
    {
        $scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }

    public function getCurrentPageURL(): string
    {
        $current = $this->getPageURL();
        $parts = parse_url($current);
        $scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        $path = $parts['path'] ?? '';
        $query = [];
        if (!empty($parts['query'])) parse_str($parts['query'], $query);
        unset($query['p']);
        $qs = !empty($query) ? ('?' . http_build_query($query)) : '';
        return $scheme . '://' . $host . $path . $qs;
    }

    public function getCurrentPageURL_CANO(): string
    {
        $current = $this->getPageURL();
        $parts = parse_url($current);
        $scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        $path = $parts['path'] ?? '';
        $path = str_replace('amp/', '', $path);
        $path = str_replace('index', '', $path);
        return $scheme . '://' . $host . $path;
    }

    public function getRealIPAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }
}

