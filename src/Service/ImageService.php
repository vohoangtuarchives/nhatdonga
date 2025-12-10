<?php
namespace Tuezy\Service;

require \LIBRARIES . 'WebpConvert/vendor/autoload.php';

use WebPConvert\WebPConvert;

class ImageService
{
    private ?ThumbnailService $thumbnailService = null;
    private ?UtilityService $utilityService = null;

    public function __construct(private $d, private $cache, ?ThumbnailService $thumbnailService = null)
    {
        $this->thumbnailService = $thumbnailService;
    }

    private function getThumbnailService(): ThumbnailService
    {
        if ($this->thumbnailService === null) {
            $this->thumbnailService = new ThumbnailService();
        }
        return $this->thumbnailService;
    }

    private function getUtilityService(): UtilityService
    {
        if ($this->utilityService === null) {
            $this->utilityService = new UtilityService($this->d, $this->cache);
        }
        return $this->utilityService;
    }

    public function getImage($data = array(), $config = null, $configBase = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        if ($configBase === null) {
            if (function_exists('Tuezy\\config')) {
                $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
                $configUrl = $config['database']['server-name'] . $config['database']['url'];
                $configBase = $http . $configUrl;
            } else {
                global $configBase;
            }
        }
        $defaults = [
            'class' => 'lazy',
            'id' => '',
            'isLazy' => true,
            'thumbs' => \THUMBS,
            'isWatermark' => false,
            'watermark' => (defined('WATERMARK')) ? \WATERMARK : '',
            'prefix' => '',
            'size-error' => '',
            'size-src' => '',
            'sizes' => '',
            'url' => '',
            'upload' => '',
            'image' => '',
            'upload-error' => 'assets/images/',
            'image-error' => 'noimage.png',
            'alt' => ''
        ];
        $info = array_merge($defaults, $data);
        if (empty($info['upload']) || empty($info['image'])) {
            $info['upload'] = $info['upload-error'];
            $info['image'] = $info['image-error'];
        }
        if (!empty($info['sizes'])) {
            $info['size-error'] = $info['size-src'] = $info['sizes'];
        }
        $info['pathOrigin'] = $info['upload'] . $info['image'];
        if (!empty($info['url'])) {
            $info['pathSrc'] = $info['url'];
        } else {
            if (!empty($info['size-src'])) {
                $info['pathSize'] = $info['size-src'] . "/" . $info['upload'] . $info['image'];
                $info['pathSrc'] = (!empty($info['isWatermark']) && !empty($info['prefix'])) ? \ASSET . $info['watermark'] . "/" . $info['prefix'] . "/" . $info['pathSize'] : \ASSET . $info['thumbs'] . "/" . $info['pathSize'];
            } else {
                $info['pathSrc'] = \ASSET . $info['pathOrigin'];
            }
        }
        $baseUrl = !empty($configBase) ? rtrim($configBase, '/') : \ASSET;
        $info['pathError'] = $baseUrl . "/" . ltrim($info['thumbs'] . "/" . $info['size-error'] . "/" . $info['upload-error'] . $info['image-error'], '/');
        $info['class'] = (empty($info['isLazy'])) ? str_replace('lazy', '', $info['class']) : $info['class'];
        $info['class'] = (!empty($info['class'])) ? "class='" . $info['class'] . "'" : "";
        $info['id'] = (!empty($info['id'])) ? "id='" . $info['id'] . "'" : "";
        $info['hasURL'] = false;
        if (filter_var(str_replace(\ASSET, "", $info['pathSrc']), FILTER_VALIDATE_URL)) {
            $info['hasURL'] = true;
        }
        if ($config['website']['image']['hasWebp']) {
            if (!$info['sizes']) {
                if (!$info['hasURL']) {
                    $this->converWebp($info['pathSrc']);
                }
            }
            if (!$info['hasURL']) {
                $info['pathSrc'] .= '.webp';
            }
        }
        $info['src'] = (!empty($info['isLazy']) && strpos($info['class'], 'lazy') !== false) ? "data-src='" . $info['pathSrc'] . "'" : "src='" . $info['pathSrc'] . "'";
        $result = "<img " . $info['class'] . " " . $info['id'] . " onerror=\"this.src='" . $info['pathError'] . "';\" " . $info['src'] . " alt='" . $info['alt'] . "'/>";
        return $result;
    }

    public function converWebp($in)
    {
        global $config;
        $in = $_SERVER['DOCUMENT_ROOT'] . $config['database']['url'] . str_replace(\ASSET, "", $in);
        if (!extension_loaded('imagick')) {
            ob_start();
            WebPConvert::serveConverted($in, $in . ".webp", [
                'fail' => 'original',
                'serve-image' => [
                    'headers' => [
                        'cache-control' => true,
                        'vary-accept' => true,
                    ],
                    'cache-control-header' => 'max-age=2',
                ],
                'convert' => [
                    "quality" => 100
                ]
            ]);
            file_put_contents($in . ".webp", ob_get_contents());
            ob_end_clean();
        } else {
            WebPConvert::convert($in, $in . ".webp", [
                'fail' => 'original',
                'convert' => [
                    'quality' => 100,
                    'max-quality' => 100,
                ]
            ]);
        }
    }

    public function createThumb($width_thumb = 0, $height_thumb = 0, $zoom_crop = '1', $src = '', $watermark = null, $path = \THUMBS, $preview = false, $args = array(), $quality = 100)
    {
        global $config;
        $thumbService = $this->getThumbnailService();
        $thumbService->validateInputs($width_thumb, $height_thumb, $src);
        $imageData = $thumbService->prepareSource($src);
        $src = $imageData['src'];
        $webp = $imageData['webp'];
        $thumbService->cleanupTempFiles(
            [\Tuezy\Service\FileService::class, 'RemoveFilesFromDirInXSeconds'],
            [\Tuezy\Service\FileService::class, 'RemoveEmptySubFolders'],
            $watermark,
            $path
        );
        $imageInfo = $thumbService->getImageInfo($src);
        $image_w = $imageInfo['width'];
        $image_h = $imageInfo['height'];
        $mime_type = $imageInfo['mime'];
        $dimensions = $thumbService->calculateDimensions($width_thumb, $height_thumb, $image_w, $image_h);
        $new_width = $dimensions['width'];
        $new_height = $dimensions['height'];
        $width = $image_w;
        $height = $image_h;
        $imageResource = $thumbService->createImageResource($src, $mime_type);
        $image = $imageResource['image'];
        $func = $imageResource['func'];
        $mime_type = $imageResource['mime'];
        $image_name = basename($src);
        $_new_width = $new_width;
        $_new_height = $new_height;
        if ($zoom_crop == 3) {
            $final_height = $height * ($new_width / $width);
            if ($final_height > $new_height) {
                $new_width = $width * ($new_height / $height);
            } else {
                $new_height = $final_height;
            }
        }
        $canvas = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($canvas, false);
        $color = imagecolorallocatealpha($canvas, 255, 255, 255, 0);
        imagefill($canvas, 0, 0, $color);
        $origin_x = 0;
        $origin_y = 0;
        if ($zoom_crop == 2) {
            $cropData = $thumbService->calculateCenterCrop($new_width, $new_height, $width, $height);
            $new_width = $cropData['width'];
            $new_height = $cropData['height'];
            $origin_x = $cropData['origin_x'];
            $origin_y = $cropData['origin_y'];
        }
        imagesavealpha($canvas, true);
        $cropParams = $thumbService->calculateCropParams($zoom_crop, $width, $height, $new_width, $new_height, '');
        imagecopyresampled(
            $canvas,
            $image,
            $origin_x,
            $origin_y,
            $cropParams['src_x'],
            $cropParams['src_y'],
            $new_width,
            $new_height,
            $cropParams['src_w'],
            $cropParams['src_h']
        );
        if ($preview) {
            $watermark = array();
            $watermark['status'] = 'hienthi';
            $options = $args;
            $overlay_url = $args['watermark'];
        }
        $folder_old = $thumbService->calculateFolderPath($src, $args);
        $upload_dir = $thumbService->buildUploadPath($width_thumb, $height_thumb, $zoom_crop, $folder_old, $watermark, $path);
        if (!file_exists($upload_dir)) {
            if (!@mkdir($upload_dir, 0777, true)) {
                $error = error_get_last();
                $errorMsg = $error ? $error['message'] : 'Unknown error';
                die('Failed to create folders: ' . $upload_dir . ' - ' . $errorMsg);
            }
        }
        if (!empty($watermark['status']) && strpos('hienthi', $watermark['status']) !== false) {
            $options = (isset($options)) ? $options : json_decode($watermark['options'], true)['watermark'];
            $per_scale = $options['per'];
            $per_small_scale = $options['small_per'];
            $max_width_w = $options['max'];
            $min_width_w = $options['min'];
            $opacity = @$options['opacity'];
            $overlay_url = (isset($overlay_url)) ? $overlay_url : \UPLOAD_PHOTO_L . $watermark['photo'];
            $overlay_ext = explode('.', $overlay_url);
            $overlay_ext = trim(strtolower(end($overlay_ext)));
            switch (strtoupper($overlay_ext)) {
                case 'JPG':
                case 'JPEG':
                    $overlay_image = imagecreatefromjpeg($overlay_url);
                    break;
                case 'PNG':
                    $overlay_image = imagecreatefrompng($overlay_url);
                    break;
                case 'GIF':
                    $overlay_image = imagecreatefromgif($overlay_url);
                    break;
                default:
                    die("UNKNOWN IMAGE TYPE: $overlay_url");
            }
            $this->filterOpacity($overlay_image, $opacity);
            $overlay_width = imagesx($overlay_image);
            $overlay_height = imagesy($overlay_image);
            $overlay_padding = 5;
            imagealphablending($canvas, true);
            if (min($_new_width, $_new_height) <= 300) $per_scale = $per_small_scale;
            $oz = max($overlay_width, $overlay_height);
            if ($overlay_width > $overlay_height) $scale = $_new_width / $oz; else $scale = $_new_height / $oz;
            if ($_new_height > $_new_width) $scale = $_new_height / $oz;
            $new_overlay_width = (floor($overlay_width * $scale) - $overlay_padding * 2) / $per_scale;
            $new_overlay_height = (floor($overlay_height * $scale) - $overlay_padding * 2) / $per_scale;
            $scale_w = $new_overlay_width / $new_overlay_height;
            $scale_h = $new_overlay_height / $new_overlay_width;
            $new_overlay_height = $new_overlay_width / $scale_w;
            if ($new_overlay_height > $_new_height) { $new_overlay_height = $_new_height / $per_scale; $new_overlay_width = $new_overlay_height * $scale_w; }
            if ($new_overlay_width > $_new_width) { $new_overlay_width = $_new_width / $per_scale; $new_overlay_height = $new_overlay_width * $scale_h; }
            if (($_new_width / $new_overlay_width) < $per_scale) { $new_overlay_width = $_new_width / $per_scale; $new_overlay_height = $new_overlay_width * $scale_h; }
            if ($_new_height < $_new_width && ($_new_height / $new_overlay_height) < $per_scale) { $new_overlay_height = $_new_height / $per_scale; $new_overlay_width = $new_overlay_height / $scale_h; }
            if ($new_overlay_width > $max_width_w && $new_overlay_width) { $new_overlay_width = $max_width_w; $new_overlay_height = $new_overlay_width * $scale_h; }
            if ($new_overlay_width < $min_width_w && $_new_width <= $min_width_w * 3) { $new_overlay_width = $min_width_w; $new_overlay_height = $new_overlay_width * $scale_h; }
            $new_overlay_width = round($new_overlay_width);
            $new_overlay_height = round($new_overlay_height);
            switch ($options['position']) {
                case 1: $khoancachx = $overlay_padding; $khoancachy = $overlay_padding; break;
                case 2: $khoancachx = abs($_new_width - $new_overlay_width) / 2; $khoancachy = $overlay_padding; break;
                case 3: $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding; $khoancachy = $overlay_padding; break;
                case 4: $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding; $khoancachy = abs($_new_height - $new_overlay_height) / 2; break;
                case 5: $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding; $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding; break;
                case 6: $khoancachx = abs($_new_width - $new_overlay_width) / 2; $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding; break;
                case 7: $khoancachx = $overlay_padding; $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding; break;
                case 8: $khoancachx = $overlay_padding; $khoancachy = abs($_new_height - $new_overlay_height) / 2; break;
                case 9: $khoancachx = abs($_new_width - $new_overlay_width) / 2; $khoancachy = abs($_new_height - $new_overlay_height) / 2; break;
                default: $khoancachx = $overlay_padding; $khoancachy = $overlay_padding; break;
            }
            $overlay_new_image = imagecreatetruecolor($new_overlay_width, $new_overlay_height);
            imagealphablending($overlay_new_image, false);
            imagesavealpha($overlay_new_image, true);
            imagecopyresampled($overlay_new_image, $overlay_image, 0, 0, 0, 0, $new_overlay_width, $new_overlay_height, $overlay_width, $overlay_height);
            imagecopy($canvas, $overlay_new_image, $khoancachx, $khoancachy, 0, 0, $new_overlay_width, $new_overlay_height);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
        }
        if ($preview) {
            $upload_dir = '';
            (new FileService())->RemoveEmptySubFolders(\WATERMARK . '/' . $path . "/");
        }
        if ($upload_dir) {
            if (!isset($_GET['preview'])) {
                $thumbPath = $upload_dir . '/' . $image_name;
                if ($func == 'imagejpeg') $func($canvas, $thumbPath, 100);
                else $func($canvas, $thumbPath, floor($quality * 0.09));
            }
            (new FileService())->removeZeroByte($path);
        }
        header('Content-Type: image/' . $mime_type);
        $cacheMaxAge = 31536000;
        header('Cache-Control: public, max-age=' . $cacheMaxAge . ', immutable');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheMaxAge) . ' GMT');
        if ($upload_dir) {
            $thumbPath = $upload_dir . '/' . $image_name;
            if (file_exists($thumbPath)) {
                $lastModified = filemtime($thumbPath);
                $etag = md5_file($thumbPath);
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
                header('ETag: ' . $etag);
            }
        }
        if ($func == 'imagejpeg') $func($canvas, NULL, 100); else $func($canvas, NULL, floor($quality * 0.09));
        imagedestroy($canvas);
        if ($config['website']['image']['hasWebp'] && ($webp || !$preview) && $upload_dir) {
            $this->converWebp($upload_dir . '/' . $image_name);
        }
        exit;
    }

    public function correctImageOrientation($filename)
    {
        ini_set('memory_limit', '1024M');
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $img = imagecreatefromjpeg($filename);
                    switch ($orientation) {
                        case 3: $image = imagerotate($img, 180, 0); break;
                        case 6: $image = imagerotate($img, -90, 0); break;
                        case 8: $image = imagerotate($img, 90, 0); break;
                    }
                    imagejpeg($image, $filename, 90);
                }
            }
        }
    }

    public function uploadName($name = '')
    {
        $result = '';
        if ($name != '') {
            $rand = rand(1000, 9999);
            $ten_anh = pathinfo($name, PATHINFO_FILENAME);
            $result = $this->getUtilityService()->changeTitle($ten_anh) . "-" . $rand;
        }
        return $result;
    }

    public function getImgSize($photo = '', $patch = '')
    {
        $array = array();
        if ($photo != '') {
            $x = (file_exists($patch)) ? getimagesize($patch) : null;
            $array = (!empty($x)) ? array("p" => $photo, "w" => $x[0], "h" => $x[1], "m" => $x['mime']) : null;
        }
        return $array;
    }

    public function copyImg($photo = '', $constant = '')
    {
        $str = '';
        if ($photo != '' && $constant != '') {
            $rand = rand(1000, 9999);
            $name = pathinfo($photo, PATHINFO_FILENAME);
            $ext = pathinfo($photo, PATHINFO_EXTENSION);
            $photo_new = $name . '-' . $rand . '.' . $ext;
            $oldpath = '../../upload/' . $constant . '/' . $photo;
            $newpath = '../../upload/' . $constant . '/' . $photo_new;
            if (file_exists($oldpath)) {
                if (copy($oldpath, $newpath)) {
                    $str = $photo_new;
                }
            }
        }
        return $str;
    }

    public function uploadImage($file = '', $extension = '', $folder = '', $newname = '', $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) $config = \Tuezy\config(); else { global $config; }
        }
        if (isset($_FILES[$file]) && !$_FILES[$file]['error']) {
            $postMaxSize = ini_get('post_max_size');
            $MaxSize = explode('M', $postMaxSize);
            $MaxSize = $MaxSize[0];
            if ($_FILES[$file]['size'] > $MaxSize * 1048576) return false;
            $ext = explode('.', $_FILES[$file]['name']);
            $ext = strtolower($ext[count($ext) - 1]);
            $name = basename($_FILES[$file]['name'], '.' . $ext);
            if (strpos($extension, $ext) === false) return false;
            if ($newname == '' && file_exists($folder . $_FILES[$file]['name']))
                for ($i = 0; $i < 100; $i++) {
                    if (!file_exists($folder . $name . $i . '.' . $ext)) {
                        $_FILES[$file]['name'] = $name . $i . '.' . $ext;
                        break;
                    }
                }
            else {
                $_FILES[$file]['name'] = $newname . '.' . $ext;
            }
            if (!copy($_FILES[$file]["tmp_name"], $folder . $_FILES[$file]['name'])) {
                if (!move_uploaded_file($_FILES[$file]["tmp_name"], $folder . $_FILES[$file]['name'])) {
                    return false;
                }
            }
            $this->correctImageOrientation($folder . $_FILES[$file]['name']);
            $array = getimagesize($folder . $_FILES[$file]['name']);
            list($image_w, $image_h) = $array;
            $maxWidth = $config['website']['upload']['max-width'];
            $maxHeight = $config['website']['upload']['max-height'];
            if ($image_w > $maxWidth) $this->smartResizeImage($folder . $_FILES[$file]['name'], null, $maxWidth, $maxHeight, true);
            return $_FILES[$file]['name'];
        }
        return false;
    }

    public function smartResizeImage($file = '', $string = null, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false, $quality = 100, $grayscale = false)
    {
        if ($height <= 0 && $width <= 0) return false;
        if ($file === null && $string === null) return false;
        $info = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        if ($proportional) {
            if ($width == 0) $factor = $height / $height_old;
            elseif ($height == 0) $factor = $width / $width_old;
            else $factor = min($width / $width_old, $height / $height_old);
            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        } else {
            $final_width = ($width <= 0) ? $width_old : $width;
            $final_height = ($height <= 0) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;
            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        switch ($info[2]) {
            case IMAGETYPE_JPEG: $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string); break;
            case IMAGETYPE_GIF: $file !== null ? $image = imagecreatefromgif($file) : $image = imagecreatefromstring($string); break;
            case IMAGETYPE_PNG: $file !== null ? $image = imagecreatefrompng($file) : $image = imagecreatefromstring($string); break;
            default: return false;
        }
        if ($grayscale) imagefilter($image, IMG_FILTER_GRAYSCALE);
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
        if ($delete_original) {
            if ($use_linux_commands) exec('rm ' . $file);
            else @unlink($file);
        }
        switch (strtolower($output)) {
            case 'browser': $mime = image_type_to_mime_type($info[2]); header("Content-type: $mime"); $output = NULL; break;
            case 'file': $output = $file; break;
            case 'return': return $image_resized; break;
            default: break;
        }
        switch ($info[2]) {
            case IMAGETYPE_GIF: imagegif($image_resized, $output); break;
            case IMAGETYPE_JPEG: imagejpeg($image_resized, $output, $quality); break;
            case IMAGETYPE_PNG: $quality = 9 - (int)((0.9 * $quality) / 10.0); imagepng($image_resized, $output, $quality); break;
            default: return false;
        }
        return true;
    }

    public function filterOpacity($img = '', $opacity = 80)
    {
        return true;
    }

    public function changeTitle($text = '')
    {
        if ($text != '') {
            $text = strtolower($this->utf8Convert($text));
            $text = preg_replace("/[^a-z0-9-\s]/", "", $text);
            $text = preg_replace('/([\s]+)/', '-', $text);
            $text = str_replace(array('%20', ' '), '-', $text);
            $text = preg_replace('/-+/', '-', $text);
            $text = trim($text, '-');
        }
        return $text;
    }

    public function utf8Convert($str = '')
    {
        if ($str != '') {
            $utf8 = array(
                'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
                'd' => 'đ|Đ',
                'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
                'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
                'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
                'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
                'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
                '' => '`|\~|\!|\@|\#|\|\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\“|\”|\:|\;|_',
            );
            foreach ($utf8 as $ascii => $uni) {
                $str = preg_replace("/($uni)/i", $ascii, $str);
            }
        }
        return $str;
    }
}
