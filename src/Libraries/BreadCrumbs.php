<?php
namespace Tuezy\Libraries;

class BreadCrumbs
{
    private $d;
    private $data = array();

    function __construct($d)
    {
        $this->d = $d;
    }

    public function set($slug='', $name='')
    {
        if($name != '')
        {
            $this->data[] = array('slug' => $slug, 'name' => $name);
        }
    }

    public function get($configBase = null)
    {
        if ($configBase === null) {
            if (function_exists('Tuezy\config')) {
                $config = \Tuezy\config();
                $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
                $configUrl = $config['database']['server-name'] . $config['database']['url'];
                $configBase = $http . $configUrl;
            } else {
                global $configBase;
            }
        }
        $json = array();
        $breadcumb = '';
        if($this->data)
        {
            $breadcumb .= '<ol class="breadcrumb">';
            $breadcumb .= '<li class="breadcrumb-item"><a class="text-decoration-none" href="'.$configBase.'"><span>'.\trangchu.'</span></a></li>';
            $k = 1;
            foreach($this->data as $key => $value)
            {
                if($value['name'] != '')
                {
                    $slug = ($value['slug']) ? $configBase.$value['slug'] : '';
                    $name = $value['name'];
                    $active = ($key == count($this->data) - 1) ? "active" : "";
                    $breadcumb .= '<li class="breadcrumb-item '.$active.'"><a class="text-decoration-none" href="'.$slug.'"><span>'.$name.'</span></a></li>';
                    $json[] = array("@type"=>"ListItem","position"=>$k,"name"=>$name,"item"=>$slug);
                    $k++;
                }
            }
            $breadcumb .= '</ol>';
            $breadcumb .= '<script type="application/ld+json">{"@context": "https://schema.org","@type": "BreadcrumbList","itemListElement": '.((json_encode($json))).'}</script>';
        }
        return $breadcumb;
    }
}

