<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'context.php';

$app = bootstrap_context('web', [
    'sources' => __DIR__ . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
    'templates' => __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
    'watermark' => 'watermark',
]);

/* Router */
require_once LIBRARIES . "router.php";

/* Template */
include TEMPLATE . "index.php";