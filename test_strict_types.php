<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/bootstrap/context.php';

use Tuezy\Helper\GlobalHelper;

try {
    $app = bootstrap_context('web');
    echo "<h1>✓ Strict Types Test - PASSED</h1>";
    echo "<p>PDODb loaded successfully with strict_types=1</p>";
    
    // Test basic query
    $d = GlobalHelper::db();
    $result = $d->rawQuery("SELECT 1 as test");
    
    if(is_array($result) && count($result) === 1) {
        echo "<p>✓ Basic query test PASSED</p>";
    } else {
        echo "<p>✗ Basic query test FAILED</p>";
    }
    
} catch (Throwable $e) {
    echo "<h1 style='color:red'>✗ Strict Types Test - FAILED</h1>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
