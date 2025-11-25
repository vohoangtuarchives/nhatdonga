<?php

/**
 * Enhanced AutoLoad with namespace support
 * Supports both old classes and new Tuezy namespace classes
 */
class AutoLoadRefactored
{
    public function __construct()
    {
        spl_autoload_register(array($this, '_autoload'));
    }

    private function _autoload($class)
    {
        // Handle Tuezy namespace
        if (strpos($class, 'Tuezy\\') === 0) {
            // Remove 'Tuezy\' prefix
            $classPath = str_replace('Tuezy\\', '', $class);
            // Convert namespace separators to directory separators
            $file = __DIR__ . '/../src/' . str_replace('\\', '/', $classPath) . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }

        // Handle old classes (backward compatibility)
        $file = LIBRARIES . "class/class." . str_replace("\\", "/", trim($class, '\\')) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

