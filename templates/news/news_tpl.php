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
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="featured-news-item card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                                <div class="row g-0 align-items-stretch">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="featured-news-image h-100">
                                            <a href="<?= $firstNews['slug' . $lang] ?>" title="<?= htmlspecialchars($firstNews['name' . $lang]) ?>" class="d-block h-100">
                                                <?php
                                                $image = $func->getImage([
                                                    'class' => 'img-fluid w-100 h-100',
                                                    'style' => 'object-fit: cover;',
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
                                        <div class="featured-news-content p-4 p-lg-5 h-100 d-flex flex-column">
                                            <div class="news-date mb-3 text-muted">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                <span><?= date("d/m/Y", $firstNews['date_created']) ?></span>
                                            </div>
                                            <h2 class="featured-news-title mb-3 flex-grow-0">
                                                <a href="<?= $firstNews['slug' . $lang] ?>" title="<?= htmlspecialchars($firstNews['name' . $lang]) ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($firstNews['name' . $lang]) ?>
                                                </a>
                                            </h2>
                                            <?php if (!empty($firstNews['desc' . $lang])): ?>
                                            <div class="featured-news-desc mb-4 text-muted flex-grow-1">
                                                <?= htmlspecialchars_decode($firstNews['desc' . $lang]) ?>
                                            </div>
                                            <?php endif; ?>
                                            <div class="mt-auto">
                                                <a href="<?= $firstNews['slug' . $lang] ?>" class="btn-read-more btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center">
                                                    Đọc thêm <i class="fas fa-arrow-right ms-2"></i>
                                                </a>
                                            </div>
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
                                <div class="news-card-wrapper h-100">
                                    <?php echo $custom->news($v, "col-news-item", true); ?>
                                </div>
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