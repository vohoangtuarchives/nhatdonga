<?php

/**
 * AutoLoad - Enhanced version with namespace support
 * Supports both old classes and new Tuezy namespace classes
 * Backward compatible with existing code
 */
class AutoLoad
{
    public function __construct()
    {
        spl_autoload_register(array($this, '_autoload'));
    }

    private function _autoload($class)
    {
        // Handle Tuezy namespace classes
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
        // Support both with and without backslashes
        $class = str_replace("\\", "/", trim($class, '\\'));
        $file = LIBRARIES . "class/class." . $class . '.php';
        
        if (file_exists($file)) {
            require_once $file;
    }
    }
}

/**
 * AutoLoadRefactored - Alias for backward compatibility
 * Use AutoLoad instead
 * @deprecated Use AutoLoad class instead
 */
class AutoLoadRefactored extends AutoLoad
{
    // This class is kept for backward compatibility
    // All functionality is now in AutoLoad class
}

