<?php

namespace Tuezy;

/**
 * ImageHelper - Image processing and manipulation helper
 * Provides common image operations
 */
class ImageHelper
{
    private $func;
    private string $configBase;

    public function __construct($func, string $configBase)
    {
        $this->func = $func;
        $this->configBase = $configBase;
    }

    /**
     * Get image HTML with thumb generation
     * 
     * @param array $options Image options
     * @return string HTML image tag
     */
    public function getImage(array $options): string
    {
        $defaults = [
            'sizes' => '85x85x2',
            'upload' => UPLOAD_PHOTO_L,
            'image' => '',
            'alt' => '',
            'class' => '',
            'lazy' => false,
        ];

        $options = array_merge($defaults, $options);

        return $this->func->getImage($options);
    }

    /**
     * Get thumbnail URL
     * 
     * @param string $image Image filename
     * @param string $sizes Thumb sizes (e.g., '300x300x2')
     * @param string $uploadPath Upload path
     * @return string Thumbnail URL
     */
    public function getThumbUrl(string $image, string $sizes = '300x300x2', string $uploadPath = UPLOAD_PHOTO_L): string
    {
        if (empty($image)) {
            return '';
        }

        return $this->configBase . THUMBS . '/' . $sizes . '/' . $uploadPath . $image;
    }

    /**
     * Get image with watermark URL
     * 
     * @param string $image Image filename
     * @param string $sizes Thumb sizes
     * @param string $type Type (product, news)
     * @return string Watermarked image URL
     */
    public function getWatermarkUrl(string $image, string $sizes = '300x300x2', string $type = 'product'): string
    {
        if (empty($image)) {
            return '';
        }

        return $this->configBase . WATERMARK . '/' . $type . '/' . $sizes . '/' . $image;
    }

    /**
     * Create thumbnail
     * 
     * @param int $width Width
     * @param int $height Height
     * @param int $zoom Zoom level
     * @param string $src Source image path
     * @param array|null $watermark Watermark data
     * @param string $type Type (product, news, etc.)
     */
    public function createThumb(int $width, int $height, int $zoom, string $src, ?array $watermark = null, string $type = 'product'): void
    {
        $thumbPath = ($watermark) ? WATERMARK . '/' . $type : THUMBS;
        $this->func->createThumb($width, $height, $zoom, $src, $watermark, $thumbPath);
    }

    /**
     * Get image dimensions from JSON options
     * 
     * @param array $item Item with options field
     * @param string $defaultImage Default image if options empty
     * @return array|null Image dimensions ['w' => int, 'h' => int, 'm' => string]
     */
    public function getImageDimensions(array $item, string $defaultImage = ''): ?array
    {
        $imgJson = (!empty($item['options'])) 
            ? json_decode($item['options'], true) 
            : null;

        if (empty($imgJson) && !empty($item['photo'])) {
            $imagePath = $defaultImage ?: UPLOAD_PHOTO_L . $item['photo'];
            $imgJson = $this->func->getImgSize($item['photo'], $imagePath);
            
            // Update in database if ID exists
            if (!empty($item['id'])) {
                $this->func->updateSeoDB(json_encode($imgJson), 'photo', $item['id']);
            }
        }

        return $imgJson;
    }

    /**
     * Format image for display
     * 
     * @param string $image Image filename
     * @param string $alt Alt text
     * @param string $sizes Thumb sizes
     * @param string $uploadPath Upload path
     * @param array $attributes Additional HTML attributes
     * @return string HTML image tag
     */
    public function formatImage(string $image, string $alt = '', string $sizes = '300x300x2', string $uploadPath = UPLOAD_PHOTO_L, array $attributes = []): string
    {
        if (empty($image)) {
            return '';
        }

        $class = $attributes['class'] ?? '';
        $lazy = $attributes['lazy'] ?? false;

        return $this->getImage([
            'sizes' => $sizes,
            'upload' => $uploadPath,
            'image' => $image,
            'alt' => $alt,
            'class' => $class,
            'lazy' => $lazy,
        ]);
    }
}

