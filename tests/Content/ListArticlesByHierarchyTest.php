<?php

require __DIR__ . '/../Stubs/Repositories/ArticleRepositoryStub.php';

use Tuezy\Application\Content\ListArticlesByHierarchy;

$repo = new ArticleRepositoryStub();
$useCase = new ListArticlesByHierarchy($repo);

$result = $useCase->execute('tin-tuc', 'list', 1, 1, 10);

echo is_array($result['items']) ? "PASS: ListArticlesByHierarchy items\n" : "FAIL: ListArticlesByHierarchy items\n";
echo $result['total'] === 3 ? "PASS: ListArticlesByHierarchy total\n" : "FAIL: ListArticlesByHierarchy total\n";

