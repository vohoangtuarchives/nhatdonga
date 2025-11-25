<?php

namespace Tuezy;

/**
 * Application - Bootstrap class to initialize application
 * Refactors index.php to reduce global variables and improve structure
 */
class Application
{
    private ServiceContainer $container;
    private Config $config;
    private array $services = [];

    public function __construct(array $config)
    {
        $this->config = new Config($config);
        $this->container = new ServiceContainer();
        $this->bootstrap();
    }

    /**
     * Bootstrap application - initialize all services
     */
    private function bootstrap(): void
    {
        // Register database
        $this->container->register('db', function() {
            return new \PDODb($this->config->get('database'));
        }, true);

        // Register cache
        $this->container->register('cache', function() {
            return new \Cache($this->container->get('db'));
        }, true);

        // Register functions
        $this->container->register('func', function() {
            return new \Functions($this->container->get('db'), $this->container->get('cache'));
        }, true);

        // Register SEO
        $this->container->register('seo', function() {
            return new \Seo($this->container->get('db'));
        }, true);

        // Register emailer
        $this->container->register('emailer', function() {
            return new \Email($this->container->get('db'));
        }, true);

        // Register router
        $this->container->register('router', function() {
            return new \AltoRouter();
        }, true);

        // Register other services
        $this->container->register('flash', function() {
            return new \Flash();
        }, true);

        $this->container->register('custom', function() {
            return new \Custom($this->container->get('db'));
        }, true);

        $this->container->register('breadcr', function() {
            return new \BreadCrumbs($this->container->get('db'));
        }, true);

        $this->container->register('statistic', function() {
            return new \Statistic($this->container->get('db'), $this->container->get('cache'));
        }, true);

        $this->container->register('cart', function() {
            return new \Cart($this->container->get('db'));
        }, true);

        $this->container->register('detect', function() {
            return new \MobileDetect();
        }, true);

        $this->container->register('addons', function() {
            return new \AddonsOnline();
        }, true);

        $this->container->register('css', function() {
            return new \CssMinify(
                $this->config->get('website.debug-css'),
                $this->container->get('func')
            );
        }, true);

        $this->container->register('js', function() {
            return new \JsMinify(
                $this->config->get('website.debug-js'),
                $this->container->get('func')
            );
        }, true);

        $this->container->register('injection', function() {
            return new \AntiSQLInjection();
        }, true);
    }

    /**
     * Get service from container
     * 
     * @param string $name Service name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * Get config
     * 
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Make services available as global variables (for backward compatibility)
     * 
     * @return array Array of global variables
     */
    public function getGlobals(): array
    {
        return [
            'd' => $this->get('db'),
            'cache' => $this->get('cache'),
            'func' => $this->get('func'),
            'seo' => $this->get('seo'),
            'emailer' => $this->get('emailer'),
            'router' => $this->get('router'),
            'flash' => $this->get('flash'),
            'custom' => $this->get('custom'),
            'breadcr' => $this->get('breadcr'),
            'statistic' => $this->get('statistic'),
            'cart' => $this->get('cart'),
            'detect' => $this->get('detect'),
            'addons' => $this->get('addons'),
            'css' => $this->get('css'),
            'js' => $this->get('js'),
            'injection' => $this->get('injection'),
            'config' => $this->config->all(), // For backward compatibility
        ];
    }
}

