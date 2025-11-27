<div class="section-main news-detail-modern">
    <div class="wrapper">
        <div class="container">
            <div class="content-main">
                <?php if (!empty($rowDetail)) { ?>
                    <!-- News Header -->
                    <div class="news-detail-header mb-4">
                        <h1 class="news-detail-title">
                            <?= !empty($rowDetail['name' . $lang]) ? htmlspecialchars($rowDetail['name' . $lang]) : '' ?>
                        </h1>
                        <div class="news-meta-modern">
                            <div class="news-meta-item">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <span><?= ngaydang ?>: <?= date("d/m/Y h:i A", $rowDetail['date_created']) ?></span>
                            </div>
                            <?php if (!empty($rowDetail['view'])) { ?>
                                <div class="news-meta-item">
                                    <i class="fas fa-eye me-2"></i>
                                    <span><?= number_format($rowDetail['view']) ?> lượt xem</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <?php if (!empty($rowDetail['photo'])) { ?>
                        <div class="news-featured-image mb-4">
                            <?= $func->getImage([
                                'sizes' => '1200x675x1',
                                'isWatermark' => false,
                                'prefix' => 'news',
                                'upload' => UPLOAD_NEWS_L,
                                'image' => $rowDetail['photo'],
                                'alt' => $rowDetail['name' . $lang],
                                'class' => 'img-fluid rounded-3 shadow-sm w-100'
                            ]) ?>
                        </div>
                    <?php } ?>

                    <!-- Table of Contents (if enabled) -->
                    <?php if (!empty($rowDetail['content' . $lang]) && strpos($rowDetail['content' . $lang], '<h') !== false) { ?>
                        <div class="meta-toc-modern mb-4">
                            <div class="toc-wrapper">
                                <div class="toc-header">
                                    <i class="fas fa-list me-2"></i>
                                    <strong>Mục lục</strong>
                                </div>
                                <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Main Content -->
                    <div class="news-content-wrapper">
                        <article class="news-content-modern">
                            <?php if (!empty($rowDetail['content' . $lang])) { ?>
                                <?= htmlspecialchars_decode($rowDetail['content' . $lang]) ?>
                            <?php } else { ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <?= noidungdangcapnhat ?>
                                </div>
                            <?php } ?>
                        </article>

                        <!-- Social Share -->
                        <div class="news-share-modern mt-4">
                            <?php include TEMPLATE . LAYOUT . "share-social.php"; ?>
                        </div>
                    </div>

                    <!-- Related News -->
                    <?php if (!empty($otherNewss)) { ?>
                        <div class="related-news-modern mt-5">
                            <h3 class="related-news-title mb-4">
                                <i class="fas fa-newspaper me-2"></i><?= baivietkhac ?>
                            </h3>
                            <div class="related-news-list">
                                <?php foreach ($otherNewss as $k => $v) { ?>
                                    <div class="related-news-item">
                                        <a href="<?= $v[$sluglang] ?>" class="related-news-link" title="<?= htmlspecialchars($v['name' . $lang]) ?>">
                                            <i class="fas fa-chevron-right me-2"></i>
                                            <span class="related-news-name"><?= htmlspecialchars($v['name' . $lang]) ?></span>
                                            <span class="related-news-date"><?= date("d/m/Y", $v['date_created']) ?></span>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($paging)) { ?>
                                <div class="pagination-home w-100 mt-4"><?= $paging ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                <?php } else { ?>
                    <!-- Empty State -->
                    <div class="news-empty-state">
                        <div class="empty-state-content">
                            <i class="fas fa-newspaper fa-4x mb-3 text-muted"></i>
                            <h3 class="empty-state-title">Bài viết không tồn tại</h3>
                            <p class="empty-state-message text-muted">
                                Bài viết bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
/* News Detail Modern Styles */
.news-detail-modern {
    padding: 2rem 0;
    min-height: 60vh;
}

.news-detail-header {
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e9ecef;
}

.news-detail-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.3;
    margin: 0 0 1rem 0;
}

.news-meta-modern {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
    color: #6c757d;
    font-size: 0.95rem;
}

.news-meta-item {
    display: flex;
    align-items: center;
}

.news-meta-item i {
    color: #dc3545;
}

.news-featured-image {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.news-featured-image img {
    width: 100%;
    height: auto;
    display: block;
}

/* Table of Contents */
.meta-toc-modern {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
}

.toc-wrapper {
    position: relative;
}

.toc-header {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #dee2e6;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-list li {
    margin-bottom: 0.5rem;
}

.toc-list a {
    color: #495057;
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    color: #dc3545;
    background: #fff;
    padding-left: 1rem;
}

/* Content Wrapper */
.news-content-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.news-content-modern {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: #495057;
}

.news-content-modern h1,
.news-content-modern h2,
.news-content-modern h3,
.news-content-modern h4,
.news-content-modern h5,
.news-content-modern h6 {
    color: #2c3e50;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.news-content-modern h1 {
    font-size: 2rem;
    border-bottom: 3px solid #dc3545;
    padding-bottom: 0.5rem;
}

.news-content-modern h2 {
    font-size: 1.75rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.news-content-modern h3 {
    font-size: 1.5rem;
}

.news-content-modern p {
    margin-bottom: 1.25rem;
}

.news-content-modern img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.news-content-modern ul,
.news-content-modern ol {
    margin-bottom: 1.25rem;
    padding-left: 2rem;
}

.news-content-modern li {
    margin-bottom: 0.5rem;
}

.news-content-modern blockquote {
    border-left: 4px solid #dc3545;
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-radius: 6px;
}

.news-content-modern a {
    color: #dc3545;
    text-decoration: none;
    transition: color 0.3s ease;
}

.news-content-modern a:hover {
    color: #c82333;
    text-decoration: underline;
}

/* Share Section */
.news-share-modern {
    padding-top: 2rem;
    border-top: 2px solid #e9ecef;
    margin-top: 2rem;
}

/* Related News */
.related-news-modern {
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 2px solid #e9ecef;
}

.related-news-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.related-news-list {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.related-news-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.related-news-item:last-child {
    border-bottom: none;
}

.related-news-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 6px;
}

.related-news-link:hover {
    background: #f8f9fa;
    color: #dc3545;
    padding-left: 1rem;
}

.related-news-link i {
    color: #dc3545;
    font-size: 0.875rem;
}

.related-news-name {
    flex-grow: 1;
    margin-left: 0.5rem;
}

.related-news-date {
    color: #6c757d;
    font-size: 0.875rem;
    white-space: nowrap;
}

/* Empty State */
.news-empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state-content {
    max-width: 500px;
    margin: 0 auto;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.empty-state-message {
    font-size: 1rem;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 991.98px) {
    .news-detail-title {
        font-size: 1.75rem;
    }
    
    .news-content-wrapper {
        padding: 1.5rem;
    }
    
    .news-content-modern {
        font-size: 1rem;
    }
}

@media (max-width: 575.98px) {
    .news-detail-modern {
        padding: 1rem 0;
    }
    
    .news-detail-title {
        font-size: 1.5rem;
    }
    
    .news-meta-modern {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .news-content-wrapper {
        padding: 1rem;
    }
    
    .related-news-link {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .related-news-date {
        margin-top: 0.5rem;
        margin-left: 1.5rem;
    }
}
</style>