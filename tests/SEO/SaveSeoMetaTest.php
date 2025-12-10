<?php

require __DIR__ . '/../Stubs/Repositories/SeoRepositoryStub.php';

use Tuezy\Application\SEO\SaveSeoMeta;

$repo = new SeoRepositoryStub();
$useCase = new SaveSeoMeta($repo);

$ok = $useCase->execute(1, 'product', 'man', 'san-pham', [
    'titlevi' => 'Tiêu đề',
    'descriptionvi' => 'Mô tả',
]);

echo $ok && $repo->savedCount === 1 ? "PASS: SaveSeoMeta\n" : "FAIL: SaveSeoMeta\n";

