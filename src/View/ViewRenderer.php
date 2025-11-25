<?php

namespace Tuezy\View;

/**
 * ViewRenderer - Manages template rendering with layout system
 * Standardizes how data is passed from Controller to View
 */
class ViewRenderer
{
    private string $templatePath;
    private string $layoutPath;
    private array $sharedData = [];

    public function __construct(string $templatePath = null, string $layoutPath = null)
    {
        $this->templatePath = $templatePath ?? (defined('TEMPLATE') ? TEMPLATE : './templates/');
        $this->layoutPath = $layoutPath ?? (defined('LAYOUT') ? LAYOUT : 'layout/');
    }

    /**
     * Share data across all views
     * 
     * @param string|array $key Data key or array of data
     * @param mixed $value Data value (if $key is string)
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
     * Render view with layout
     * 
     * @param string $template Template name (without .php extension)
     * @param array $data Data to pass to view
     * @param string|null $layout Layout name (null = no layout)
     */
    public function render(string $template, array $data = [], ?string $layout = null): void
    {
        // Merge shared data with view data
        $data = array_merge($this->sharedData, $data);

        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include template
        $templateFile = $this->templatePath . $template . '.php';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template not found: $templateFile");
        }

        include $templateFile;
        $content = ob_get_clean();

        // If layout is specified, wrap content in layout
        if ($layout !== null) {
            $layoutFile = $this->templatePath . $this->layoutPath . $layout . '.php';
            if (file_exists($layoutFile)) {
                // Extract data again for layout
                extract($data);
                // $content variable is available in layout
                include $layoutFile;
            } else {
                // Layout not found, just output content
                echo $content;
            }
        } else {
            // No layout, just output content
            echo $content;
        }
    }

    /**
     * Render view without layout
     * 
     * @param string $template Template name
     * @param array $data Data to pass to view
     */
    public function renderPartial(string $template, array $data = []): void
    {
        $this->render($template, $data, null);
    }

    /**
     * Get rendered view as string
     * 
     * @param string $template Template name
     * @param array $data Data to pass to view
     * @param string|null $layout Layout name
     * @return string Rendered HTML
     */
    public function renderToString(string $template, array $data = [], ?string $layout = null): string
    {
        ob_start();
        $this->render($template, $data, $layout);
        return ob_get_clean();
    }

    /**
     * Check if template exists
     * 
     * @param string $template Template name
     * @return bool
     */
    public function exists(string $template): bool
    {
        $templateFile = $this->templatePath . $template . '.php';
        return file_exists($templateFile);
    }

    /**
     * Get template path
     * 
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Set template path
     * 
     * @param string $path Template path
     */
    public function setTemplatePath(string $path): void
    {
        $this->templatePath = rtrim($path, '/') . '/';
    }
}

