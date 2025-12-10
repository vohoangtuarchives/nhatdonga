<?php

function run($file) {
    echo "Running: {$file}\n";
    include $file;
}

$base = __DIR__;
run($base . '/Newsletter/SubscribeNewsletterTest.php');
run($base . '/SEO/SaveSeoMetaTest.php');
run($base . '/Catalog/ListProductsTest.php');
run($base . '/Content/ListArticlesTest.php');
run($base . '/Catalog/ListProductsByHierarchyTest.php');
run($base . '/Content/ListArticlesByHierarchyTest.php');
run($base . '/Search/SearchProductsTest.php');
run($base . '/Search/SearchArticlesTest.php');

echo "Done.\n";
