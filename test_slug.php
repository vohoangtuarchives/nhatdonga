<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/bootstrap/context.php';

use Tuezy\Helper\GlobalHelper;

echo "<h1>Debug: Check Slug in Database</h1>";

try {
    $app = bootstrap_context('web');
    $d = GlobalHelper::db();
    
    $slug = 'enzyme-gluco-amylase-ga260-45745745745';
    
    echo "<h2>Test 1: Search by slug in product table</h2>";
    $product = $d->rawQueryOne("SELECT id, namevi, slugvi, type, status FROM table_product WHERE slugvi = ?", [$slug]);
    
    if($product) {
        echo "<p>✓ Found product with slug: $slug</p>";
        echo "<pre>";
        print_r($product);
        echo "</pre>";
    } else {
        echo "<p>✗ Product with slug '$slug' NOT FOUND</p>";
        
        // Try to find similar slugs
        echo "<h3>Similar slugs in database:</h3>";
        $similar = $d->rawQuery("SELECT id, namevi, slugvi, type FROM table_product WHERE slugvi LIKE ? LIMIT 10", ['%enzyme%']);
        if($similar) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Type</th></tr>";
            foreach($similar as $s) {
                echo "<tr>";
                echo "<td>{$s['id']}</td>";
                echo "<td>{$s['namevi']}</td>";
                echo "<td>{$s['slugvi']}</td>";
                echo "<td>{$s['type']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h2>Test 2: Check product ID 25</h2>";
    $product25 = $d->rawQueryOne("SELECT id, namevi, slugvi, type, status FROM table_product WHERE id = 25");
    if($product25) {
        echo "<p>✓ Found product ID 25</p>";
        echo "<pre>";
        print_r($product25);
        echo "</pre>";
        echo "<p><strong>Correct URL should be:</strong> http://donga.test/{$product25['slugvi']}</p>";
    }
    
} catch (Throwable $e) {
    echo "<h2 style='color:red'>Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
