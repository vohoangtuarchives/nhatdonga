<?php

namespace Tuezy;

/**
 * Context - Centralized dependency management
 * Replaces global variables with a context object that can be passed around
 * 
 * This class provides a cleaner way to access services without using global variables
 */
class Context
{
    private Application $app;
    private static ?Context $instance = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get singleton instance
     * 
     * @return Context
     */
    public static function getInstance(): ?Context
    {
        return self::$instance;
    }

    /**
     * Set singleton instance
     * 
     * @param Context $context
     */
    public static function setInstance(Context $context): void
    {
        self::$instance = $context;
    }

    /**
     * Get a service from the application container
     * 
     * @param string $name Service name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->app->get($name);
    }

    /**
     * Get database instance
     * 
     * @return \PDODb
     */
    public function db(): \PDODb
    {
        return $this->get('db');
    }

    /**
     * Get cache instance
     * 
     * @return \Cache
     */
    public function cache(): \Cache
    {
        return $this->get('cache');
    }

    /**
     * Get functions instance
     * 
     * @return \Functions
     */
    public function func(): \Functions
    {
        return $this->get('func');
    }

    /**
     * Get SEO instance
     * 
     * @return \Seo
     */
    public function seo(): \Seo
    {
        return $this->get('seo');
    }

    /**
     * Get emailer instance
     * 
     * @return \Email
     */
    public function emailer(): \Email
    {
        return $this->get('emailer');
    }

    /**
     * Get router instance
     * 
     * @return \AltoRouter
     */
    public function router(): \AltoRouter
    {
        return $this->get('router');
    }

    /**
     * Get flash instance
     * 
     * @return mixed
     */
    public function flash()
    {
        return $this->get('flash');
    }

    /**
     * Get custom instance
     * 
     * @return \Custom
     */
    public function custom(): \Custom
    {
        return $this->get('custom');
    }

    /**
     * Get breadcrumb instance
     * 
     * @return \BreadCrumbs
     */
    public function breadcr(): \BreadCrumbs
    {
        return $this->get('breadcr');
    }

    /**
     * Get statistic instance
     * 
     * @return \Statistic
     */
    public function statistic(): \Statistic
    {
        return $this->get('statistic');
    }

    /**
     * Get cart instance
     * 
     * @return \Cart
     */
    public function cart(): \Cart
    {
        return $this->get('cart');
    }

    /**
     * Get detect instance
     * 
     * @return \MobileDetect
     */
    public function detect(): \MobileDetect
    {
        return $this->get('detect');
    }

    /**
     * Get addons instance
     * 
     * @return \AddonsOnline
     */
    public function addons(): \AddonsOnline
    {
        return $this->get('addons');
    }

    /**
     * Get CSS minifier instance
     * 
     * @return \CssMinify
     */
    public function css(): \CssMinify
    {
        return $this->get('css');
    }

    /**
     * Get JS minifier instance
     * 
     * @return \JsMinify
     */
    public function js(): \JsMinify
    {
        return $this->get('js');
    }

    /**
     * Get injection instance
     * 
     * @return \AntiSQLInjection
     */
    public function injection(): \AntiSQLInjection
    {
        return $this->get('injection');
    }

    /**
     * Get config
     * 
     * @return Config
     */
    public function config(): Config
    {
        return $this->app->getConfig();
    }

    /**
     * Get config array (for backward compatibility)
     * 
     * @return array
     */
    public function configArray(): array
    {
        return $this->app->getConfig()->all();
    }

    /**
     * Get application instance
     * 
     * @return Application
     */
    public function app(): Application
    {
        return $this->app;
    }
}

