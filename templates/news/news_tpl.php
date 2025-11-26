<div class="section-main section-news-listing">

    <div class="wrapper">

        <div class="content-main">

            <?php if (!empty($news)) { ?>

                <?= $custom->titleContainer($titleMain) ?>

                <div class="news-listing-container">
                    
                    <?php 
                    // Hiển thị tin đầu tiên với layout lớn (nếu có)
                    $firstNews = !empty($news[0]) ? $news[0] : null;
                    $remainingNews = array_slice($news, 1);
                    ?>
                    
                    <?php if ($firstNews): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="featured-news-item">
                                <div class="row g-3 align-items-center">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="featured-news-image">
                                            <a href="<?= $firstNews['slug' . $lang] ?>" title="<?= htmlspecialchars($firstNews['name' . $lang]) ?>">
                                                <?php
                                                $image = $func->getImage([
                                                    'class' => 'img-fluid w-100',
                                                    'sizes' => '640x425x2',
                                                    'isWatermark' => false,
                                                    'prefix' => 'news',
                                                    'upload' => UPLOAD_NEWS_L,
                                                    'image' => $firstNews['photo'],
                                                    'alt' => $firstNews['name' . $lang]
                                                ]);
                                                echo $image;
                                                ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="featured-news-content">
                                            <div class="news-date mb-2">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                <span><?= date("d/m/Y", $firstNews['date_created']) ?></span>
                                            </div>
                                            <h2 class="featured-news-title mb-3">
                                                <a href="<?= $firstNews['slug' . $lang] ?>" title="<?= htmlspecialchars($firstNews['name' . $lang]) ?>">
                                                    <?= htmlspecialchars($firstNews['name' . $lang]) ?>
                                                </a>
                                            </h2>
                                            <?php if (!empty($firstNews['desc' . $lang])): ?>
                                            <div class="featured-news-desc mb-3">
                                                <?= htmlspecialchars_decode($firstNews['desc' . $lang]) ?>
                                            </div>
                                            <?php endif; ?>
                                            <a href="<?= $firstNews['slug' . $lang] ?>" class="btn-read-more">
                                                Đọc thêm <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($remainingNews)): ?>
                    <div class="row row-news g-4">
                        <?php foreach ($remainingNews as $k => $v): ?>
                            <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                <?php echo $custom->news($v, "col-news-item", true); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>

            <?php } else { ?>

                <div class="alert alert-warning w-100 text-center py-5" role="alert">
                    <i class="fas fa-info-circle fa-3x mb-3 d-block"></i>
                    <strong><?= khongtimthayketqua ?></strong>
                    <p class="mt-2 mb-0">Vui lòng quay lại sau hoặc thử tìm kiếm với từ khóa khác.</p>
                </div>

            <?php } ?>

            <?php if (!empty($paging)): ?>
            <div class="w-100 mt-5">
                <div class="pagination-home w-100 text-center"><?= $paging ?></div>
            </div>
            <?php endif; ?>

        </div>

    </div>

</div>