<?php

namespace Tuezy\Helper;

/**
 * AssetHelper - CSS and JS minification
 * Refactored from class.CssMinify.php and class.JsMinify.php
 * 
 * Handles minification and caching of CSS/JS assets
 */
class AssetHelper
{
    private array $paths = [];
    private array $files = [];
    private bool $debug;
    private $func;
    
    private array $access = [
        'server' => ROOT . 'assets/',
        'asset' => ASSET . 'assets/',
        'folder' => 'caches/'
    ];
    
    private string $cacheName = '';
    private string $cacheFile = '';
    private string $cacheLink = '';
    private int $cacheSize = 0;
    private int $cacheTime = 3600 * 24 * 30; // 30 days
    private string $fileExtension;

    public function __construct(bool $debug, $func, string $fileExtension = 'css')
    {
        $this->debug = $debug;
        $this->func = $func;
        $this->fileExtension = strtolower($fileExtension);
    }

    /**
     * Initialize cache
     * 
     * @param string $name Cache name
     */
    public function init(string $name): void
    {
        $cacheDir = $this->access['server'] . $this->access['folder'];
        
        if (!$this->debug && !file_exists($cacheDir)) {
            if (!mkdir($cacheDir, 0777, true)) {
                die('Failed to create cache folder...');
            }
        }

        $this->cacheName = $name;
        $this->cacheFile = $cacheDir . $this->cacheName . '.' . $this->fileExtension;
        $this->cacheLink = $this->access['asset'] . $this->access['folder'] . $this->cacheName . '.' . $this->fileExtension;
        $this->cacheSize = file_exists($this->cacheFile) ? filesize($this->cacheFile) : 0;
    }

    /**
     * Add file path
     * 
     * @param string $path Relative path from assets folder
     */
    public function set(string $path): void
    {
        $this->paths[] = [
            'server' => $this->access['server'] . $path,
            'asset' => $this->access['asset'] . $path
        ];

        $this->files[] = $path;
    }

    /**
     * Get minified asset HTML
     * 
     * @return string HTML tag
     */
    public function get(): string
    {
        $this->init(md5(implode(',', $this->files)));

        if (empty($this->paths)) {
            die("No files to optimize");
        }

        return $this->debug ? $this->links() : $this->minify();
    }

    /**
     * Minify and cache files
     * 
     * @return string HTML tag
     */
    private function minify(): string
    {
        $content = '';

        if (!$this->cacheSize || $this->isExpired($this->cacheFile)) {
            foreach ($this->paths as $path) {
                $parts = pathinfo($path['server']);
                $extension = strtolower($parts['extension'] ?? '');

                if ($extension !== $this->fileExtension) {
                    die("Invalid file extension. Expected: {$this->fileExtension}");
                }

                if (!file_exists($path['server'])) {
                    continue;
                }

                $fileContent = file_get_contents($path['server']);
                if ($fileContent !== false) {
                    $content .= $this->compress($fileContent);
                }
            }

            if ($content) {
                file_put_contents($this->cacheFile, $content);
            }
        }

        $version = file_exists($this->cacheFile) ? filemtime($this->cacheFile) : time();
        
        if ($this->fileExtension === 'css') {
            return '<link href="' . $this->cacheLink . '?v=' . $version . '" rel="stylesheet">';
        } else {
            return '<script type="text/javascript" src="' . $this->cacheLink . '?v=' . $version . '"></script>';
        }
    }

    /**
     * Get individual file links (debug mode)
     * 
     * @return string HTML tags
     */
    private function links(): string
    {
        $links = '';

        // Clear cache file in debug mode
        if ($this->cacheSize && file_exists($this->cacheFile)) {
            file_put_contents($this->cacheFile, '');
        }

        foreach ($this->paths as $path) {
            $parts = pathinfo($path['server']);
            $extension = strtolower($parts['extension'] ?? '');

            if ($extension !== $this->fileExtension) {
                die("Invalid file extension. Expected: {$this->fileExtension}");
            }

            $version = $this->func->stringRandom(10);
            
            if ($this->fileExtension === 'css') {
                $links .= '<link href="' . $path['asset'] . '?v=' . $version . '" rel="stylesheet">' . PHP_EOL;
            } else {
                $links .= '<script type="text/javascript" src="' . $path['asset'] . '?v=' . $version . '"></script>' . PHP_EOL;
            }
        }

        return $links;
    }

    /**
     * Compress CSS
     * 
     * @param string $buffer CSS content
     * @return string Compressed CSS
     */
    private function compressCss(string $buffer): string
    {
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        
        // Remove spaces after colons
        $buffer = str_replace(': ', ':', $buffer);
        
        // Remove whitespace
        $buffer = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $buffer);
        
        return $buffer;
    }

    /**
     * Compress JavaScript
     * 
     * @param string $js JavaScript content
     * @return string Compressed JavaScript
     */
    private function compressJs(string $js): string
    {
        // Remove single-line comments
        $js = preg_replace('/^[\t ]*?\/\/.*\s?/m', '', $js);
        
        // Remove end-of-line comments
        $js = preg_replace('/([\s;})]+)\/\/.*/m', '\\1', $js);
        
        // Remove multi-line comments (careful with regex)
        $js = preg_replace('/(\s+)\/\*([^\/]*)\*\/(\s+)/s', "\n", $js);
        
        // Remove leading whitespace
        $js = preg_replace('/^\s*/m', '', $js);
        
        // Replace tabs with spaces
        $js = preg_replace('/\t+/m', ' ', $js);
        
        // Remove newlines
        $js = preg_replace('/[\r\n]+/', '', $js);
        
        // Handle strings (don't minify inside quotes)
        $lock = ['status' => false, 'char' => ''];
        $jsSubstrings = preg_split('/([\'"])/', $js, -1, PREG_SPLIT_DELIM_CAPTURE);
        $js = '';

        foreach ($jsSubstrings as $substring) {
            if ($substring === '\'' || $substring === '"') {
                if ($lock['status'] === false) {
                    $lock['status'] = true;
                    $lock['char'] = $substring;
                } elseif ($substring === $lock['char']) {
                    $lock['status'] = false;
                    $lock['char'] = '';
                }
                $js .= $substring;
                continue;
            }

            if ($lock['status'] === false) {
                // Remove unnecessary semicolons
                $substring = str_replace(';}', '}', $substring);
                
                // Remove spaces around operators
                $substring = preg_replace('/ *([<>=+\-!\|{(},;&:?]+) */', '\\1', $substring);
            }

            $js .= $substring;
        }

        return trim($js);
    }

    /**
     * Compress content based on file type
     * 
     * @param string $content File content
     * @return string Compressed content
     */
    private function compress(string $content): string
    {
        if ($this->fileExtension === 'css') {
            return $this->compressCss($content);
        } else {
            return $this->compressJs($content);
        }
    }

    /**
     * Check if cache file is expired
     * 
     * @param string $file Cache file path
     * @return bool True if expired
     */
    private function isExpired(string $file): bool
    {
        if (!file_exists($file)) {
            return true;
        }

        $fileTime = filemtime($file);
        return (time() - $fileTime) > $this->cacheTime;
    }
}

