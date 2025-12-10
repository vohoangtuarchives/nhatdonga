<div class="section-main static-page-modern">
    <div class="wrapper">
        <div class="container">
            <div class="content-main">

            
                <?php if (!empty($static)) { ?>
                    <!-- Breadcrumbs -->
                    <!-- Page Title -->
                    <div class="static-page-header mb-4">
                    <div class="section-header-isp text-center">
            <h1 class="section-title-isp"><?= !empty($static['name' . $lang]) ? htmlspecialchars($static['name' . $lang]) :$titleMain ?></h1>
        </div>          
                    
                    </div>

                    <!-- Table of Contents (if enabled) -->
                    <?php if (!empty($static['content' . $lang]) && strpos($static['content' . $lang], '<h') !== false) { ?>
                        <div class="meta-toc-modern mb-4">
                            <div class="toc-wrapper">
                                <div class="toc-header">
                                    <i class="fas fa-list me-2"></i>
                                    <strong><?= mucluc ?></strong>
                                </div>
                                <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Main Content -->
                    <div class="static-content-wrapper">
                        <article class="static-content-modern">
                            <?php if (!empty($static['content' . $lang])) { ?>
                                <?= htmlspecialchars_decode($static['content' . $lang]) ?>
                            <?php } else { ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Nội dung đang được cập nhật...
                                </div>
                            <?php } ?>
                        </article>

                        <!-- Social Share -->
                        <div class="static-share-modern mt-4">
                            <?php include TEMPLATE . LAYOUT . "share-social.php"; ?>
                        </div>
                    </div>

                <?php } else { ?>
                    <!-- Empty State -->
                    <div class="static-empty-state">
                        <div class="empty-state-content">
                            <i class="fas fa-file-alt fa-4x mb-3 text-muted"></i>
                            <h3 class="empty-state-title"><?= dangcapnhatdulieu ?></h3>
                            <p class="empty-state-message text-muted">
                                Trang này đang được cập nhật. Vui lòng quay lại sau.
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Static Page Modern Styles */
.static-page-modern {
    padding: 2rem 0;
    min-height: 60vh;
}

.static-page-header {
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.static-page-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.3;
    margin: 0;
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
.static-content-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.static-content-modern {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: #495057;
}

.static-content-modern h1,
.static-content-modern h2,
.static-content-modern h3,
.static-content-modern h4,
.static-content-modern h5,
.static-content-modern h6 {
    color: #2c3e50;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.static-content-modern h1 {
    font-size: 2rem;
    border-bottom: 3px solid #dc3545;
    padding-bottom: 0.5rem;
}

.static-content-modern h2 {
    font-size: 1.75rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.static-content-modern h3 {
    font-size: 1.5rem;
}

.static-content-modern h4 {
    font-size: 1.25rem;
}

.static-content-modern p {
    margin-bottom: 1.25rem;
}

.static-content-modern img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.static-content-modern ul,
.static-content-modern ol {
    margin-bottom: 1.25rem;
    padding-left: 2rem;
}

.static-content-modern li {
    margin-bottom: 0.5rem;
}

.static-content-modern blockquote {
    border-left: 4px solid #dc3545;
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-radius: 6px;
}

.static-content-modern table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    overflow: hidden;
}

.static-content-modern table th,
.static-content-modern table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.static-content-modern table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.static-content-modern table tr:hover {
    background: #f8f9fa;
}

.static-content-modern a {
    color: #dc3545;
    text-decoration: none;
    transition: color 0.3s ease;
}

.static-content-modern a:hover {
    color: #c82333;
    text-decoration: underline;
}

.static-content-modern code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9em;
    color: #e83e8c;
}

.static-content-modern pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 1.5rem 0;
    border: 1px solid #e9ecef;
}

.static-content-modern pre code {
    background: none;
    padding: 0;
    color: #495057;
}

/* Share Section */
.static-share-modern {
    padding-top: 2rem;
    border-top: 2px solid #e9ecef;
    margin-top: 2rem;
}

/* Empty State */
.static-empty-state {
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
    .static-page-title {
        font-size: 1.75rem;
    }
    
    .static-content-wrapper {
        padding: 1.5rem;
    }
    
    .static-content-modern {
        font-size: 1rem;
    }
    
    .static-content-modern h1 {
        font-size: 1.75rem;
    }
    
    .static-content-modern h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 575.98px) {
    .static-page-modern {
        padding: 1rem 0;
    }
    
    .static-page-title {
        font-size: 1.5rem;
    }
    
    .static-content-wrapper {
        padding: 1rem;
    }
    
    .meta-toc-modern {
        padding: 1rem;
    }
    
    .static-content-modern h1 {
        font-size: 1.5rem;
    }
    
    .static-content-modern h2 {
        font-size: 1.25rem;
    }
    
    .static-content-modern h3 {
        font-size: 1.125rem;
    }
}
</style>