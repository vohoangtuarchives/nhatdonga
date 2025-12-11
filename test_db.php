<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/bootstrap/context.php';

// Bootstrap app to get DB connection
try {
    $app = bootstrap_context('web', [
        'sources' => __DIR__ . '/sources/',
        'templates' => __DIR__ . '/templates/',
        'watermark' => 'watermark',
    ]);
} catch (Throwable $e) {
    die("Bootstrap Error: " . $e->getMessage());
}

use Tuezy\Helper\GlobalHelper;

try {
    $d = GlobalHelper::db();
} catch (Throwable $e) {
    die("DB Connection Error: " . $e->getMessage());
}

echo "<h1>Database Content Inspection</h1>";

// 1. Check what tables exist matching 'product'
echo "<h2>1. Search for Tables</h2>";
$tables = $d->rawQuery("SHOW TABLES LIKE '%product%'");
echo "<pre>"; print_r($tables); echo "</pre>";

// Debug: Check PDO connection
echo "<h2>1.5 PDO Connection Info</h2>";
try {
    $pdo = $d->pdo();
    echo "<p>PDO instance exists: " . (($pdo instanceof PDO) ? 'YES' : 'NO') . "</p>";
    if($pdo instanceof PDO) {
        // Try a simple query
        $testStmt = $pdo->query("SELECT DATABASE() as db");
        $dbInfo = $testStmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Connected to database: " . ($dbInfo['db'] ?? 'UNKNOWN') . "</p>";
    }
} catch (Exception $e) {
    echo "<p>Error getting PDO: " . $e->getMessage() . "</p>";
}

// 2. Check content of table_product (raw, no filters)
echo "<h2>2. Raw Content of table_product (Limit 5)</h2>";

// First test with simplest possible query
echo "<h3>2a. Test with SELECT 1</h3>";
$simpleTest = $d->rawQuery("SELECT 1 as test");
echo "<p>SELECT 1 result type: " . gettype($simpleTest) . "</p>";
if(is_array($simpleTest)) {
    echo "<p>Count: " . count($simpleTest) . "</p>";
    echo "<pre>"; print_r($simpleTest); echo "</pre>";
}

echo "<h3>2b. Test with table_product</h3>";
echo "<p>Debug: Executing query...</p>";
$products = $d->rawQuery("SELECT id, namevi, type, status FROM table_product LIMIT 5");
echo "<p>Debug: Query executed. Result type: " . gettype($products) . "</p>";
if(is_array($products)) {
    echo "<p>Debug: Array count: " . count($products) . "</p>";
}
if ($products) {
    echo "<pre>"; print_r($products); echo "</pre>";
} else {
    echo "<p>No result from table_product. Error: </p>";
    $error = $d->getLastError();
    if(is_array($error)) {
         echo "<pre>Code: " . ($error[0] ?? '') . "\nMessage: " . ($error[2] ?? '') . "</pre>";
    } else {
         var_dump($error);
    }
}

// 3. Count products by type and status
echo "<h2>3. Counts by Type</h2>";
$counts = $d->rawQuery("SELECT type, COUNT(*) as total FROM table_product GROUP BY type");
if ($counts) {
    echo "<table border='1' cellpadding='5'><tr><th>Type</th><th>Total</th></tr>";
    foreach ($counts as $row) {
        echo "<tr><td>{$row['type']}</td><td>{$row['total']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "Could not group by type.";
}

// 4. Check specific 'san-pham' items and their status
echo "<h2>4. Check 'san-pham' Status</h2>";
$items = $d->rawQuery("SELECT id, namevi, status FROM table_product WHERE type = 'san-pham' LIMIT 10");
if ($items) {
    echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Status</th></tr>";
    foreach ($items as $item) {
        echo "<tr><td>{$item['id']}</td><td>{$item['namevi']}</td><td>{$item['status']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No 'san-pham' items found.";
}
