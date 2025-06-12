<div class="dangkynhantin py-5" >

    <div>

    <div class="close-dknt"><span></span></div>

    <div class="wrapper">

        <div class="title-form">

        <div class="title-main reverse">

            <h2 class="text-uppercase">

                Đặt lịch tư vấn

                <span class="decoration"></span>

            </h2>

            <p class="title-main__desc">

                <?=$txtDKNT["noidungvi"] ?? ''?>

                </p>



        </div>



        </div>

        <form class="form-newsletter validation-newsletter" novalidate method="post" action="" enctype="multipart/form-data">

            <div class="newsletter-row row">

                <div class="col-6 newsletter-input-group">

                    <span class="icon name"><i class="fa fa-user me-2"></i>Họ và tên:</span>

                    <div class="input_">

                        <input name="dataNewsletter[name]" type="text" required id="ten-newsletter" placeholder="Họ và tên:"/>

                        <div class="invalid-feedback"><?=vuilongnhaphoten?></div>

                    </div>

                </div>

                <div class="col-6 newsletter-input-group">

                    <span class="icon services"><i class="fa fa-edit me-2"></i>Tư vấn về</span>

                    <div class="input_">

                        <input name="dataNewsletter[services]" type="text" required id="content-newsletter" placeholder="Dịch vụ:"/>

                    </div>

                </div>

                

                <div class="col-6 newsletter-input-group">

                    <span class="icon phone"><i class="fa fa-phone me-2"></i>Số điện thoại:</span>

                    <div class="input_">

                        <input name="dataNewsletter[phone]" type="text" required id="diachi-newsletter" placeholder="Số điện thoại:"/>

                        <div class="invalid-feedback"><?=vuilongnhapsodienthoai?></div>

                    </div>

                </div>

                <div class="col-6 newsletter-input-group">

                    <span class="icon time"><i class="fa fa-edit me-2"></i>Thời gian</span>

                    <div class="input_">

                        <input name="dataNewsletter[time]" type="date" required id="content-newsletter" placeholder="Thời gian:"/>

                    </div>

                </div>



                <div class="col-6 newsletter-input-group">

                    <span class="icon email"><i class="fa fa-envelope me-2"></i>Email:</span>

                    <div class="input_">

                        <input name="dataNewsletter[email]" type="text" required id="email-newsletter" placeholder="Email:"/>

                        <div class="invalid-feedback"><?=vuilongnhapdiachiemail?></div>

                    </div>

                </div>

                <div class="col-6 newsletter-input-group">

                    <span class="icon noidung"><i class="fa fa-edit me-2"></i>Nội dung</span>

                    <div class="input_">

                        <input name="dataNewsletter[content]" type="text" required id="content-newsletter" placeholder="Nội dung:"/>

                    </div>

                </div>



                

                <div class="col-12">

                    <div class="newsletter-button">

                        <button type="submit"  id="submit-newsletter" class="me-3" name="submit-newsletter" value="ĐĂNG KÝ"><i class="fa fa-paper-plane me-2"></i>Đăng ký</button>

                        <div id="reset-newsletter" name="submit-newsletter"><i class="fa fa-paper-plane me-2"></i>Nhập Lại</div>

                    </div>

                </div>

            </div>

            <input type="hidden" name="submit-newsletter" value="1">

            <input type="hidden" name="recaptcha_response_newsletter" id="recaptchaResponseNewsletter">

            <input type="hidden" name="type-newsletter" value="dangkynhantin">

        </form>

    </div>

    </div>

</div>