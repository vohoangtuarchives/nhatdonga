<?php

namespace Tuezy\API\Controller;

use Tuezy\Service\ProductService;
use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;

/**
 * ColorAPIController - Handles color gallery API requests
 */
class ColorAPIController extends BaseAPIController
{
    private ProductService $productService;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);

        $productRepo = new ProductRepository($db, $cache, $lang, $sluglang, 'san-pham');
        $categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $tagsRepo = new TagsRepository($db, $cache, $lang, $sluglang);
        $this->productService = new ProductService($productRepo, $categoryRepo, $tagsRepo, $db, $lang);
    }

    /**
     * Get gallery by color
     * 
     * @return void Outputs HTML
     */
    public function getGalleryByColor(): void
    {
        $id_color = (int)$this->post('id_color', 0);
        $id_pro = (int)$this->post('id_pro', 0);

        if (!$id_color || !$id_pro) {
            return;
        }

        // Get gallery by color
        $rowDetailPhoto = $this->db->rawQuery(
            "SELECT photo, id_parent, id FROM #_gallery WHERE id_color = ? AND id_parent = ? AND com = ? AND type = ? AND kind = ? AND val = ?",
            [$id_color, $id_pro, 'product', 'san-pham', 'man', 'san-pham']
        );

        // Get product detail
        $rowDetailContext = $this->productService->getDetailContext($id_pro, 'san-pham', false);
        $rowDetail = $rowDetailContext['detail'] ?? null;

        if (!empty($rowDetailPhoto) && $rowDetail) { ?>
            <a id="Zoom-1" class="MagicZoom" data-options="zoomMode: off; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;" href="<?=ASSET.WATERMARK?>/product/540x540x1/<?=UPLOAD_PRODUCT_L.$rowDetailPhoto[0]['photo']?>" title="<?=$rowDetail['name'.$this->lang]?>">
                <?=$this->func->getImage(['isLazy' => false, 'sizes' => '540x540x1', 'isWatermark' => true, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetailPhoto[0]['photo'], 'alt' => $rowDetail['name'.$this->lang]])?>
            </a>
            
            <div class="gallery-thumb-pro">
                <div class="owl-page owl-carousel owl-theme owl-pro-detail"
                    data-xsm-items="5:10" 
                    data-sm-items="5:10" 
                    data-md-items="5:10" 
                    data-lg-items="5:10" 
                    data-xlg-items="5:10" 
                    data-nav="1" 
                    data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>" 
                    data-navcontainer=".control-pro-detail">
                    <?php foreach($rowDetailPhoto as $v) { ?>
                        <a class="thumb-pro-detail" data-zoom-id="Zoom-1" href="<?=ASSET.WATERMARK?>/product/540x540x1/<?=UPLOAD_PRODUCT_L.$v['photo']?>" title="<?=$rowDetail['name'.$this->lang]?>">
                            <?=$this->func->getImage(['isLazy' => false, 'sizes' => '540x540x1', 'isWatermark' => true, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $v['photo'], 'alt' => $rowDetail['name'.$this->lang]])?>
                        </a>
                    <?php } ?>
                </div>
                <div class="control-pro-detail control-owl transition"></div>
            </div>
        <?php }
        exit;
    }
}

