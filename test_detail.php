<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/bootstrap/context.php';

use Tuezy\Helper\GlobalHelper;

echo "<h1>Debug: Chi Tiết Bài Viết</h1>";

try {
    $app = bootstrap_context('web');
    $d = GlobalHelper::db();
    
    // Test get news detail
    echo "<h2>Test 1: Get News by ID</h2>";
    $newsId = 253; // From log
    $news = $d->rawQueryOne("SELECT * FROM table_news WHERE id = ?", [$newsId]);
    
    if($news) {
        echo "<p>✓ Found news ID $newsId</p>";
        echo "<pre>";
        print_r($news);
        echo "</pre>";
    } else {
        echo "<p>✗ News ID $newsId not found</p>";
    }
    
    // Test get product detail
    echo "<h2>Test 2: Get Product by ID</h2>";
    $productId = 25; // From log
    $product = $d->rawQueryOne("SELECT * FROM table_product WHERE id = ?", [$productId]);
    
    if($product) {
        echo "<p>✓ Found product ID $productId</p>";
        echo "<pre>";
        print_r($product);
        echo "</pre>";
    } else {
        echo "<p>✗ Product ID $productId not found</p>";
    }
    
    // Test font loading
    echo "<h2>Test 3: Font Montserrat</h2>";
    echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">';
    echo '<p style="font-family: Montserrat, sans-serif; font-size: 24px;">This text should be in Montserrat font</p>';
    
} catch (Throwable $e) {
    echo "<h2 style='color:red'>Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
