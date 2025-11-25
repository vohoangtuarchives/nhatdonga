<?php

namespace Tuezy;

/**
 * DataProvider - Centralized data fetching with caching
 * Refactors repetitive cache->get() calls in sources files
 */
class DataProvider
{
    private $cache;
    private string $lang;
    private string $seolang;

    public function __construct($cache, string $lang = 'vi', string $seolang = 'vi')
    {
        $this->cache = $cache;
        $this->lang = $lang;
        $this->seolang = $seolang;
    }

    /**
     * Get product list
     * 
     * @param string $type Product type
     * @param int $limit Limit results
     * @return array
     */
    public function getProductList(string $type = 'san-pham', int $limit = 0): array
    {
        $lang = $this->lang;
        $sql = "SELECT id, name$lang, slug$lang, type, photo from #_product_list 
                where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) 
                order by numb";
        
        if ($limit > 0) {
            $sql .= " limit 0,$limit";
        }
        
        return $this->cache->get($sql, [$type], 'result', 7200);
    }

    /**
     * Get photo by type and act
     * 
     * @param string $type Photo type
     * @param string $act Photo act
     * @return array|null
     */
    public function getPhoto(string $type, string $act): ?array
    {
        return $this->cache->get(
            "select photo from #_photo where type = ? and act = ? and find_in_set('hienthi',status) limit 0,1",
            [$type, $act],
            'fetch',
            7200
        );
    }

    /**
     * Get logo with options
     * 
     * @return array|null
     */
    public function getLogo(): ?array
    {
        return $this->cache->get(
            "select id, photo, options from #_photo where type = ? and act = ? limit 0,1",
            ['logo', 'photo_static'],
            'fetch',
            7200
        );
    }

    /**
     * Get photos by type
     * 
     * @param string $type Photo type
     * @return array
     */
    public function getPhotos(string $type): array
    {
        $lang = $this->lang;
        return $this->cache->get(
            "select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc",
            [$type],
            'result',
            7200
        );
    }

    /**
     * Get static content
     * 
     * @param string $type Static type
     * @return array|null
     */
    public function getStatic(string $type): ?array
    {
        $lang = $this->lang;
        return $this->cache->get(
            "select name$lang, content$lang from #_static where type = ? limit 0,1",
            [$type],
            'fetch',
            7200
        );
    }

    /**
     * Get news by type
     * 
     * @param string $type News type
     * @param int $limit Limit results
     * @return array
     */
    public function getNews(string $type, int $limit = 0): array
    {
        $lang = $this->lang;
        $sql = "select name$lang, slugvi, slugen, id, photo from #_news 
                where type = ? and find_in_set('hienthi',status) 
                order by numb,id desc";
        
        if ($limit > 0) {
            $sql .= " limit 0,$limit";
        }
        
        return $this->cache->get($sql, [$type], 'result', 7200);
    }

    /**
     * Get news list by type
     * 
     * @param string $type News type
     * @return array
     */
    public function getNewsList(string $type): array
    {
        $lang = $this->lang;
        return $this->cache->get(
            "select name$lang, slugvi, slugen, id, photo from #_news_list 
             where type = ? and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$type],
            'result',
            7200
        );
    }

    /**
     * Get featured news
     * 
     * @param string $type News type
     * @return array
     */
    public function getFeaturedNews(string $type): array
    {
        $lang = $this->lang;
        return $this->cache->get(
            "SELECT name$lang, slug$lang,photo,desc$lang from table_news 
             where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) 
             order by numb",
            [$type],
            'result',
            7200
        );
    }

    /**
     * Get featured products
     * 
     * @param string $type Product type
     * @param int $limit Limit results
     * @return array
     */
    public function getFeaturedProducts(string $type = 'san-pham', int $limit = 8): array
    {
        $lang = $this->lang;
        return $this->cache->get(
            "SELECT id,name$lang, slug$lang,photo,regular_price,sale_price from table_product 
             where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) 
             order by numb limit 0,$limit",
            [$type],
            'result',
            7200
        );
    }

    /**
     * Get screenshot
     * 
     * @return array|null
     */
    public function getScreenshot(): ?array
    {
        return $this->cache->get(
            "SELECT id, photo,options from table_photo where type = ?",
            ['screenshot'],
            'fetch',
            7200
        );
    }

    /**
     * Get video link
     * 
     * @return array|null
     */
    public function getVideoLink(): ?array
    {
        return $this->cache->get(
            "select id, photo, link_video from #_photo where type = ? and act = ? limit 0,1",
            ['video', 'photo_static'],
            'fetch',
            7200
        );
    }
}

