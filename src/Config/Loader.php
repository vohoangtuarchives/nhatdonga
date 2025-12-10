<?php
namespace Tuezy\Config;

class Loader
{
    public static function get(string $module): array
    {
        $ns = __NAMESPACE__ . '\\' . self::normalize($module);
        if (class_exists($ns) && method_exists($ns, 'get')) {
            return $ns::get();
        }
        $legacy = self::legacyPath($module);
        if (file_exists($legacy)) {
            $data = include $legacy;
            if (is_array($data)) return $data;
            return [];
        }
        return [];
    }

    private static function normalize(string $module): string
    {
        $module = trim($module);
        $module = str_replace(['-', '_'], ' ', strtolower($module));
        $module = ucwords($module);
        return str_replace(' ', '', $module);
    }

    private static function legacyPath(string $module): string
    {
        $module = strtolower($module);
        $paths = [
            \LIBRARIES . "config-{$module}.php",
            \LIBRARIES . "config-{$module}-type.php",
            \LIBRARIES . "config-type-{$module}.php",
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) return $p;
        }
        return \LIBRARIES . "config-{$module}.php";
    }
}

