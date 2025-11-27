<div class="section-main contact-page-modern">
    <div class="wrapper">
        <div class="container">
            <div class="content-main">
                <!-- Page Title -->
                <div class="contact-page-header mb-4">
                    <h1 class="contact-page-title">
                        <?= $titleMain ?? 'Liên hệ' ?>
                    </h1>
                    <p class="contact-page-subtitle text-muted">
                        Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn
                    </p>
                </div>

                <!-- Flash Messages -->
                <?= $flash->getMessages("frontend") ?>

                <!-- Contact Content & Form -->
                <div class="contact-wrapper-modern">
                    <div class="row g-4">
                        <!-- Contact Information -->
                        <div class="col-lg-5">
                            <div class="contact-info-modern">
                                <h3 class="contact-info-title mb-4">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin liên hệ
                                </h3>
                                <div class="contact-info-content">
                                    <?php if (!empty($lienhe['content' . $lang])) { ?>
                                        <?= htmlspecialchars_decode($lienhe['content' . $lang]) ?>
                                    <?php } else { ?>
                                        <p class="text-muted">Thông tin liên hệ đang được cập nhật...</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="col-lg-7">
                            <div class="contact-form-wrapper-modern">
                                <h3 class="contact-form-title mb-4">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                                </h3>
                                <form class="contact-form-modern validation-contact" novalidate method="post" action="" enctype="multipart/form-data">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label for="fullname-contact" class="form-label-modern">
                                                    <i class="fas fa-user me-2"></i><?= hoten ?> <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       class="form-control form-control-modern" 
                                                       id="fullname-contact" 
                                                       name="dataContact[fullname]" 
                                                       placeholder="<?= hoten ?>" 
                                                       value="<?= $flash->get('fullname') ?>" 
                                                       required />
                                                <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label for="phone-contact" class="form-label-modern">
                                                    <i class="fas fa-phone me-2"></i><?= sodienthoai ?> <span class="text-danger">*</span>
                                                </label>
                                                <input type="tel" 
                                                       class="form-control form-control-modern" 
                                                       id="phone-contact" 
                                                       name="dataContact[phone]" 
                                                       placeholder="<?= sodienthoai ?>" 
                                                       value="<?= $flash->get('phone') ?>" 
                                                       required />
                                                <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label for="address-contact" class="form-label-modern">
                                                    <i class="fas fa-map-marker-alt me-2"></i><?= diachi ?> <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       class="form-control form-control-modern" 
                                                       id="address-contact" 
                                                       name="dataContact[address]" 
                                                       placeholder="<?= diachi ?>" 
                                                       value="<?= $flash->get('address') ?>" 
                                                       required />
                                                <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label for="email-contact" class="form-label-modern">
                                                    <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" 
                                                       class="form-control form-control-modern" 
                                                       id="email-contact" 
                                                       name="dataContact[email]" 
                                                       placeholder="Email" 
                                                       value="<?= $flash->get('email') ?>" 
                                                       required />
                                                <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group-modern">
                                        <label for="subject-contact" class="form-label-modern">
                                            <i class="fas fa-tag me-2"></i><?= chude ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-modern" 
                                               id="subject-contact" 
                                               name="dataContact[subject]" 
                                               placeholder="<?= chude ?>" 
                                               value="<?= $flash->get('subject') ?>" 
                                               required />
                                        <div class="invalid-feedback"><?= vuilongnhapchude ?></div>
                                    </div>

                                    <div class="form-group-modern">
                                        <label for="content-contact" class="form-label-modern">
                                            <i class="fas fa-comment-alt me-2"></i><?= noidung ?> <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control form-control-modern" 
                                                  id="content-contact" 
                                                  name="dataContact[content]" 
                                                  rows="5" 
                                                  placeholder="<?= noidung ?>" 
                                                  required><?= $flash->get('content') ?></textarea>
                                        <div class="invalid-feedback"><?= vuilongnhapnoidung ?></div>
                                    </div>

                                    <div class="form-group-modern">
                                        <label for="file_attach" class="form-label-modern">
                                            <i class="fas fa-paperclip me-2"></i><?= dinhkemtaptin ?>
                                        </label>
                                        <div class="file-input-wrapper-modern">
                                            <input type="file" 
                                                   class="form-control form-control-modern" 
                                                   id="file_attach" 
                                                   name="file_attach">
                                            <small class="form-text text-muted">Chọn file đính kèm (nếu có)</small>
                                        </div>
                                    </div>

                                    <div class="form-actions-modern">
                                        <button type="submit" class="btn btn-primary-modern" name="submit-contact" disabled>
                                            <i class="fas fa-paper-plane me-2"></i><?= gui ?>
                                        </button>
                                        <button type="reset" class="btn btn-secondary-modern">
                                            <i class="fas fa-redo me-2"></i><?= nhaplai ?>
                                        </button>
                                    </div>

                                    <input type="hidden" name="recaptcha_response_contact" id="recaptchaResponseContact">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <?php if (!empty($optsetting['coords_iframe'])) { ?>
                    <div class="contact-map-modern mt-5">
                        <h3 class="contact-map-title mb-4">
                            <i class="fas fa-map-marked-alt me-2"></i>Bản đồ
                        </h3>
                        <div class="map-wrapper-modern">
                            <?= htmlspecialchars_decode($optsetting['coords_iframe']) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Contact Page Modern Styles */
.contact-page-modern {
    padding: 2rem 0;
    min-height: 60vh;
}

.contact-page-header {
    text-align: center;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e9ecef;
}

.contact-page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.contact-page-subtitle {
    font-size: 1.125rem;
}

/* Contact Wrapper */
.contact-wrapper-modern {
    margin-top: 2rem;
}

.contact-info-modern {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    height: 100%;
}

.contact-info-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.contact-info-content {
    font-size: 1rem;
    line-height: 1.8;
    color: #495057;
}

.contact-info-content h1,
.contact-info-content h2,
.contact-info-content h3 {
    color: #2c3e50;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.contact-info-content p {
    margin-bottom: 1rem;
}

.contact-info-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

/* Contact Form */
.contact-form-wrapper-modern {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.contact-form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.contact-form-modern .form-group-modern {
    margin-bottom: 1.5rem;
}

.form-label-modern {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control-modern {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
    outline: none;
}

.form-control-modern.is-invalid {
    border-color: #dc3545;
}

.file-input-wrapper-modern {
    position: relative;
}

.form-actions-modern {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-primary-modern {
    background: #dc3545;
    border: none;
    color: #fff;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.btn-primary-modern:hover:not(:disabled) {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-primary-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary-modern {
    background: #6c757d;
    border: none;
    color: #fff;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.btn-secondary-modern:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

/* Map Section */
.contact-map-modern {
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 2px solid #e9ecef;
}

.contact-map-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.map-wrapper-modern {
    background: #fff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.map-wrapper-modern iframe {
    width: 100%;
    height: 450px;
    border: none;
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 991.98px) {
    .contact-page-title {
        font-size: 2rem;
    }
    
    .contact-info-modern,
    .contact-form-wrapper-modern {
        padding: 1.5rem;
    }
}

@media (max-width: 575.98px) {
    .contact-page-modern {
        padding: 1rem 0;
    }
    
    .contact-page-title {
        font-size: 1.75rem;
    }
    
    .contact-info-modern,
    .contact-form-wrapper-modern {
        padding: 1rem;
    }
    
    .form-actions-modern {
        flex-direction: column;
    }
    
    .btn-primary-modern,
    .btn-secondary-modern {
        width: 100%;
        justify-content: center;
    }
    
    .map-wrapper-modern iframe {
        height: 300px;
    }
}
</style>