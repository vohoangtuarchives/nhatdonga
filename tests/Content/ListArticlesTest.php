<?php

require __DIR__ . '/../Stubs/Repositories/ArticleRepositoryStub.php';

use Tuezy\Application\Content\ListArticles;

$repo = new ArticleRepositoryStub();
$useCase = new ListArticles($repo);

$result = $useCase->execute('tin-tuc', [], 1, 10);

echo is_array($result['items']) ? "PASS: ListArticles items\n" : "FAIL: ListArticles items\n";
echo $result['total'] === 3 ? "PASS: ListArticles total\n" : "FAIL: ListArticles total\n";

