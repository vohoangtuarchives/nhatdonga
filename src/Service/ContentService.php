<?php
namespace Tuezy\Service;

class ContentService
{
    public function __construct(private $d, private $cache)
    {
    }

    public function createSitemap($com = '', $type = '', $field = '', $table = '', $time = '', $changefreq = '', $priority = '', $lang = 'vi', $orderby = '', $menu = true, $configBase = null)
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
        $urlSm = '';
        $sitemap = null;
        if (!empty($type) && !in_array($table, ['photo', 'static'])) {
            $where = 'type = ?';
            $where .= ($table != 'static') ? 'order by ' . $orderby . ' desc' : '';
            $sitemap = $this->d->rawQuery("select slug$lang, date_created from #_$table where $where", array($type));
        }
        if ($menu == true && $field == 'id') {
            $urlSm = $configBase . $com;
            echo '<url>';
            echo '<loc>' . $urlSm . '</loc>';
            echo '<lastmod>' . date('c', time()) . '</lastmod>';
            echo '<changefreq>' . $changefreq . '</changefreq>';
            echo '<priority>' . $priority . '</priority>';
            echo '</url>';
        }
        if (!empty($sitemap)) {
            foreach ($sitemap as $value) {
                if (!empty($value['slug' . $lang])) {
                    $urlSm = $configBase . $value['slug' . $lang];
                    echo '<url>';
                    echo '<loc>' . $urlSm . '</loc>';
                    echo '<lastmod>' . date('c', $value['date_created']) . '</lastmod>';
                    echo '<changefreq>' . $changefreq . '</changefreq>';
                    echo '<priority>' . $priority . '</priority>';
                    echo '</url>';
                }
            }
        }
    }

    public function getStatusNewsletter($confirm_status = 0, $type = '', $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) {
                $config = \Tuezy\config();
            } else {
                global $config;
            }
        }
        $loai = '';
        if (!empty($config['newsletter'][$type]['confirm_status'])) {
            foreach ($config['newsletter'][$type]['confirm_status'] as $key => $value) {
                if ($key == $confirm_status) {
                    $loai = $value;
                    break;
                }
            }
        }
        if ($loai == '') $loai = "Đang chờ duyệt...";
        return $loai;
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
        ob_start();
        include dirname(__DIR__, 2) . "/libraries/sample/gallery/admin.php";
        $str = ob_get_contents();
        ob_clean();
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

    public function checkTitle($data = array(), $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) $config = \Tuezy\config(); else { global $config; }
        }
        $result = array();
        foreach ($config['website']['lang'] as $k => $v) {
            if (isset($data['name' . $k])) {
                $title = trim($data['name' . $k]);
                if (empty($title)) {
                    $result[] = 'Tiêu đề (' . $v . ') không được trống';
                }
            }
        }
        return $result;
    }

    public function checkSlug($data = array())
    {
        $result = 'valid';
        if (isset($data['slug'])) {
            $slug = trim($data['slug']);
            if (!empty($slug)) {
                $tableMap = array(
                    'product' => array("#_product_list", "#_product_cat", "#_product_item", "#_product_sub", "#_product_brand", "#_product"),
                    'news' => array("#_news_list", "#_news_cat", "#_news_item", "#_news_sub", "#_news"),
                    'tags' => array("#_tags")
                );
                $tablesToCheck = array();
                if (!empty($data['table'])) {
                    if (strpos($data['table'], '_') !== false) {
                        $tablesToCheck[] = "#_" . $data['table'];
                    } elseif (isset($tableMap[$data['table']])) {
                        $tablesToCheck = $tableMap[$data['table']];
                    } else {
                        foreach ($tableMap as $tables) {
                            $tablesToCheck = array_merge($tablesToCheck, $tables);
                        }
                    }
                } else {
                    foreach ($tableMap as $tables) {
                        $tablesToCheck = array_merge($tablesToCheck, $tables);
                    }
                }
                $whereConditions = array();
                $whereParams = array();
                $currentId = isset($data['id']) ? (int)$data['id'] : 0;
                if ($currentId > 0 && empty($data['copy'])) {
                    $whereConditions[] = "id != ?";
                    $whereParams[] = $currentId;
                }
                if (!empty($data['type'])) {
                    $whereConditions[] = "type = ?";
                    $whereParams[] = $data['type'];
                }
                $where = '';
                if (!empty($whereConditions)) {
                    $where = implode(' AND ', $whereConditions) . ' AND ';
                }
                $slugParams = array($data['slug'], $data['slug']);
                $allParams = array_merge($whereParams, $slugParams);
                foreach ($tablesToCheck as $v) {
                    $check = $this->d->rawQueryOne("select id from $v where $where (slugvi = ? or slugen = ?) limit 0,1", $allParams);
                    if (!empty($check['id'])) {
                        $result = 'exist';
                        break;
                    }
                }
            } else {
                $result = 'empty';
            }
        }
        return $result;
    }

    public function checkRecaptcha($response = '', $config = null)
    {
        if ($config === null) {
            if (function_exists('Tuezy\\config')) $config = \Tuezy\config(); else { global $config; }
        }
        $result = [];
        $active = $config['googleAPI']['recaptcha']['active'] ?? false;
        if ($active == true && $response != '') {
            $recaptchaResponse = @file_get_contents($config['googleAPI']['recaptcha']['urlapi'] . '?secret=' . $config['googleAPI']['recaptcha']['secretkey'] . '&response=' . $response);
            if ($recaptchaResponse === false) {
                $result['success'] = false;
                $result['error'] = 'Failed to connect to reCAPTCHA service';
                return $result;
            }
            $recaptcha = json_decode($recaptchaResponse);
            if ($recaptcha === null || !is_object($recaptcha)) {
                $result['success'] = false;
                $result['error'] = 'Invalid reCAPTCHA response';
                return $result;
            }
            $result['success'] = isset($recaptcha->success) ? (bool)$recaptcha->success : false;
            if (isset($recaptcha->score)) $result['score'] = (float)$recaptcha->score;
            if (isset($recaptcha->action)) $result['action'] = (string)$recaptcha->action;
            if (isset($recaptcha->{'error-codes'})) $result['error-codes'] = $recaptcha->{'error-codes'};
        } else if (!$active) {
            $result['test'] = true;
            $result['success'] = true;
        } else {
            $result['success'] = false;
            $result['error'] = 'reCAPTCHA response is empty';
        }
        return $result;
    }
}

