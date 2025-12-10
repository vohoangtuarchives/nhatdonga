<?php

require __DIR__ . '/../Stubs/Repositories/ProductRepositoryStub.php';

use Tuezy\Application\Catalog\ListProducts;

$repo = new ProductRepositoryStub();
$useCase = new ListProducts($repo);

$result = $useCase->execute('san-pham', [], 1, 10);

echo is_array($result['items']) ? "PASS: ListProducts items\n" : "FAIL: ListProducts items\n";
echo $result['total'] === 2 ? "PASS: ListProducts total\n" : "FAIL: ListProducts total\n";

