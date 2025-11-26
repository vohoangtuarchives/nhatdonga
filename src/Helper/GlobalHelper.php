<?php

namespace Tuezy\Helper;

use Tuezy\Context;

/**
 * GlobalHelper - Helper functions to access services without global variables
 * 
 * This provides a migration path from global variables to Context-based access
 * 
 * Usage:
 *   Instead of: global $d, $func, $cache;
 *   Use: $d = db(); $func = func(); $cache = cache();
 */
class GlobalHelper
{
    /**
     * Get database instance
     * 
     * @return \PDODb
     */
    public static function db(): \PDODb
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->db();
        }
        
        // Fallback to global for backward compatibility
        global $d;
        return $d;
    }

    /**
     * Get cache instance
     * 
     * @return \Cache
     */
    public static function cache(): \Cache
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->cache();
        }
        
        global $cache;
        return $cache;
    }

    /**
     * Get functions instance
     * 
     * @return \Functions
     */
    public static function func(): \Functions
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->func();
        }
        
        global $func;
        return $func;
    }

    /**
     * Get SEO instance
     * 
     * @return \Seo
     */
    public static function seo(): \Seo
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->seo();
        }
        
        global $seo;
        return $seo;
    }

    /**
     * Get emailer instance
     * 
     * @return \Email
     */
    public static function emailer(): \Email
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->emailer();
        }
        
        global $emailer;
        return $emailer;
    }

    /**
     * Get router instance
     * 
     * @return \AltoRouter
     */
    public static function router(): \AltoRouter
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->router();
        }
        
        global $router;
        return $router;
    }

    /**
     * Get flash instance
     * 
     * @return mixed
     */
    public static function flash()
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->flash();
        }
        
        global $flash;
        return $flash;
    }

    /**
     * Get custom instance
     * 
     * @return \Custom
     */
    public static function custom(): \Custom
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->custom();
        }
        
        global $custom;
        return $custom;
    }

    /**
     * Get breadcrumb instance
     * 
     * @return \BreadCrumbs
     */
    public static function breadcr(): \BreadCrumbs
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->breadcr();
        }
        
        global $breadcr;
        return $breadcr;
    }

    /**
     * Get statistic instance
     * 
     * @return \Statistic
     */
    public static function statistic(): \Statistic
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->statistic();
        }
        
        global $statistic;
        return $statistic;
    }

    /**
     * Get cart instance
     * 
     * @return \Cart
     */
    public static function cart(): \Cart
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->cart();
        }
        
        global $cart;
        return $cart;
    }

    /**
     * Get detect instance
     * 
     * @return \MobileDetect
     */
    public static function detect(): \MobileDetect
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->detect();
        }
        
        global $detect;
        return $detect;
    }

    /**
     * Get addons instance
     * 
     * @return \AddonsOnline
     */
    public static function addons(): \AddonsOnline
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->addons();
        }
        
        global $addons;
        return $addons;
    }

    /**
     * Get CSS minifier instance
     * 
     * @return \CssMinify
     */
    public static function css(): \CssMinify
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->css();
        }
        
        global $css;
        return $css;
    }

    /**
     * Get JS minifier instance
     * 
     * @return \JsMinify
     */
    public static function js(): \JsMinify
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->js();
        }
        
        global $js;
        return $js;
    }

    /**
     * Get injection instance
     * 
     * @return \AntiSQLInjection
     */
    public static function injection(): \AntiSQLInjection
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->injection();
        }
        
        global $injection;
        return $injection;
    }

    /**
     * Get config array
     * 
     * @return array
     */
    public static function config(): array
    {
        $context = Context::getInstance();
        if ($context) {
            return $context->configArray();
        }
        
        global $config;
        return $config ?? [];
    }
}

// Note: Helper functions are now accessed via GlobalHelper class methods
// Use: GlobalHelper::db() instead of db()
// This avoids namespace declaration conflicts

