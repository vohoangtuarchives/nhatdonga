<?php

require __DIR__ . '/../Stubs/Repositories/ArticleRepositoryStub.php';

use Tuezy\Application\Search\SearchArticles;

$repo = new ArticleRepositoryStub();
$useCase = new SearchArticles($repo);

$result = $useCase->execute('tin-tuc', 'policy', 1, 10);

echo is_array($result['items']) ? "PASS: SearchArticles items\n" : "FAIL: SearchArticles items\n";
echo $result['total'] === 3 ? "PASS: SearchArticles total\n" : "FAIL: SearchArticles total\n";

