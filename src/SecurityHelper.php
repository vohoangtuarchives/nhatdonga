<?php

namespace Tuezy;

/**
 * SecurityHelper - Security operations (sanitization, validation, CSRF)
 * Centralizes security-related functions
 */
class SecurityHelper
{
    /**
     * Sanitize string input
     * 
     * @param string $input Input string
     * @param int $flags htmlspecialchars flags
     * @return string Sanitized string
     */
    public static function sanitize(string $input, int $flags = ENT_QUOTES): string
    {
        return htmlspecialchars($input, $flags, 'UTF-8');
    }

    /**
     * Sanitize array input
     * 
     * @param array $input Input array
     * @return array Sanitized array
     */
    public static function sanitizeArray(array $input): array
    {
        return array_map(function($value) {
            return is_array($value) ? self::sanitizeArray($value) : self::sanitize($value);
        }, $input);
    }

    /**
     * Sanitize request parameter
     * 
     * @param string $key Parameter key
     * @param string $default Default value
     * @return string Sanitized value
     */
    public static function sanitizeRequest(string $key, string $default = ''): string
    {
        $value = $_REQUEST[$key] ?? $default;
        return is_string($value) ? self::sanitize($value) : $default;
    }

    /**
     * Sanitize POST parameter
     * 
     * @param string $key Parameter key
     * @param string $default Default value
     * @return string Sanitized value
     */
    public static function sanitizePost(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? self::sanitize($value) : $default;
    }

    /**
     * Sanitize GET parameter
     * 
     * @param string $key Parameter key
     * @param string $default Default value
     * @return string Sanitized value
     */
    public static function sanitizeGet(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? self::sanitize($value) : $default;
    }

    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if valid
     */
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validate email
     * 
     * @param string $email Email address
     * @return bool True if valid
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     * 
     * @param string $url URL
     * @return bool True if valid
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate IP address
     * 
     * @param string $ip IP address
     * @return bool True if valid
     */
    public static function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Escape SQL (use prepared statements instead, but this is for legacy code)
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeSql(string $string): string
    {
        // Note: This is a basic escape, prefer prepared statements
        return addslashes($string);
    }

    /**
     * Clean XSS from string
     * 
     * @param string $input Input string
     * @return string Cleaned string
     */
    public static function cleanXss(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove script tags and event handlers
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $input);
        $input = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $input);
        
        // HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    /**
     * Generate secure random string
     * 
     * @param int $length String length
     * @return string Random string
     */
    public static function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password
     * 
     * @param string $password Password
     * @return string Hashed password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     * 
     * @param string $password Plain password
     * @param string $hash Hashed password
     * @return bool True if match
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check for SQL injection attempts in query string
     * Refactored from class.AntiSQLInjection.php
     * 
     * @return bool True if safe, false if injection detected
     */
    public static function checkSqlInjection(): bool
    {
        if (!isset($_SERVER['QUERY_STRING'])) {
            return true;
        }

        $queryString = strtolower($_SERVER['QUERY_STRING']);
        
        // List of dangerous SQL injection keywords
        $dangerousKeywords = [
            'union', 'chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(',
            'cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20',
            'union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=',
            'esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(',
            'mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm',
            'mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(',
            'chmod(', 'chmod%20', '%20chmod', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(',
            'locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall',
            'passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20',
            'insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', '%20like', 'like%20',
            '$_request', '$_get', '$request', '$get', '.system', '&aim', '%20getenv', 'getenv%20',
            'new_password', '&icq', '/etc/password', '/etc/shadow', '/etc/groups', '/etc/gshadow',
            '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id', '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g++', 'bin/python',
            'bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20', '.pl', '/usr/X11R6/bin/xterm', 'lsof%20',
            '/bin/mail', '.conf', 'motd%20', '_config.php', 'cgi-', '.eml',
            'file\://', 'window.open', '<script>', 'javascript\://', 'img src', 'img%20src', '.jsp', 'ftp.exe',
            'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd',
            'servlet', '/etc/passwd', 'alert', '~root', '~ftp', '.js', '.jsp', '.history',
            'bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20',
            'powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con',
            '<script', '/robot.txt', '/perl', 'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from',
            'select from', 'drop%20', '.system', 'getenv', '_php', 'php_', 'phpinfo()', '<?php', '?>', 'sql='
        ];

        $sanitized = str_replace($dangerousKeywords, '*', $queryString);
        
        // If query string changed, it means dangerous keywords were found
        if ($queryString !== $sanitized) {
            return false;
        }

        return true;
    }

    /**
     * Block request if SQL injection detected
     * 
     * @return void
     */
    public static function blockSqlInjection(): void
    {
        if (!self::checkSqlInjection()) {
            header("HTTP/1.0 404 Not Found");
            die("404 Not found");
        }
    }
}

