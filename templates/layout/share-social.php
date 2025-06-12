<div class="share">
    <b><?= chiase ?>:</b>
    <div class="social-plugin w-clear">
        <?php
        $params = array();
        $params['oaid'] = $optsetting['oaidzalo'];
        echo $func->markdown('social/share', $params);
        ?>
    </div>
</div>