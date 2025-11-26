<?php
// Set default breadcrumbs if not set
if (!isset($breadcrumbs)) {
    global $breadcr, $configBase;
    if (isset($breadcr) && is_object($breadcr)) {
        $breadcrumbs = $breadcr->get($configBase ?? '');
    } else {
        $breadcrumbs = '';
    }
}
?>
<div class="breadCrumbs"><div class="wrapper"><?=$breadcrumbs?></div></div>