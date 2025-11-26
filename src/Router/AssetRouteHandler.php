<?php

namespace Tuezy\Router;

use Tuezy\Repository\PhotoRepository;

/**
 * AssetRouteHandler - Handles asset routes (thumbnails, watermarks)
 * Extracted from libraries/router.php for better organization
 */
class AssetRouteHandler
{
    private $func;
    private $cache;
    private $d;
    private array $config;

    public function __construct($func, $cache, $d, array $config)
    {
        $this->func = $func;
        $this->cache = $cache;
        $this->d = $d;
        $this->config = $config;
    }

    /**
     * Handle thumbnail generation route
     * 
     * @param int $w Width
     * @param int $h Height
     * @param int $z Zoom/quality
     * @param string $src Source image path
     */
    public function handleThumbnail(int $w, int $h, int $z, string $src): void
    {
        // Convert URL path to file system path
        $src = str_replace('%20', ' ', $src);
        $src = ltrim($src, '/');
        $filePath = ROOT . $src;
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Try alternative paths if file doesn't exist
        if (!file_exists($filePath)) {
            $filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
            $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        }

        if (!file_exists($filePath) && !empty($this->config['database']['url'])) {
            $srcClean = str_replace($this->config['database']['url'], '', $src);
            $srcClean = ltrim($srcClean, '/');
            $filePath = ROOT . $srcClean;
            $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        }

        $this->func->createThumb($w, $h, $z, $filePath, null, THUMBS);
    }

    /**
     * Handle watermark route for products
     * 
     * @param int $w Width
     * @param int $h Height
     * @param int $z Zoom/quality
     * @param string $src Source image path
     * @param string|null $lang Language code
     * @param string|null $sluglang Slug language field
     */
    public function handleProductWatermark(int $w, int $h, int $z, string $src, ?string $lang = null, ?string $sluglang = null): void
    {
        $src = str_replace('%20', ' ', $src);
        $src = ltrim($src, '/');
        $filePath = ROOT . $src;
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        if (!file_exists($filePath)) {
            $filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
            $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        }

        $photoRepo = new PhotoRepository(
            $this->d,
            $lang ?? 'vi',
            $sluglang ?? 'slugvi'
        );
        $wtm = $photoRepo->getByTypeAndAct('watermark', 'photo_static');

        $this->func->createThumb($w, $h, $z, $filePath, $wtm, "product");
    }

    /**
     * Handle watermark route for news
     * 
     * @param int $w Width
     * @param int $h Height
     * @param int $z Zoom/quality
     * @param string $src Source image path
     * @param string|null $lang Language code
     * @param string|null $sluglang Slug language field
     */
    public function handleNewsWatermark(int $w, int $h, int $z, string $src, ?string $lang = null, ?string $sluglang = null): void
    {
        $src = str_replace('%20', ' ', $src);
        $src = ltrim($src, '/');
        $filePath = ROOT . $src;
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        if (!file_exists($filePath)) {
            $filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
            $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        }

        $photoRepo = new PhotoRepository(
            $this->d,
            $lang ?? 'vi',
            $sluglang ?? 'slugvi'
        );
        $wtm = $photoRepo->getByTypeAndAct('watermark-news', 'photo_static');

        $this->func->createThumb($w, $h, $z, $filePath, $wtm, "news");
    }
}

