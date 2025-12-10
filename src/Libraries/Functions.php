<?php
namespace Tuezy\Libraries;

require \LIBRARIES . 'WebpConvert/vendor/autoload.php';

use WebPConvert\WebPConvert;
use Tuezy\Service\ThumbnailService;
use Tuezy\Service\UrlService;
use Tuezy\Service\ValidationService;
use Tuezy\Service\PaginationService;
use Tuezy\Service\FileService;
use Tuezy\Service\AuthService;
use Tuezy\Service\ImageService;
use Tuezy\Service\CatalogService;
use Tuezy\Service\ContentService;
use Tuezy\Service\UtilityService;

class Functions
{
    private $hash;
    private $lang;
    private $slug_lang;
    private $comlang;
    private ?ThumbnailService $thumbnailService = null;
    private ?UrlService $urlService = null;
    private ?ValidationService $validationService = null;
    private ?PaginationService $paginationService = null;
    private ?FileService $fileService = null;
    private ?AuthService $authService = null;
    private ?ImageService $imageService = null;
    private ?CatalogService $catalogService = null;
    private ?ContentService $contentService = null;
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

    private function getUrlService(): UrlService
    {
        if ($this->urlService === null) $this->urlService = new UrlService();
        return $this->urlService;
    }

    private function getValidationService(): ValidationService
    {
        if ($this->validationService === null) $this->validationService = new ValidationService();
        return $this->validationService;
    }

    private function getPaginationService(): PaginationService
    {
        if ($this->paginationService === null) $this->paginationService = new PaginationService();
        return $this->paginationService;
    }

    private function getFileService(): FileService
    {
        if ($this->fileService === null) $this->fileService = new FileService();
        return $this->fileService;
    }

    private function getAuthService(): AuthService
    {
        if ($this->authService === null) $this->authService = new AuthService($this->d, $this->cache);
        return $this->authService;
    }

    private function getImageService(): ImageService
    {
        if ($this->imageService === null) $this->imageService = new ImageService($this->d, $this->cache, $this->thumbnailService);
        return $this->imageService;
    }

    private function getCatalogService(): CatalogService
    {
        if ($this->catalogService === null) $this->catalogService = new CatalogService($this->d, $this->cache);
        return $this->catalogService;
    }

    private function getContentService(): ContentService
    {
        if ($this->contentService === null) $this->contentService = new ContentService($this->d, $this->cache);
        return $this->contentService;
    }

    private function getUtilityService(): UtilityService
    {
        if ($this->utilityService === null) $this->utilityService = new UtilityService($this->d, $this->cache);
        return $this->utilityService;
    }

    private function baseUrl($configBase = null)
    {
        if ($configBase !== null) return $configBase;
        if (function_exists('Tuezy\\config')) {
            $config = \Tuezy\config();
            $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
            $configUrl = $config['database']['server-name'] . $config['database']['url'];
            return $http . $configUrl;
        } else {
            global $configBase;
            return $configBase;
        }
    }

    public function get_comorigin($com = '')
    {
        $result  = array();
        $comlang = $this->comlang;
        if (!empty($comlang) && $com != '') {
            foreach ($comlang as $kcomlang => $vcomlang) {
                foreach ($vcomlang as $vcom) {
                    if ($com == $vcom) {
                        $result['com_vi'] = $vcomlang['vi'];
                        $result['com_en'] = $vcomlang['en'];
                        $result['com_zh'] = $vcomlang['zh'];
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function set_comlang($comlang)
    {
        $this->comlang = $comlang;
    }

    public function check_com($com)
    {
        $comlang = $this->comlang;
        foreach ($comlang as $key => $value) {
            if ($value[$this->lang] == $com) {
                return $com;
            }
        }
    }

    public function get_comlang($com)
    {
        return $this->url($this->get_com_key($com));
    }

    public function get_com_key($com)
    {
        return $this->comlang[$com][$this->slug_lang];
    }

    public function set_language($lang = 'vi')
    {
        $this->lang      = $lang;
        $this->slug_lang = in_array($this->lang, array('vi', 'en', 'zh')) ? $this->lang : 'vi';
    }

    public function markdown($path = '', $params = array())
    {
        return $this->getUtilityService()->markdown($path, $params);
    }

    public function checkURL($index = false, $configBase = null)
    {
        $this->getUrlService()->checkURL((bool)$index, $configBase);
    }

    public function checkHTTP($http, $arrayDomain, &$configBase, $configUrl)
    {
        if (count($arrayDomain) == 0 && $http == 'https://') {
            $configBase = 'http://' . $configUrl;
        }
    }

    public function createSitemap($com = '', $type = '', $field = '', $table = '', $time = '', $changefreq = '', $priority = '', $lang = 'vi', $orderby = '', $menu = true, $configBase = null)
    {
        return $this->getContentService()->createSitemap($com, $type, $field, $table, $time, $changefreq, $priority, $lang, $orderby, $menu, $configBase);
    }

    public function cleanInput($input = '', $type = '')
    {
        return $this->getValidationService()->cleanInput((string)$input, (string)$type);
    }

    public function sanitize($input = '', $type = '')
    {
        return $this->getValidationService()->sanitize($input, $type);
    }

    public function checkLoginAdmin()
    {
        return $this->getAuthService()->checkLoginAdmin();
    }

    public function encryptPassword($secret = '', $str = '', $salt = '')
    {
        return $this->getAuthService()->encryptPassword($secret, $str, $salt);
    }

    public function checkPermission($com = '', $act = '', $type = '', $array = null, $case = '')
    {
        return $this->getAuthService()->checkPermission($com, $act, $type, $array, $case);
    }

    public function checkRole($config = null, $loginAdmin = null)
    {
        return $this->getAuthService()->checkRole($config, $loginAdmin);
    }

    public function getStatusNewsletter($confirm_status = 0, $type = '', $config = null)
    {
        return $this->getContentService()->getStatusNewsletter($confirm_status, $type, $config);
    }

    public function databaseMaintenance($action = '', $tables = array())
    {
        return $this->getUtilityService()->databaseMaintenance($action, $tables);
    }

    public function formatMoney($price = 0, $unit = 'đ', $html = false)
    {
        return $this->getUtilityService()->formatMoney($price, $unit, $html);
    }

    public function isPhone($number)
    {
        return $this->getValidationService()->isPhone($number);
    }

    public function formatPhone($number, $dash = ' ')
    {
        return $this->getUtilityService()->formatPhone($number, $dash);
    }

    public function parsePhone($number)
    {
        return $this->getUtilityService()->parsePhone($number);
    }

    public function isAlphaNum($str)
    {
        if (preg_match('/^[a-z0-9]+$/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function isMatch($value1, $value2)
    {
        if ($value1 == $value2) {
            return true;
        } else {
            return false;
        }
    }

    public function isDecimal($number)
    {
        if (preg_match('/^\d{1,10}(\.\d{1,4})?$/', $number)) {
            return true;
        } else {
            return false;
        }
    }

    public function isCoords($str)
    {
        if (preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isUrl($str)
    {
        if (preg_match('/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isYoutube($str)
    {
        if (preg_match('/https?:\/\/(?:[a-zA_Z]{2,3}.)?(?:youtube\.com\/watch\?)((?:[\w\d\-\_\=]+&amp;(?:amp;)?)*v(?:&lt;[A-Z]+&gt;)?=([0-9a-zA-Z\-\_]+))/i', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isFanpage($str)
    {
        if (preg_match('/^(https?:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-\.]*)/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isDate($str)
    {
        return $this->getValidationService()->isDate($str);
    }

    public function isNumber($numbs)
    {
        return $this->getValidationService()->isNumber($numbs);
    }

    public function checkAccount($data = '', $type = '', $tbl = '', $id = 0)
    {
        return $this->getUtilityService()->checkAccount($data, $type, $tbl, $id);
    }

    public function checkTitle($data = array(), $config = null)
    {
        return $this->getContentService()->checkTitle($data, $config);
    }

    public function checkSlug($data = array())
    {
        return $this->getContentService()->checkSlug($data);
    }

    public function checkRecaptcha($response = '', $config = null)
    {
        return $this->getContentService()->checkRecaptcha($response, $config);
    }

    public function checkLoginMember($configBase = null, $loginMember = null)
    {
        return $this->getAuthService()->checkLoginMember($configBase, $loginMember);
    }

    public function getYoutube($url = '')
    {
        if ($url != '') {
            $parts = parse_url($url);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (isset($qs['v'])) return $qs['v'];
                else if ($qs['vi']) return $qs['vi'];
            }
            if (isset($parts['path'])) {
                $path = explode('/', trim($parts['path'], '/'));
                return $path[count($path) - 1];
            }
        }
        return false;
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

    public function listsGallery($file = '')
    {
        $result = array();
        if (!empty($file) && !empty($_POST['fileuploader-list-' . $file])) {
            $fileLists = '';
            $fileLists = str_replace('"', '', $_POST['fileuploader-list-' . $file]);
            $fileLists = str_replace('[', '', $fileLists);
            $fileLists = str_replace(']', '', $fileLists);
            $fileLists = str_replace('{', '', $fileLists);
            $fileLists = str_replace('}', '', $fileLists);
            $fileLists = str_replace('0:/', '', $fileLists);
            $fileLists = str_replace('file:', '', $fileLists);
            $result = explode(',', $fileLists);
        }
        return $result;
    }

    public function galleryFiler($numb = 1, $id = 0, $photo = '', $name = '', $folder = '', $col = '')
    {
        $params = array();
        $params['numb'] = $numb;
        $params['id'] = $id;
        $params['photo'] = $photo;
        $params['name'] = $name;
        $params['folder'] = $folder;
        $params['col'] = $col;
        $str = $this->markdown('gallery/admin', $params);
        return $str;
    }

    public function deleteGallery()
    {
        $row = $this->d->rawQuery("select id, com, photo from #_gallery where hash != '' and date_created < " . (time() - 3 * 3600));
        $array = array("product" => \UPLOAD_PRODUCT, "news" => \UPLOAD_NEWS);
        if ($row) {
            foreach ($row as $item) {
                @unlink($array[$item['com']] . $item['photo']);
                $this->d->rawQuery("delete from #_gallery where id = " . $item['id']);
            }
        }
    }

    public function generateHash()
    {
        if (!$this->hash) {
            $this->hash = $this->stringRandom(10);
        }
        return $this->hash;
    }

    public function makeDate($time = 0, $dot = '.', $lang = 'vi', $f = false)
    {
        $str = ($lang == 'vi') ? date("d{$dot}m{$dot}Y", $time) : date("m{$dot}d{$dot}Y", $time);
        if ($f == true) {
            $thu['vi'] = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
            $thu['en'] = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $str = $thu[$lang][date('w', $time)] . ', ' . $str;
        }
        return $str;
    }

    public function alert($notify = '')
    {
        echo '<script language="javascript">alert("' . $notify . '")</script>';
    }

    public function deleteFile($file = '')
    {
        return @unlink($file);
    }

    public function transfer($msg = '', $page = '', $numb = true, $configBase = null)
    {
        if ($configBase === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
                $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
                $configUrl = $config['database']['server-name'] . $config['database']['url'];
                $configBase = $http . $configUrl;
            } else {
                global $configBase;
            }
        }
        $basehref = $configBase;
        $showtext = $msg;
        $page_transfer = $page;
        $numb = $numb;
        include("./templates/layout/transfer.php");
        exit();
    }

    public function redirect($url = '', $response = null)
    {
        header("location:$url", true, $response);
        exit();
    }

    public function dump($value = '', $exit = false)
    {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
        if ($exit) exit();
    }

    public function pagination($totalq = 0, $perPage = 10, $page = 1, $url = '?')
    {
        $urlpos = strpos($url, "?");
        $url = ($urlpos) ? $url . "&" : $url . "?";
        $total = $totalq;
        $adjacents = "2";
        $firstlabel = "<i class='fas fa-angle-double-left'></i>";
        $prevlabel = "<i class='fas fa-angle-left'></i>";
        $nextlabel = "<i class='fas fa-angle-right'></i>";
        $lastlabel = "<i class='fas fa-angle-double-right'></i>";
        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $perPage;
        $prev = $page - 1;
        $next = $page + 1;
        $lastpage = ceil($total / $perPage);
        $lpm1 = $lastpage - 1;
        $pagination = "";
        if ($lastpage > 1) {
            $pagination .= "<ul class='pagination pagination-cus flex-wrap justify-content-center mb-0'>";
            if ($page > 1) {
                $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$firstlabel}</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$prev}'>{$prevlabel}</a></li>";
            } else {
                $pagination .= "<li class='page-item disabled'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$firstlabel}</a></li>";
                $pagination .= "<li class='page-item disabled'><a class='page-link' href='{$url}p={$prev}'>{$prevlabel}</a></li>";
            }
            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) $pagination .= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                    else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>...</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lpm1}'>{$lpm1}</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lastpage}'>{$lastpage}</a></li>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>1</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=2'>2</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>...</a></li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>...</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lpm1}'>{$lpm1}</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lastpage}'>{$lastpage}</a></li>";
                } else {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>1</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=2'>2</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=1'>...</a></li>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }
                }
            }
            if ($page < $lastpage) {
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$next}'>{$nextlabel}</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=$lastpage'>{$lastlabel}</a></li>";
            } else {
                $pagination .= "<li class='page-item disabled'><a class='page-link' href='{$url}p={$next}'>{$nextlabel}</a></li>";
                $pagination .= "<li class='page-item disabled'><a class='page-link' href='{$url}p=$lastpage'>{$lastlabel}</a></li>";
            }
            $pagination .= "</ul>";
        }
        return $pagination;
    }

    public function paging($totalq = 0, $perPage = 10, $page = 1, $url = '?')
    {
        return $this->pagination($totalq, $perPage, $page, $url);
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
                '' => '`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\“|\”|\:|\;|_',
            );
            foreach ($utf8 as $ascii => $uni) {
                $str = preg_replace("/($uni)/i", $ascii, $str);
            }
        }
        return $str;
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

    public function getRealIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getPageURL()
    {
        return $this->getUrlService()->getPageURL();
    }

    public function getCurrentPageURL()
    {
        return $this->getUrlService()->getCurrentPageURL();
    }

    public function getCurrentPageURL_CANO()
    {
        return $this->getUrlService()->getCurrentPageURL_CANO();
    }

    public function hasFile($file)
    {
        return $this->getFileService()->hasFile($file);
    }

    public function sizeFile($file)
    {
        return $this->getFileService()->sizeFile($file);
    }

    public function checkFile($file, $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        $result = true;
        if ($this->hasFile($file)) {
            if ($this->sizeFile($file) > $config['website']['video']['max-size']) {
                $result = false;
            }
        }
        return $result;
    }

    public function checkExtFile($file, $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        $result = true;
        if ($this->hasFile($file)) {
            $ext = $this->infoPath($_FILES[$file]["name"], 'extension');
            if (!in_array($ext, $config['website']['video']['extension'])) {
                $result = false;
            }
        }
        return $result;
    }

    public function infoPath($filename = '', $type = '')
    {
        return $this->getFileService()->infoPath($filename, $type);
    }

    public function formatBytes($size, $precision = 2)
    {
        $result = array();
        $base = log($size, 1024);
        $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');
        $result['numb'] = round(pow(1024, $base - floor($base)), $precision);
        $result['ext'] = $suffixes[floor($base)];
        return $result;
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

    public function getImgSize($photo = '', $patch = '')
    {
        $array = array();
        if ($photo != '') {
            $x = (file_exists($patch)) ? getimagesize($patch) : null;
            $array = (!empty($x)) ? array("p" => $photo, "w" => $x[0], "h" => $x[1], "m" => $x['mime']) : null;
        }
        return $array;
    }

    public function uploadName($name = '')
    {
        $result = '';
        if ($name != '') {
            $rand = rand(1000, 9999);
            $ten_anh = pathinfo($name, PATHINFO_FILENAME);
            $result = $this->changeTitle($ten_anh) . "-" . $rand;
        }
        return $result;
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
            case IMAGETYPE_JPEG:
                $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_GIF:
                $file !== null ? $image = imagecreatefromgif($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_PNG:
                $file !== null ? $image = imagecreatefrompng($file) : $image = imagecreatefromstring($string);
                break;
            default:
                return false;
        }
        if ($grayscale) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }
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
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }
        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9 * $quality) / 10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default:
                return false;
        }
        return true;
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
                        case 3:
                            $image = imagerotate($img, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($img, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($img, 90, 0);
                            break;
                    }
                    imagejpeg($image, $filename, 90);
                }
            }
        }
    }

    public function uploadImage($file = '', $extension = '', $folder = '', $newname = '', $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        if (isset($_FILES[$file]) && !$_FILES[$file]['error']) {
            $postMaxSize = ini_get('post_max_size');
            $MaxSize = explode('M', $postMaxSize);
            $MaxSize = $MaxSize[0];
            if ($_FILES[$file]['size'] > $MaxSize * 1048576) {
                $this->alert('Dung lượng file không được vượt quá ' . $postMaxSize);
                return false;
            }
            $ext = explode('.', $_FILES[$file]['name']);
            $ext = strtolower($ext[count($ext) - 1]);
            $name = basename($_FILES[$file]['name'], '.' . $ext);
            if (strpos($extension, $ext) === false) {
                $this->alert('Chỉ hỗ trợ upload file dạng ' . $extension);
                return false;
            }
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

    public function removeDir($dirname = '')
    {
        if (is_dir($dirname)) $dir_handle = opendir($dirname);
        if (!isset($dir_handle) || $dir_handle == false) return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file)) unlink($dirname . "/" . $file);
                else $this->removeDir($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function RemoveEmptySubFolders($path = '')
    {
        $empty = true;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            if (is_dir($file)) {
                if (!$this->RemoveEmptySubFolders($file)) $empty = false;
            } else {
                $empty = false;
            }
        }
        if ($empty) {
            if (is_dir($path)) {
                rmdir($path);
            }
        }
        return $empty;
    }

    public function RemoveFilesFromDirInXSeconds($dir = '', $seconds = 3600)
    {
        $files = glob(rtrim($dir, '/') . "/*");
        $now = time();
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    if ($now - filemtime($file) >= $seconds) {
                        unlink($file);
                    }
                } else {
                    $this->RemoveFilesFromDirInXSeconds($file, $seconds);
                }
            }
        }
    }

    public function removeZeroByte($dir)
    {
        $files = glob(rtrim($dir, '/') . "/*");
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (!filesize($file)) {
                        unlink($file);
                    }
                } else {
                    $this->removeZeroByte($file);
                }
            }
        }
    }

    public function filterOpacity($img = '', $opacity = 80)
    {
        return true;
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
            [$this, 'RemoveFilesFromDirInXSeconds'],
            [$this, 'RemoveEmptySubFolders'],
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
            if ($overlay_width > $overlay_height) {
                $scale = $_new_width / $oz;
            } else {
                $scale = $_new_height / $oz;
            }
            if ($_new_height > $_new_width) {
                $scale = $_new_height / $oz;
            }
            $new_overlay_width = (floor($overlay_width * $scale) - $overlay_padding * 2) / $per_scale;
            $new_overlay_height = (floor($overlay_height * $scale) - $overlay_padding * 2) / $per_scale;
            $scale_w = $new_overlay_width / $new_overlay_height;
            $scale_h = $new_overlay_height / $new_overlay_width;
            $new_overlay_height = $new_overlay_width / $scale_w;
            if ($new_overlay_height > $_new_height) {
                $new_overlay_height = $_new_height / $per_scale;
                $new_overlay_width = $new_overlay_height * $scale_w;
            }
            if ($new_overlay_width > $_new_width) {
                $new_overlay_width = $_new_width / $per_scale;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if (($_new_width / $new_overlay_width) < $per_scale) {
                $new_overlay_width = $_new_width / $per_scale;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if ($_new_height < $_new_width && ($_new_height / $new_overlay_height) < $per_scale) {
                $new_overlay_height = $_new_height / $per_scale;
                $new_overlay_width = $new_overlay_height / $scale_h;
            }
            if ($new_overlay_width > $max_width_w && $new_overlay_width) {
                $new_overlay_width = $max_width_w;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if ($new_overlay_width < $min_width_w && $_new_width <= $min_width_w * 3) {
                $new_overlay_width = $min_width_w;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            $new_overlay_width = round($new_overlay_width);
            $new_overlay_height = round($new_overlay_height);
            switch ($options['position']) {
                case 1:
                    $khoancachx = $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
                case 2:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = $overlay_padding;
                    break;
                case 3:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
                case 4:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                case 5:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 6:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 7:
                    $khoancachx = $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 8:
                    $khoancachx = $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                case 9:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                default:
                    $khoancachx = $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
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
            $this->RemoveEmptySubFolders(\WATERMARK . '/' . $path . "/");
        }
        if ($upload_dir) {
            if (!isset($_GET['preview'])) {
                $thumbPath = $upload_dir . '/' . $image_name;
                if ($func == 'imagejpeg') {
                    $func($canvas, $thumbPath, 100);
                } else {
                    $func($canvas, $thumbPath, floor($quality * 0.09));
                }
            }
            $this->removeZeroByte($path);
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
        if ($func == 'imagejpeg') {
            $func($canvas, NULL, 100);
        } else {
            $func($canvas, NULL, floor($quality * 0.09));
        }
        imagedestroy($canvas);
        if ($config['website']['image']['hasWebp'] && ($webp || !$preview) && $upload_dir) {
            $this->converWebp($upload_dir . '/' . $image_name);
        }
        exit;
    }

    public function stringRandom($sokytu = 10)
    {
        $str = '';
        if ($sokytu > 0) {
            $chuoi = 'ABCDEFGHIJKLMNOPQRSTUVWXYZWabcdefghijklmnopqrstuvwxyzw0123456789';
            for ($i = 0; $i < $sokytu; $i++) {
                $vitri = mt_rand(0, strlen($chuoi) - 1);
                $str = $str . substr($chuoi, $vitri, 1);
            }
        }
        return $str;
    }

    public function digitalRandom($min = 1, $max = 10, $num = 10)
    {
        $result = '';
        if ($num > 0) {
            for ($i = 0; $i < $num; $i++) {
                $result .= rand($min, $max);
            }
        }
        return $result;
    }

    public function getPermission($id_permission = 0)
    {
        $row = $this->cache->get("select * from #_permission_group where find_in_set('hienthi',status) order by numb,id desc", null, "result", 7200);
        $str = '<select id="id_permission" name="data[id_permission]" class="form-control select2"><option value="0">Nhóm quyền</option>';
        foreach ($row as $v) {
            if ($v["id"] == (int)@$id_permission) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function orderStatus($status = 0)
    {
        $row = $this->cache->get("select * from #_order_status order by id", null, "result", 7200);
        $str = '<select id="order_status" name="data[order_status]" class="form-control custom-select text-sm"><option value="0">Chọn tình trạng</option>';
        foreach ($row as $v) {
            if (isset($_REQUEST['order_status']) && ($v["id"] == (int)$_REQUEST['order_status']) || ($v["id"] == $status)) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getInfoDetail($cols = '', $table = '', $id = 0)
    {
        $row = array();
        if (!empty($cols) && !empty($table) && !empty($id)) {
            $row = $this->cache->get("select $cols from #_$table where id = ? limit 0,1", array($id), "fetch", 7200);
        }
        return $row;
    }

    public function joinCols($array = null, $column = null)
    {
        $str = '';
        $arrayTemp = array();
        if ($array && $column) {
            foreach ($array as $k => $v) {
                if (!empty($v[$column])) {
                    $arrayTemp[] = $v[$column];
                }
            }
            if (!empty($arrayTemp)) {
                $arrayTemp = array_unique($arrayTemp);
                $str = implode(",", $arrayTemp);
            }
        }
        return $str;
    }

    function orderPayments()
    {
        $row = $this->cache->get("select * from #_news where type='hinh-thuc-thanh-toan' order by numb,id desc", null, "result", 7200);
        $str = '<select id="order_payment" name="order_payment" class="form-control custom-select text-sm"><option value="0">Chọn hình thức thanh toán</option>';
        foreach ($row as $v) {
            if (isset($_REQUEST['order_payment']) && ($v["id"] == (int)$_REQUEST['order_payment'])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getColor($id = 0)
    {
        global $type;
        if ($id) {
            $temps = $this->d->rawQuery("select id_color from #_product_sale where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_color') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }
        $row_color = $this->d->rawQuery("select namevi, id from #_color where type = ? order by numb,id desc", array($type));
        $str = '<select id="dataColor" name="dataColor[]" class="select multiselect" multiple="multiple" >';
        for ($i = 0; $i < count($row_color); $i++) {
            if (!empty($temps)) {
                if (in_array($row_color[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $row_color[$i]["id"] . '" ' . $selected . ' /> ' . $row_color[$i]["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getSize($id = 0)
    {
        global $type;
        if ($id) {
            $temps = $this->d->rawQuery("select id_size from #_product_sale where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_size') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }
        $row_size = $this->d->rawQuery("select namevi, id from #_size where type = ? order by numb,id desc", array($type));
        $str = '<select id="dataSize" name="dataSize[]" class="select multiselect" multiple="multiple" >';
        for ($i = 0; $i < count($row_size); $i++) {
            if (!empty($temps)) {
                if (in_array($row_size[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $row_size[$i]["id"] . '" ' . $selected . ' /> ' . $row_size[$i]["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getTags($id = 0, $element = '', $table = '', $type = '')
    {
        if ($id) {
            $temps = $this->d->rawQuery("select id_tags from #_" . $table . " where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_tags') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }
        $row_tags = $this->cache->get("select namevi, id from #_tags where type = ? order by numb,id desc", array($type), "result", 7200);
        $str = '<select id="' . $element . '" name="' . $element . '[]" class="select multiselect" multiple="multiple" >';
        for ($i = 0; $i < count($row_tags); $i++) {
            if (!empty($temps)) {
                if (in_array($row_tags[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $row_tags[$i]["id"] . '" ' . $selected . ' /> ' . $row_tags[$i]["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getAjaxCategory($table = '', $level = '', $type = '', $title_select = 'Chọn danh mục', $class_select = 'select-category')
    {
        $where = '';
        $params = array($type);
        $id_parent = 'id_' . $level;
        $data_level = '';
        $data_type = 'data-type="' . $type . '"';
        $data_table = '';
        $data_child = '';
        if ($level == 'list') {
            $data_level = 'data-level="0"';
            $data_table = 'data-table="#_' . $table . '_cat"';
            $data_child = 'data-child="id_cat"';
        } else if ($level == 'cat') {
            $data_level = 'data-level="1"';
            $data_table = 'data-table="#_' . $table . '_item"';
            $data_child = 'data-child="id_item"';
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
        } else if ($level == 'item') {
            $data_level = 'data-level="2"';
            $data_table = 'data-table="#_' . $table . '_sub"';
            $data_child = 'data-child="id_sub"';
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
        } else if ($level == 'sub') {
            $data_level = '';
            $data_type = '';
            $class_select = '';
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
            $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;
            $where .= ' and id_item = ?';
            array_push($params, $iditem);
        } else if ($level == 'brand') {
            $data_level = '';
            $data_type = '';
            $class_select = '';
        }
        $rows = $this->cache->get("select namevi, id from #_" . $table . "_" . $level . " where type = ? " . $where . " order by numb,id desc", $params, "result", 7200);
        $str = '<select id="' . $id_parent . '" name="data[' . $id_parent . ']" ' . $data_level . ' ' . $data_type . ' ' . $data_table . ' ' . $data_child . ' class="form-control select2 ' . $class_select . '"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getLinkCategory($table = '', $level = '', $type = '', $title_select = 'Chọn danh mục')
    {
        $where = '';
        $params = array($type);
        $id_parent = 'id_' . $level;
        if ($level == 'cat') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
        } else if ($level == 'item') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
        } else if ($level == 'sub') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
            $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;
            $where .= ' and id_item = ?';
            array_push($params, $iditem);
        }
        $rows = $this->cache->get("select namevi, id from #_" . $table . "_" . $level . " where type = ? " . $where . " order by numb,id desc", $params, "result", 7200);
        $str = '<select id="' . $id_parent . '" name="' . $id_parent . '" onchange="onchangeCategory($(this))" class="form-control filter-category select2"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getAjaxPlace($table = '', $title_select = 'Chọn danh mục')
    {
        $where = '';
        $params = array('0');
        $id_parent = 'id_' . $table;
        $data_level = '';
        $data_table = '';
        $data_child = '';
        if ($table == 'city') {
            $data_level = 'data-level="0"';
            $data_table = 'data-table="#_district"';
            $data_child = 'data-child="id_district"';
        } else if ($table == 'district') {
            $data_level = 'data-level="1"';
            $data_table = 'data-table="#_ward"';
            $data_child = 'data-child="id_ward"';
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
        } else if ($table == 'ward') {
            $data_level = '';
            $data_table = '';
            $data_child = '';
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
            $iddistrict = (isset($_REQUEST['id_district'])) ? htmlspecialchars($_REQUEST['id_district']) : 0;
            $where .= ' and id_district = ?';
            array_push($params, $iddistrict);
        }
        $rows = $this->cache->get("select name, id from #_" . $table . " where id <> ? " . $where . " order by id asc", $params, "result", 7200);
        $str = '<select id="' . $id_parent . '" name="data[' . $id_parent . ']" ' . $data_level . ' ' . $data_table . ' ' . $data_child . ' class="form-control select2 select-place"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function getLinkPlace($table = '', $title_select = 'Chọn danh mục')
    {
        $where = '';
        $params = array('0');
        $id_parent = 'id_' . $table;
        if ($table == 'district') {
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
        } else if ($table == 'ward') {
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
            $iddistrict = (isset($_REQUEST['id_district'])) ? htmlspecialchars($_REQUEST['id_district']) : 0;
            $where .= ' and id_district = ?';
            array_push($params, $iddistrict);
        }
        $rows = $this->cache->get("select name, id from #_" . $table . " where id <> ? " . $where . " order by id asc", $params, "result", 7200);
        $str = '<select id="' . $id_parent . '" name="' . $id_parent . '" onchange="onchangeCategory($(this))" class="form-control filter-category select2"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    public function buildSchemaProduct($id_pro, $name, $image, $description, $code_pro, $name_brand, $name_author, $url, $price)
    {
        $str = '{';
        $str .= '"@context": "https://schema.org/",';
        $str .= '"@type": "Product",';
        $str .= '"name": "' . $name . '",';
        $str .= '"image":[';
        $str .= '"' . $image . '"';
        $str .= '],';
        $str .= '"description": "' . $description . '",';
        $str .= '"sku":"SP0' . $id_pro . '",';
        $str .= '"mpn": "' . $code_pro . '",';
        $str .= '"brand":{';
        $str .= '"@type": "Brand",';
        $str .= '"name": "' . $name_brand . '"';
        $str .= '},';
        $str .= '"review":{';
        $str .= '"@type": "Review",';
        $str .= '"reviewRating":{';
        $str .= '"@type": "Rating",';
        $str .= '"ratingValue": "5",';
        $str .= '"bestRating": "5"';
        $str .= '},';
        $str .= '"author":{';
        $str .= '"@type": "Person",';
        $str .= '"name": "' . $name_author . '"';
        $str .= '}';
        $str .= '},';
        $str .= '"aggregateRating":{';
        $str .= '"@type": "AggregateRating",';
        $str .= '"ratingValue": "4.4",';
        $str .= '"reviewCount": "89"';
        $str .= '},';
        $str .= '"offers":{';
        $str .= '"@type": "Offer",';
        $str .= '"url": "' . $url . '",';
        $str .= '"priceCurrency": "VND",';
        $str .= '"priceValidUntil": "2099-11-20",';
        $str .= '"price": "' . $price . '",';
        $str .= '"itemCondition": "https://schema.org/UsedCondition",';
        $str .= '"availability": "https://schema.org/InStock"';
        $str .= '}';
        $str .= '}';
        $str = json_encode(json_decode($str), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $str;
    }

    public function buildSchemaArticle($id_news, $name, $image, $ngaytao, $ngaysua, $name_author, $url, $logo, $url_author)
    {
        $str = '{';
        $str .= '"@context": "https://schema.org",';
        $str .= '"@type": "NewsArticle",';
        $str .= '"mainEntityOfPage": {';
        $str .= '"@type": "WebPage",';
        $str .= '"@id": "' . $url . '"';
        $str .= '},';
        $str .= '"headline": "' . $name . '",';
        $str .= '"image":"' . $image . '",';
        $str .= '"datePublished": "' . date('c', $ngaytao) . '",';
        $str .= '"dateModified": "' . date('c', $ngaysua) . '",';
        $str .= '"author":{';
        $str .= '"@type": "Person",';
        $str .= '"name": "' . $name_author . '",';
        $str .= '"url": "' . $url_author . '"';
        $str .= '},';
        $str .= '"publisher": {';
        $str .= '"@type": "Organization",';
        $str .= '"name": "' . $name_author . '",';
        $str .= '"logo": {';
        $str .= '"@type": "ImageObject",';
        $str .= '"url": "' . $logo . '"';
        $str .= '}';
        $str .= '}';
        $str .= '}';
        $str = json_encode(json_decode($str), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $str;
    }

    public function emptyString($str): string{
        return $str ?? '';
    }

    public function htmlspecialchars_decode($str): string{
        return htmlspecialchars_decode($this->emptyString($str));
    }
}
