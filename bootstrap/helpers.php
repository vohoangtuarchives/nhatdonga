<?php

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('loadEnv')) {
    /**
     * Load key=value pairs from a .env file into $_ENV/$_SERVER.
     */
    function loadEnv(string $basePath, string $filename = '.env'): void
    {
        $envPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($envPath) || !is_readable($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $line, 2), 2, null);
            $name = trim($name);
            $value = trim((string)$value);

            if ($name === '') {
                continue;
            }

            $value = trim($value, "'\"");

            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }

            if (!array_key_exists($name, $_SERVER)) {
                $_SERVER[$name] = $value;
            }
        }
    }
}

if (!function_exists('env')) {
    /**
     * Convenience helper to fetch value from $_ENV/$_SERVER.
     */
    function env(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        return $default;
    }
}

