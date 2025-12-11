<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct PDO Test</h1>";

// Test PDO connection directly
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=cuuho;port=3306;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p>✓ PDO Connection successful</p>";
    
    // Test simple query
    $stmt = $pdo->prepare("SELECT id, namevi, type, status FROM table_product LIMIT 5");
    echo "<p>✓ Statement prepared</p>";
    
    $stmt->execute();
    echo "<p>✓ Statement executed</p>";
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✓ fetchAll completed. Type: " . gettype($result) . ", Count: " . count($result) . "</p>";
    
    if($result) {
        echo "<h2>Results:</h2>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "<p>No results (but no error)</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ PDO Error: " . $e->getMessage() . "</p>";
}
