<?php

namespace Tuezy\Helper;

/**
 * AddonsHelper - Online addons management
 * Refactored from class.AddonsOnline.php
 * 
 * Handles dynamic loading of addons via AJAX
 */
class AddonsHelper
{
    private array $scripts = [];

    /**
     * Add script for loading addon
     * 
     * @param string $element Element ID to load content into
     * @param string $type Addon type
     * @param float $timeout Timeout in seconds (default 3.5)
     */
    public function script(string $element, string $type, float $timeout = 3.5): void
    {
        if (empty($element) || empty($type)) {
            return;
        }

        $timeoutMs = (int)($timeout * 1000);
        $script = '<script type="text/javascript">'
            . '$(function(){'
            . 'setTimeout(function(){'
            . '$("#' . htmlspecialchars($element) . '").load("api/addons.php?type=' . htmlspecialchars($type) . '")'
            . '}, ' . $timeoutMs . ');'
            . '});'
            . '</script>';

        $this->scripts[] = $script;
    }

    /**
     * Set addon element and script
     * 
     * @param string $element Element ID
     * @param string $type Addon type
     * @param float $timeout Timeout in seconds
     * @return string HTML element
     */
    public function set(string $element, string $type, float $timeout = 3.5): string
    {
        if (empty($element) || empty($type)) {
            return '';
        }

        $elementHtml = '<div id="' . htmlspecialchars($element) . '"></div>';
        $this->script($element, $type, $timeout);

        return $elementHtml;
    }

    /**
     * Get all scripts
     * 
     * @return string All scripts HTML
     */
    public function get(): string
    {
        return implode('', $this->scripts);
    }

    /**
     * Clear all scripts
     */
    public function clear(): void
    {
        $this->scripts = [];
    }
}

