<?php

namespace Tuezy\Controller;

use Tuezy\FormHandler;
use Tuezy\ValidationHelper;
use Tuezy\Repository\StaticRepository;

/**
 * ContactController - Handles contact page requests
 */
class ContactController extends BaseController
{
    private FormHandler $formHandler;
    private ValidationHelper $validator;
    private StaticRepository $staticRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        $emailer,
        $flash
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';
        $configBase = $config['database']['url'] ?? '';

        $this->validator = new ValidationHelper($func, $config);
        $this->formHandler = new FormHandler($db, $func, $emailer, $flash, $this->validator, $configBase, $lang, $GLOBALS['setting'] ?? null);
        $this->staticRepo = new StaticRepository($db, $cache, $lang, $sluglang);
    }

    /**
     * Display contact page
     * 
     * @return array View data
     */
    public function index(): array
    {
        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';

        // Handle form submission
        if (!empty($_POST['submit-contact'])) {
            $dataContact = $_POST['dataContact'] ?? [];
            $recaptchaResponse = $_POST['recaptcha_response_contact'] ?? '';
            
            $this->formHandler->handleContact($dataContact, $recaptchaResponse);
            // FormHandler handles redirect, so this won't be reached if successful
        }

        // SEO
        $seoDB = $this->seo->getOnDB(0, 'contact', 'update', '');
        $this->seo->set('h1', 'lienhe');

        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
        } else {
            $this->seo->set('title', 'lienhe');
        }

        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }

        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }

        $this->seo->set('url', $this->func->getPageURL());

        // Breadcrumbs
        $this->breadcrumbHelper->add('lienhe', '/lien-he');

        return [
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }
}

