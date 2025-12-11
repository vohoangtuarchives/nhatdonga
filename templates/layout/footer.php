<div class="desktop-dknt-wrapper">
    <div class="container">
        <div class="desktop-dknt-wrap flex-wrap">

            <div class="desktop-dknt ">

                <form class="form-newsletter validation-newsletter" novalidate method="post" action="" enctype="multipart/form-data">
                    <div class="newsletter-row d-flex justify-content-center align-items-center">
                        <div class="dknt-wrap-info">
                            <h4 class="form-title">
                                ĐĂNG KÝ NHẬN TIN
                            </h4>
                        </div>
                            <div class="newsletter-input-group">
                                <div class="input_">
                                    <input name="dataNewsletter[email]" type="text" required id="email-newsletter" placeholder="Email:"/>
                                    <div class="invalid-feedback"><?=vuilongnhapdiachiemail?></div>
                                </div>
                        </div>
                        <div class="newsletter-button ">
                            <button type="submit"  id="submit-newsletter" name="submit-newsletter" value="ĐĂNG KÝ">
                                <span>
                                    GỬI
                                </span>
                            </button>
                        </div>

                    </div>
                    <input type="hidden" name="submit-newsletter" value="1">
                    <input type="hidden" name="recaptcha_response_newsletter" id="recaptchaResponseNewsletter">
                    <input type="hidden" name="type-newsletter" value="dangkynhantin">

                </form>
            </div>
        </div>
    </div>
</div>
<div id="footer" >
        <div class="wrapper ">
            <div class="row w-100 my-4">
                <div class="col-md-6 col-12">
                    <div >
                        <a href="/"><img src="assets/images/logo_footer.webp" alt="logo footer" class="img-fluid"/></a>
                        <h3 class="footer-company-title py-3 my-3">CÔNG TY TNHH ISF</h3>
                        <div>
                            <p><?=$optsetting["address"] ?? ''?></p>
                            <p>Hotline: <?=$optsetting["hotline"] ?? ''?></p>
                            <p>Email: <?=$optsetting["email"] ?? ''?></p>
                            <p>MST: <?=$optsetting["email"] ?? ''?></p>
                        </div>
                        <div class="my-4 text-center">
                            <img src="assets/images/bct.webp" alt="bct" height="133">
                        </div>
                        <div class="text-center">
                            <?php foreach ([
                                               [
                                                       'title' => 'facebook',
                                                        'link' => 'https://www.facebook.com/',
                                                        'icon' => 'fb',
                                               ],
                                               [
                                                       'title' => 'youtube',
                                                       'link' => '',
                                                       'icon' => 'yt',
                                               ],
                                               [
                                                       'title' => 'Tiktok',
                                                       'link' => 'https://www.facebook.com/',
                                                       'icon' => 'tik',
                                               ],
                                               [
                                                       'title' => 'Zalo',
                                                       'link' => 'https://www.facebook.com/',
                                                       'icon' => 'zalo',
                                               ],
                                           ]as $it): ?>
                                <span>
                                    <a href="<?=$it["link"]?>" class="ft-icon">
                                        <img src="assets/images/<?=$it["icon"]?>.webp" alt="icon" width="60" height="60">
                                    </a>
                                </span>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 col-12">
                                  <div class="row">
                                      <div class="col-md-6 col-12">
                                          <h3 class="footer-company-title pb-3 mb-3">SẢN PHẨM</h3>
                                          <ul class="menu-footer">
                                              <?php if (!empty($splist)) { foreach($splist as $v) { ?>
                                                  <li><a class="text-decoration-none" href="<?=!empty($v['slug'.$lang]) ? $v['slug'.$lang] : '#'?>" title="<?=!empty($v['name'.$lang]) ? $v['name'.$lang] : ''?>">
                                                          <?=!empty($v['name'.$lang]) ? $v['name'.$lang] : ''?></a></li>
                                              <?php } } ?>
                                          </ul>
                                      </div>

                                      <div class="col-md-6 col-12">
                                          <h3 class="footer-company-title  pb-3 mb-3">CHÍNH SÁCH</h3>
                                          <ul class="menu-footer">
                                              <?php if (!empty($chinhsach)) { foreach($chinhsach as $v) { ?>
                                                  <li>
                                                      <a class="text-decoration-none" href="<?=!empty($v['slug'.$lang]) ? $v['slug'.$lang] : '#'?>" title="<?=!empty($v['name'.$lang]) ? $v['name'.$lang] : ''?>">
                                                          <?=!empty($v['name'.$lang]) ? $v['name'.$lang] : ''?>
                                                      </a>
                                                  </li>
                                              <?php } } ?>
                                          </ul>
                                      </div>

                                      <div class="my-3 col-12">
                                            <?= renderMessagesFacebookFooter() ?>
                                      </div>
                                  </div>
            </div>
        </div>
    </div>
</div>
<div class="footer-powered">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <p>Copyright 2025 © All rights reserved. Design by <a href="#" class="underline">dahadu.com</a></p>
        </div>

    </div>
</div>
