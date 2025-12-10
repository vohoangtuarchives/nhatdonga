<?php

namespace Tuezy;

/**
 * SEOHelper - Centralized SEO setup logic
 * Refactors repetitive SEO setup code in sources files
 */
class SEOHelper
{
    private $seo;
    private $func;
    private $d;
    private string $lang;
    private string $seolang;
    private string $configBase;

    public function __construct($seo, $func, $d, string $lang, string $seolang, string $configBase)
    {
        $this->seo = $seo;
        $this->func = $func;
        $this->d = $d;
        $this->lang = $lang;
        $this->seolang = $seolang;
        $this->configBase = $configBase;
    }

    /**
     * Setup SEO from database (seopage table)
     * 
     * @param string $type SEO page type
     * @param string|null $titleMain Main title (fallback)
     */
    public function setupFromSeopage(string $type, ?string $titleMain = null): void
    {
        $seopage = $this->d->rawQueryOne(
            "select * from #_seopage where type = ? limit 0,1",
            [$type]
        );

        if (!empty($titleMain)) {
            $this->seo->set('h1', $titleMain);
        }

        if (!empty($seopage['title' . $this->seolang])) {
            $this->seo->set('title', $seopage['title' . $this->seolang]);
        } elseif (!empty($titleMain)) {
            $this->seo->set('title', $titleMain);
        }

        if (!empty($seopage['keywords' . $this->seolang])) {
            $this->seo->set('keywords', $seopage['keywords' . $this->seolang]);
        }

        if (!empty($seopage['description' . $this->seolang])) {
            $this->seo->set('description', $seopage['description' . $this->seolang]);
        }

        $this->seo->set('url', $this->func->getPageURL());

        // Handle SEO image
        if (!empty($seopage['photo'])) {
            $this->setupSeoImage($seopage, 'seopage');
        }
    }

    /**
     * Setup SEO from setting
     * 
     * @param int $id ID (usually 0 for setting)
     */
    public function setupFromSetting(int $id = 0): void
    {
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->d)))->execute($id, 'setting', 'update', 'setting', $this->seolang);

        if ($seoMeta && $seoMeta->title) {
            $this->seo->set('h1', $seoMeta->title);
            $this->seo->set('title', $seoMeta->title);
        }

        if ($seoMeta && $seoMeta->keywords) {
            $this->seo->set('keywords', $seoMeta->keywords);
        }

        if ($seoMeta && $seoMeta->description) {
            $this->seo->set('description', $seoMeta->description);
        }

        $this->seo->set('url', $this->func->getPageURL());
    }

    /**
     * Setup SEO image from screenshot
     * 
     * @param array $screenshot Screenshot data
     */
    public function setupScreenshotImage(array $screenshot): void
    {
        $imgJson = (!empty($screenshot['options'])) 
            ? json_decode($screenshot['options'], true) 
            : null;

        if (is_array($screenshot) && (empty($imgJson) || ($imgJson['p'] != $screenshot['photo']))) {
            $imgJson = $this->func->getImgSize(
                $screenshot['photo'],
                UPLOAD_PHOTO_L . $screenshot['photo']
            );
            $this->seo->updateSeoDB(json_encode($imgJson), 'photo', $screenshot['id']);
        }

        if (!empty($imgJson)) {
            $this->setSeoImageData($imgJson, UPLOAD_PHOTO_L . $screenshot['photo']);
        }
    }

    /**
     * Setup SEO image from seopage
     * 
     * @param array $seopage Seopage data
     * @param string $type Type (seopage)
     */
    private function setupSeoImage(array $seopage, string $type): void
    {
        $imgJson = (!empty($seopage['options'])) 
            ? json_decode($seopage['options'], true) 
            : null;

        if (empty($imgJson) || ($imgJson['p'] != $seopage['photo'])) {
            $uploadPath = ($type === 'seopage') ? UPLOAD_SEOPAGE_L : UPLOAD_PHOTO_L;
            $imgJson = $this->func->getImgSize($seopage['photo'], $uploadPath . $seopage['photo']);
            $this->seo->updateSeoDB(json_encode($imgJson), $type, $seopage['id']);
        }

        if (!empty($imgJson)) {
            $uploadPath = ($type === 'seopage') ? UPLOAD_SEOPAGE_L : UPLOAD_PHOTO_L;
            $this->setSeoImageData($imgJson, $uploadPath . $seopage['photo']);
        }
    }

    /**
     * Set SEO image data
     * 
     * @param array $imgJson Image JSON data
     * @param string $imagePath Image path
     */
    private function setSeoImageData(array $imgJson, string $imagePath): void
    {
        $this->seo->set('photo', $this->configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . $imagePath);
        $this->seo->set('photo:width', $imgJson['w']);
        $this->seo->set('photo:height', $imgJson['h']);
        $this->seo->set('photo:type', $imgJson['m']);
    }

    /**
     * Set SEO type
     * 
     * @param string $type SEO type (object, article, website)
     */
    public function setType(string $type): void
    {
        $this->seo->set('type', $type);
    }
}

