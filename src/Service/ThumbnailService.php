<?php

namespace Tuezy\Service;

/**
 * ThumbnailService - Service for thumbnail generation and processing
 * Extracted from Functions class for better separation of concerns
 */
class ThumbnailService
{
    /**
     * Validate thumbnail input parameters
     * 
     * @param int $width_thumb Width
     * @param int $height_thumb Height
     * @param string $src Source image path
     * @throws void (dies on error)
     */
    public function validateInputs($width_thumb, $height_thumb, $src): void
    {
        if ($width_thumb < 10 && $height_thumb < 10) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die("Width and height larger than 10px");
        }

        if ($width_thumb > 2000 || $height_thumb > 2000) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die("Width and height less than 2000px");
        }

        $src = str_replace("%20", " ", $src);
        if (!file_exists($src)) {
            die("NO IMAGE $src");
        }
    }

    /**
     * Prepare source image (handle webp, normalize path)
     * 
     * @param string $src Source image path
     * @return array ['src' => string, 'webp' => bool]
     */
    public function prepareSource(string $src): array
    {
        $webp = false;
        if (strpos($src, ".webp") !== false) {
            $webp = true;
            $src = str_replace(".webp", "", $src);
        }
        $src = str_replace("%20", " ", $src);
        return ['src' => $src, 'webp' => $webp];
    }

    /**
     * Cleanup old temp files
     * 
     * @param callable $removeFilesCallback Callback to remove files
     * @param callable $removeEmptyFoldersCallback Callback to remove empty folders
     * @param array|null $watermark Watermark data
     * @param string $path Thumb path
     */
    public function cleanupTempFiles(callable $removeFilesCallback, callable $removeEmptyFoldersCallback, $watermark, string $path): void
    {
        $t = 3600 * 24 * 30; // 30 days
        $removeFilesCallback(UPLOAD_TEMP_L, 1);

        if ($watermark != null) {
            $removeFilesCallback(WATERMARK . '/' . $path . "/", $t);
            $removeEmptyFoldersCallback(WATERMARK . '/' . $path . "/");
        } else {
            $removeFilesCallback($path . "/", $t);
            $removeEmptyFoldersCallback($path . "/");
        }
    }

    /**
     * Get image information (dimensions, mime type)
     * 
     * @param string $src Source image path
     * @return array ['width' => int, 'height' => int, 'mime' => string]
     * @throws void (dies on error)
     */
    public function getImageInfo(string $src): array
    {
        $array = getimagesize($src);
        if (!$array) {
            die("NO IMAGE $src");
        }
        return [
            'width' => $array[0],
            'height' => $array[1],
            'mime' => $array['mime']
        ];
    }

    /**
     * Calculate thumbnail dimensions
     * 
     * @param int $width_thumb Desired width
     * @param int $height_thumb Desired height
     * @param int $image_w Original width
     * @param int $image_h Original height
     * @return array ['width' => int, 'height' => int]
     */
    public function calculateDimensions(int $width_thumb, int $height_thumb, int $image_w, int $image_h): array
    {
        $new_width = $width_thumb;
        $new_height = $height_thumb;

        if ($new_height && !$new_width) {
            $new_width = $image_w * ($new_height / $image_h);
        } elseif ($new_width && !$new_height) {
            $new_height = $image_h * ($new_width / $image_w);
        }

        return ['width' => $new_width, 'height' => $new_height];
    }

    /**
     * Create image resource from source file
     * 
     * @param string $src Source image path
     * @param string $mime_type MIME type
     * @return array ['image' => resource, 'func' => string, 'mime' => string]
     * @throws void (dies on error)
     */
    public function createImageResource(string $src, string $mime_type): array
    {
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($src);
                $func = 'imagejpeg';
                $mime = 'jpeg';
                break;

            case 'image/x-ms-bmp':
            case 'image/png':
                $image = imagecreatefrompng($src);
                $func = 'imagepng';
                $mime = 'png';
                break;

            case 'image/gif':
                $image = imagecreatefromgif($src);
                $func = 'imagegif';
                $mime = 'png';
                break;

            default:
                die("UNKNOWN IMAGE TYPE: $src");
        }

        return ['image' => $image, 'func' => $func, 'mime' => $mime];
    }

    /**
     * Calculate center crop dimensions and origin
     * 
     * @param int $new_width Desired width
     * @param int $new_height Desired height
     * @param int $width Original width
     * @param int $height Original height
     * @return array ['width' => int, 'height' => int, 'origin_x' => int, 'origin_y' => int]
     */
    public function calculateCenterCrop(int $new_width, int $new_height, int $width, int $height): array
    {
        $origin_x = 0;
        $origin_y = 0;
        $final_height = $height * ($new_width / $width);

        if ($final_height > $new_height) {
            $origin_x = $new_width / 2;
            $new_width = $width * ($new_height / $height);
            $origin_x = round($origin_x - ($new_width / 2));
        } else {
            $origin_y = $new_height / 2;
            $new_height = $final_height;
            $origin_y = round($origin_y - ($new_height / 2));
        }

        return [
            'width' => $new_width,
            'height' => $new_height,
            'origin_x' => $origin_x,
            'origin_y' => $origin_y
        ];
    }

    /**
     * Calculate crop parameters for imagecopyresampled
     * 
     * @param int|string $zoom_crop Zoom crop mode
     * @param int $width Original width
     * @param int $height Original height
     * @param int $new_width New width
     * @param int $new_height New height
     * @param string $align Alignment
     * @return array ['src_x' => int, 'src_y' => int, 'src_w' => int, 'src_h' => int]
     */
    public function calculateCropParams($zoom_crop, int $width, int $height, int $new_width, int $new_height, string $align): array
    {
        if ($zoom_crop <= 0) {
            return [
                'src_x' => 0,
                'src_y' => 0,
                'src_w' => $width,
                'src_h' => $height
            ];
        }

        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        $cmp_x = $width / $new_width;
        $cmp_y = $height / $new_height;

        if ($cmp_x > $cmp_y) {
            $src_w = round($width / $cmp_x * $cmp_y);
            $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
        } elseif ($cmp_y > $cmp_x) {
            $src_h = round($height / $cmp_y * $cmp_x);
            $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
        }

        // Apply alignment if specified
        if ($align) {
            if (strpos($align, 't') !== false) {
                $src_y = 0;
            }
            if (strpos($align, 'b') !== false) {
                $src_y = $height - $src_h;
            }
            if (strpos($align, 'l') !== false) {
                $src_x = 0;
            }
            if (strpos($align, 'r') !== false) {
                $src_x = $width - $src_w;
            }
        }

        return [
            'src_x' => $src_x,
            'src_y' => $src_y,
            'src_w' => $src_w,
            'src_h' => $src_h
        ];
    }

    /**
     * Calculate folder path for thumbnail
     * 
     * @param string $src Source image path
     * @param array $args Additional arguments (may contain 'folder_old')
     * @return string Folder path (with trailing slash)
     */
    public function calculateFolderPath(string $src, array $args): string
    {
        // Nếu có folder_old trong args (từ router), sử dụng nó
        if (!empty($args['folder_old'])) {
            $folder_old = $args['folder_old'];
            if (substr($folder_old, -1) !== '/') {
                $folder_old .= '/';
            }
            return $folder_old;
        }

        // Tính từ $src (đường dẫn đầy đủ)
        $folder_old = dirname($src) . '/';
        
        // Normalize folder_old - remove absolute path if present, keep only relative path
        $folder_old = str_replace('\\', '/', $folder_old);
        if (strpos($folder_old, $_SERVER['DOCUMENT_ROOT']) === 0) {
            $folder_old = str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder_old);
        }
        // Remove leading slashes and normalize
        $folder_old = ltrim($folder_old, '/\\');
        if (!empty($folder_old) && substr($folder_old, -1) !== '/') {
            $folder_old .= '/';
        }

        return $folder_old;
    }

    /**
     * Build upload directory path for thumbnail
     * 
     * @param int $width_thumb Width
     * @param int $height_thumb Height
     * @param int|string $zoom_crop Zoom crop mode
     * @param string $folder_old Folder path
     * @param array|null $watermark Watermark data
     * @param string $path Base path
     * @return string Upload directory path
     */
    public function buildUploadPath(int $width_thumb, int $height_thumb, $zoom_crop, string $folder_old, $watermark, string $path): string
    {
        $sizeDir = $width_thumb . 'x' . $height_thumb . 'x' . $zoom_crop;

        if (!empty($watermark['status']) && strpos('hienthi', $watermark['status']) !== false) {
            $upload_dir = WATERMARK . '/' . $path . '/' . $sizeDir . '/' . $folder_old;
        } else {
            if ($watermark != null) {
                $upload_dir = WATERMARK . '/' . $path . '/' . $sizeDir . '/' . $folder_old;
            } else {
                $upload_dir = $path . '/' . $sizeDir . '/' . $folder_old;
            }
        }
        
        // Normalize upload_dir path for Windows compatibility
        $upload_dir = str_replace('\\', '/', $upload_dir);
        $upload_dir = str_replace('//', '/', $upload_dir);
        $upload_dir = rtrim($upload_dir, '/');

        return $upload_dir;
    }
}

