<?php

namespace Tuezy;

/**
 * Examples - Usage examples for refactored classes
 * This file is for reference only, not meant to be executed
 */
class Examples
{
    /**
     * Example 1: Using Application Bootstrap
     */
    public function exampleApplication()
    {
        // In index.php
        require_once LIBRARIES."config.php";
        require_once LIBRARIES.'autoload.php';
        
        use Tuezy\Application;
        
        $app = new Application($config);
        $globals = $app->getGlobals();
        
        // Extract to create global variables (backward compatible)
        extract($globals);
        
        // Or use container directly
        $d = $app->get('db');
        $cache = $app->get('cache');
    }

    /**
     * Example 2: Using DataProvider
     */
    public function exampleDataProvider()
    {
        // In sources/allpage.php
        use Tuezy\DataProvider;
        
        $dataProvider = new DataProvider($cache, $lang, $seolang);
        
        // Instead of:
        // $slider = $cache->get("select name$lang, photo, link from #_photo...", ['slide'], 'result', 7200);
        
        // Use:
        $slider = $dataProvider->getPhotos('slide');
        $logo = $dataProvider->getLogo();
        $favicon = $dataProvider->getPhoto('favicon', 'photo_static');
        $featuredProducts = $dataProvider->getFeaturedProducts('san-pham', 8);
        $featuredNews = $dataProvider->getFeaturedNews('tin-tuc');
    }

    /**
     * Example 3: Using FormHandler
     */
    public function exampleFormHandler()
    {
        // In sources/contact.php
        use Tuezy\FormHandler;
        use Tuezy\ValidationHelper;
        
        $validator = new ValidationHelper($func);
        $formHandler = new FormHandler($d, $func, $emailer, $flash, $validator, $configBase, $lang, $setting);
        
        if (isset($_POST['submit-contact'])) {
            $dataContact = $_POST['dataContact'] ?? [];
            $recaptchaResponse = $_POST['recaptcha_response_contact'] ?? '';
            
            $formHandler->handleContact($dataContact, $recaptchaResponse);
            // That's it! All validation, database insert, file upload, and email sending handled
        }
        
        // In sources/allpage.php for newsletter
        if (isset($_POST['submit-newsletter'])) {
            $dataNewsletter = $_POST['dataNewsletter'] ?? [];
            $recaptchaResponse = $_POST['recaptcha_response_newsletter'] ?? '';
            
            $formHandler->handleNewsletter($dataNewsletter, $recaptchaResponse);
        }
    }

    /**
     * Example 4: Using ValidationHelper
     */
    public function exampleValidation()
    {
        use Tuezy\ValidationHelper;
        
        $validator = new ValidationHelper($func);
        
        // Individual validations
        $validator->required($data['email'], 'Email');
        $validator->email($data['email']);
        $validator->phone($data['phone']);
        
        if ($validator->isValid()) {
            // Process
        } else {
            $errors = $validator->getErrors();
        }
        
        // Or use predefined validators
        if ($validator->validateContact($data)) {
            // Valid
        }
    }

    /**
     * Example 5: Using RequestHandler
     */
    public function exampleRequestHandler()
    {
        use Tuezy\RequestHandler;
        
        $params = RequestHandler::getParams();
        $com = $params['com'];
        $id = $params['id'];
        $curPage = $params['curPage'];
    }

    /**
     * Example 6: Using Config
     */
    public function exampleConfig()
    {
        use Tuezy\Config;
        
        $configObj = new Config($config);
        
        // Instead of: $config['website']['debug-css']
        $debugCss = $configObj->get('website.debug-css');
        
        // With default value
        $timeout = $configObj->get('api.timeout', 30);
        
        // Check if exists
        if ($configObj->has('feature.newFeature')) {
            // Use feature
        }
    }

    /**
     * Example 7: Using RouteHandler
     */
    public function exampleRouteHandler()
    {
        use Tuezy\RouteHandler;
        
        $routeHandler = new RouteHandler();
        
        $routeConfig = $routeHandler->getRouteConfig('tin-tuc', [
            'hasId' => !empty($_GET['id']),
        ]);
        
        if ($routeConfig) {
            $source = $routeConfig['source'];
            $template = $routeConfig['template'];
            $seo->set('type', $routeConfig['seoType']);
        }
    }
}

