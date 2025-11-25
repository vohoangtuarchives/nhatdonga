<?php

namespace Tuezy;

/**
 * ErrorHandler - Centralized error handling and logging
 */
class ErrorHandler
{
    private $func;
    private string $logPath;
    private bool $logErrors;
    private bool $displayErrors;

    public function __construct($func, string $logPath = '', bool $logErrors = true, bool $displayErrors = false)
    {
        $this->func = $func;
        $this->logPath = $logPath ?: (defined('LOGS') ? LOGS : 'logs/');
        $this->logErrors = $logErrors;
        $this->displayErrors = $displayErrors;
    }

    /**
     * Handle error
     * 
     * @param \Throwable $exception Exception
     * @param int $statusCode HTTP status code
     * @param bool $log Log error
     */
    public function handle(\Throwable $exception, int $statusCode = 500, bool $log = true): void
    {
        if ($log && $this->logErrors) {
            $this->logError($exception);
        }

        $this->sendErrorResponse($exception, $statusCode);
    }

    /**
     * Log error
     * 
     * @param \Throwable $exception Exception
     */
    private function logError(\Throwable $exception): void
    {
        $logFile = $this->logPath . date('Y-m-d') . '.txt';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        @file_put_contents($logFile, $message, FILE_APPEND);
    }

    /**
     * Send error response
     * 
     * @param \Throwable $exception Exception
     * @param int $statusCode HTTP status code
     */
    private function sendErrorResponse(\Throwable $exception, int $statusCode): void
    {
        http_response_code($statusCode);

        // If API request, send JSON
        if ($this->isApiRequest()) {
            $this->sendJsonError($exception, $statusCode);
            return;
        }

        // Otherwise, show error page
        $this->showErrorPage($statusCode, $exception);
    }

    /**
     * Check if current request is API request
     * 
     * @return bool
     */
    private function isApiRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false
            || strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/api/') !== false
            || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    /**
     * Send JSON error response
     * 
     * @param \Throwable $exception Exception
     * @param int $statusCode HTTP status code
     */
    private function sendJsonError(\Throwable $exception, int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => false,
            'error' => $this->displayErrors ? $exception->getMessage() : 'An error occurred',
            'code' => $statusCode,
        ];

        if ($this->displayErrors) {
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Show error page
     * 
     * @param int $statusCode HTTP status code
     * @param \Throwable|null $exception Exception
     */
    private function showErrorPage(int $statusCode, ?\Throwable $exception = null): void
    {
        $errorPage = "{$statusCode}.php";
        
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            $this->showDefaultErrorPage($statusCode, $exception);
        }
        
        exit;
    }

    /**
     * Show default error page
     * 
     * @param int $statusCode HTTP status code
     * @param \Throwable|null $exception Exception
     */
    private function showDefaultErrorPage(int $statusCode, ?\Throwable $exception = null): void
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];

        $message = $messages[$statusCode] ?? 'Error';
        
        echo "<!DOCTYPE html>
<html>
<head>
    <title>{$statusCode} - {$message}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { font-size: 72px; margin: 0; }
        p { font-size: 18px; }
    </style>
</head>
<body>
    <h1>{$statusCode}</h1>
    <p>{$message}</p>";
        
        if ($this->displayErrors && $exception) {
            echo "<pre>" . htmlspecialchars($exception->getMessage()) . "</pre>";
        }
        
        echo "</body>
</html>";
    }

    /**
     * Register error handlers
     */
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handle']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Handle PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile Error file
     * @param int $errline Error line
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $exception = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        $this->handle($exception, 500);
        
        return true;
    }

    /**
     * Handle shutdown errors
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            $this->handle($exception, 500);
        }
    }

    /**
     * Send standardized error response
     * 
     * @param string $message Error message
     * @param array $errors Additional errors
     * @param int $statusCode HTTP status code
     */
    public function error(string $message, array $errors = [], int $statusCode = 400): void
    {
        if ($this->isApiRequest()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            
            $response = [
                'success' => false,
                'message' => $message,
            ];
            
            if (!empty($errors)) {
                $response['errors'] = $errors;
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $this->func->transfer($message, null, false);
    }
}

