<?php

require __DIR__ . '/../Stubs/Repositories/ProductRepositoryStub.php';

use Tuezy\Application\Catalog\ListProductsByHierarchy;

$repo = new ProductRepositoryStub();
$useCase = new ListProductsByHierarchy($repo);

$result = $useCase->execute('san-pham', 'cat', 2, 1, 10);

echo is_array($result['items']) ? "PASS: ListProductsByHierarchy items\n" : "FAIL: ListProductsByHierarchy items\n";
echo $result['total'] === 2 ? "PASS: ListProductsByHierarchy total\n" : "FAIL: ListProductsByHierarchy total\n";

