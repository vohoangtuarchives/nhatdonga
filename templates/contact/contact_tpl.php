<div class="section-main">

    <div class="wrapper">

        <div class="content-main">

            <?= $custom->titleContainer($titleMain) ?>

            <?= $flash->getMessages("frontend") ?>

            <div class="contact-article row">

                <div class="contact-text col-lg-6"><?= !empty($lienhe['content' . $lang]) ? htmlspecialchars_decode($lienhe['content' . $lang]) : '' ?></div>

                <form class="contact-form validation-contact col-lg-6" novalidate method="post" action="" enctype="multipart/form-data">

                    <div class="row">

                        <div class="contact-input col-sm-6">

                            <input type="text" class="form-control text-sm" id="fullname-contact" name="dataContact[fullname]" placeholder="<?= hoten ?>" value="<?= $flash->get('fullname') ?>" required />

                            <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>

                        </div>

                        <div class="contact-input col-sm-6">

                            <input type="number" class="form-control text-sm" id="phone-contact" name="dataContact[phone]" placeholder="<?= sodienthoai ?>" value="<?= $flash->get('phone') ?>" required />

                            <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="contact-input col-sm-6">

                            <input type="text" class="form-control text-sm" id="address-contact" name="dataContact[address]" placeholder="<?= diachi ?>" value="<?= $flash->get('address') ?>" required />

                            <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>

                        </div>

                        <div class="contact-input col-sm-6">

                            <input type="email" class="form-control text-sm" id="email-contact" name="dataContact[email]" placeholder="Email" value="<?= $flash->get('email') ?>" required />

                            <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>

                        </div>

                    </div>

                    <div class="contact-input">

                        <input type="text" class="form-control text-sm" id="subject-contact" name="dataContact[subject]" placeholder="<?= chude ?>" value="<?= $flash->get('subject') ?>" required />

                        <div class="invalid-feedback"><?= vuilongnhapchude ?></div>

                    </div>

                    <div class="contact-input">

                        <textarea class="form-control text-sm" id="content-contact" name="dataContact[content]" placeholder="<?= noidung ?>" required /><?= $flash->get('content') ?></textarea>

                        <div class="invalid-feedback"><?= vuilongnhapnoidung ?></div>

                    </div>

                    <div class="contact-input">

                        <input type="file" class="custom-file-input" name="file_attach">

                        <label class="custom-file-label" for="file_attach" title="<?= chon ?>"><?= dinhkemtaptin ?></label>

                    </div>

                    <input type="submit" class="btn btn-primary mr-2" name="submit-contact" value="<?= gui ?>" disabled />

                    <input type="reset" class="btn btn-secondary" value="<?= nhaplai ?>" />

                    <input type="hidden" name="recaptcha_response_contact" id="recaptchaResponseContact">

                </form>

            </div>

            <div class="contact-map"><?= htmlspecialchars_decode($optsetting['coords_iframe']) ?></div>

        </div>

    </div>

</div>