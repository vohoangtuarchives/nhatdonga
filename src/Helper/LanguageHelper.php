<?php

namespace Tuezy\Helper;

/**
 * LanguageHelper - Language detection and management
 * Handles language detection, validation, and setting
 */
class LanguageHelper
{
    private $func;
    private array $config;
    private $cache;
    private $d;

    public function __construct($func, array $config, $cache, $d)
    {
        $this->func = $func;
        $this->config = $config;
        $this->cache = $cache;
        $this->d = $d;
    }

    /**
     * Detect and set language from request
     * 
     * @param array|null $matchParams Route match parameters
     * @return string Detected language code
     */
    public function detectLanguage(?array $matchParams = null): string
    {
        // Get setting for default language
        $sqlCache = "SELECT * FROM #_setting";
        $setting = $this->cache->get($sqlCache, null, 'fetch', 7200);
        $optsetting = (!empty($setting['options'])) ? json_decode($setting['options'], true) : null;

        // Check if language is in URL params
        if (!empty($matchParams['lang'])) {
            $_SESSION['lang'] = $matchParams['lang'];
        } elseif (empty($_SESSION['lang']) && empty($matchParams['lang'])) {
            $_SESSION['lang'] = $optsetting['lang_default'] ?? 'vi';
        }

        $lang = $_SESSION['lang'];

        // Validate language against configured languages
        $weblang = (!empty($this->config['website']['lang'])) 
            ? array_keys($this->config['website']['lang']) 
            : [];

        if (!in_array($lang, $weblang)) {
            $_SESSION['lang'] = 'vi';
            $lang = $_SESSION['lang'];
        }

        // Set language in Functions
        $this->func->set_language($lang);
        $this->func->set_comlang($this->config['website']['comlang'] ?? []);

        return $lang;
    }

    /**
     * Get slug language field name
     * 
     * @param string $lang Language code
     * @return string Slug language field name (e.g., 'slugvi', 'slugen')
     */
    public function getSlugLang(string $lang): string
    {
        $slugLangMap = [
            'vi' => 'slugvi',
            'en' => 'slugen',
            'zh' => 'slugzh',
        ];

        return $slugLangMap[$lang] ?? 'slugvi';
    }

    /**
     * Get SEO language field name
     * 
     * @param string $lang Language code
     * @return string SEO language field name
     */
    public function getSeoLang(string $lang): string
    {
        // For now, default to 'vi' for SEO
        // Can be extended later if multi-language SEO is needed
        return 'vi';
    }

    /**
     * Load language file
     * 
     * @param string $lang Language code
     */
    public function loadLanguageFile(string $lang): void
    {
        $langFile = LIBRARIES . "lang/$lang.php";
        if (file_exists($langFile)) {
            require_once $langFile;
        }
    }

    /**
     * Get current language
     * 
     * @return string Current language code
     */
    public function getCurrentLanguage(): string
    {
        return $_SESSION['lang'] ?? 'vi';
    }
}

