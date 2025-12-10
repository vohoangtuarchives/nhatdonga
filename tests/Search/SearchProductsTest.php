<?php

require __DIR__ . '/../Stubs/Repositories/ProductRepositoryStub.php';

use Tuezy\Application\Search\SearchProducts;

$repo = new ProductRepositoryStub();
$useCase = new SearchProducts($repo);

$result = $useCase->execute('san-pham', 'enzyme', 1, 10);

echo is_array($result['items']) ? "PASS: SearchProducts items\n" : "FAIL: SearchProducts items\n";
echo $result['total'] === 2 ? "PASS: SearchProducts total\n" : "FAIL: SearchProducts total\n";

