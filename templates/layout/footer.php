<footer id="footer" >
        <footer class="text-white text-sm ">
            <div class="container text-center py-5">
                <img src="/assets/images/logo.png" alt="Nhật Đông Á" class="mx-auto h-20 mb-4" />
                <h2 class="text-lg font-semibold mb-6"><?=$setting["name$lang"]?></h2>
            </div>
            <div class="container my-4">
                <div class="d-flex justify-content-between align-baseline">
              <!-- Thông tin liên hệ -->
                  <div>
                    <h3 class="font-semibold mb-2 fs-20 titleFooter">THÔNG TIN LIÊN HỆ</h3>
                    <p><i class="fa fa-location-arrow mr-2 p-2"></i><?=$optsetting["address"]?></p>
                    <p><i class="fa fa-phone mr-2 mt-2 p-2"></i>Hotline:<?=$optsetting["hotline"]?></p>
                    <p><i class="fa fa-envelope mr-2 mt-2 p-2"></i>Email: <?=$optsetting["email"]?></p>
                    <p><i class="fa fa-file-invoice mr-2 mt-2 p-2"></i>MST: <?=$optsetting["email"]?></p>
                    <div class="flex mt-3 space-x-3 text-xl">
                      <a href="#"><i class="fa-brands fa-facebook-f hover:text-gray-300"></i></a>
                      <a href="#"><i class="fa-brands fa-youtube hover:text-gray-300"></i></a>
                      <a href="#"><i class="fa-brands fa-tiktok hover:text-gray-300"></i></a>
                      <a href="#"><i class="fa-brands fa-instagram hover:text-gray-300"></i></a>
                    </div>
                  </div>

              <!-- Dịch vụ -->
              <div>
                <h3 class="font-semibold mb-2 fs-20 titleFooter">DỊCH VỤ</h3>
                <ul class="list-unstyled">

                    <?php foreach($dichvuFooter as $v) { ?>
                    <li><a class="text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>"> <span>
                          <img src="assets/images/star.svg" alt="star-icon" class="img-fluid">
                      </span><?=$v['name'.$lang]?></a></li>
                    <li>
                    <?php } ?>
                </ul>
              </div>

              <!-- Chính sách -->
              <div>
                <h3 class="font-semibold mb-2 fs-20 titleFooter">CHÍNH SÁCH</h3>
                <ul class="list-unstyled">
                    <?php foreach($chinhsach as $v) { ?>
                    <li><a class="text-decoration-none" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>"> <span>
                          <img src="assets/images/star.svg" alt="star-icon" class="img-fluid">
                      </span><?=$v['name'.$lang]?></a></li>
                    <li>
                        <?php } ?>
                </ul>
              </div>
            </div>

            <!-- Copyright -->
           <div class="d-flex justify-content-between align-items-center">
               <div class="border-t border-white/30 mt-10 pt-4 text-xs">
                   <div class="text-left">
                       <p>Copyright 2025 © All rights reserved. Design by <a href="#" class="underline">dahadu.com</a></p>
                       <p>
                           CÔNG TY TNHH MTV Nhật Đông Á, ĐKKD: <strong>3602946080</strong> do Sở KH & ĐT TP.HCM cấp ngày XX/XX/XXXX.<br />
                           GPMXH: 238/GP-BTTTT Bộ TTTT ngày <strong>04/06/2025</strong>.
                       </p>
                   </div>
               </div>
               <div>
                   <img src="assets/images/bocongthuong.png" alt="Bộ Công Thương" class="mx-auto img-fluid d-inline-block" />
               </div>
           </div>
          </div>
        </footer>
    </div>
    <div class="footer-powered">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex items-center space-x-1">
                    <i class="fa fa-phone"></i>
                    <span>Hotline mua hàng:</span>
                    <span class="font-bold">0962 597 540</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i class="fa fa-headset"></i>
                    <span>Chăm sóc khách hàng:</span>
                    <span class="font-bold">0962 597 540</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-facebook-messenger"></i>
                        <span>Messenger</span>
                    </a>
                    <a href="#" class="hover:text-gray-200 transition"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="hover:text-gray-200 transition"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="hover:text-gray-200 transition"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

        </div>
    </div>



</footer>

