<div class="share-modern">

    <span class="share-label">
        <i class="fas fa-share-alt me-2"></i><?= chiase ?>:
    </span>

    <div class="social-plugin-modern">
        <?php
        $params = array();
        $params['oaid'] = $optsetting['oaidzalo'] ?? '';
        $params['url'] = $func->getCurrentPageURL();
        $params['title'] = !empty($rowDetail) ? ($rowDetail['name' . $lang] ?? '') : ($seo->get('title') ?? '');
        $params['description'] = !empty($rowDetail) ? ($rowDetail['desc' . $lang] ?? '') : ($seo->get('description') ?? '');
        $params['image'] = !empty($rowDetail) ? (ASSET . THUMBS . '/614x530x2/' . UPLOAD_PRODUCT_L . $rowDetail['photo']) : '';
        echo $func->markdown('social/share', $params);
        ?>
    </div>

</div>