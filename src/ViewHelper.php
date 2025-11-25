<?php

namespace Tuezy;

/**
 * ViewHelper - View rendering and template helpers
 * Provides methods for rendering views and templates
 */
class ViewHelper
{
    private string $templatePath;
    private array $sharedData = [];

    public function __construct(string $templatePath = './templates/')
    {
        $this->templatePath = rtrim($templatePath, '/') . '/';
    }

    /**
     * Render view/template
     * 
     * @param string $view View name (e.g., 'product/product_detail')
     * @param array $data Data to pass to view
     * @return string Rendered HTML
     */
    public function render(string $view, array $data = []): string
    {
        // Extract data to variables
        extract(array_merge($this->sharedData, $data));

        // Start output buffering
        ob_start();

        // Include view file
        $viewFile = $this->templatePath . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \RuntimeException("View not found: $view");
        }

        // Get buffered content
        return ob_get_clean();
    }

    /**
     * Include partial/component
     * 
     * @param string $partial Partial name
     * @param array $data Data to pass to partial
     */
    public function partial(string $partial, array $data = []): void
    {
        extract(array_merge($this->sharedData, $data));
        
        $partialFile = $this->templatePath . 'components/' . $partial . '.php';
        if (file_exists($partialFile)) {
            include $partialFile;
        }
    }

    /**
     * Share data with all views
     * 
     * @param string|array $key Data key or array of data
     * @param mixed $value Data value (if key is string)
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedData = array_merge($this->sharedData, $key);
        } else {
            $this->sharedData[$key] = $value;
        }
    }

    /**
     * Check if view exists
     * 
     * @param string $view View name
     * @return bool
     */
    public function exists(string $view): bool
    {
        $viewFile = $this->templatePath . $view . '.php';
        return file_exists($viewFile);
    }

    /**
     * Get view path
     * 
     * @param string $view View name
     * @return string View file path
     */
    public function path(string $view): string
    {
        return $this->templatePath . $view . '.php';
    }

    /**
     * Render JSON response
     * 
     * @param array $data Data to encode
     * @param int $statusCode HTTP status code
     */
    public function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Escape HTML
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function e(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get old input value (from flash)
     * 
     * @param string $key Input key
     * @param mixed $default Default value
     * @return mixed
     */
    public function old(string $key, $default = '')
    {
        // This would typically get from flash/session
        // Implementation depends on your flash system
        return $default;
    }
}

