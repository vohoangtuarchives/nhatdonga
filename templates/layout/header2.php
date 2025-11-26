<div id="header">
    
	<div class="d-block d-lg-none">

		<div class="menu-m-top">

			<div class="container">

				<div class="text-slide">

					<marquee behavior="scroll" direction="left">

						<span><?php echo $optsetting['address'] ?></span>

					</marquee>

				</div>

			</div>

		</div>

		<div class="logo-m-top">

			<div class="container">

				<div class="logo-mobile text-center">

					<a href="<?= $config_base ?>">

						<?= $func->getImage(['size' => '100x100x2', 'upload' => UPLOAD_PHOTO_L, 'image' => $logo['photo']]); ?>

					</a>

				</div>

			</div>

		</div>

	</div>

	<div id="fix">

		<div class="block-menu ">

			<div class="d-lg-none d-block">

				<div class="menu-m ">

					<div class="container">

						<div class="menu-m-inside">

							<div class="bar-menu">
								<a id="hamburger" href="#menu" title="Menu"><span></span></a>
							</div>

							<div class="search-res">

								<p class="icon-search transition"><i class="fa fa-search"></i></p>

								<form class="form-search search-grid w-clear">

									<input type="text" class="keyword" placeholder="<?= nhaptukhoatimkiem ?>" />

									<button type="submit"><i class="fa fa-search"></i></button>

								</form>

							</div>

						</div>

					</div>

				</div>

			</div>

			<div class="d-lg-block d-none ">

				<div class="menu-desktop bg-general <?= $source != 'index' ? 'show-second-menu' : '' ?>">

					<div class="container">

						<div class="flex-box">

							<div class="on-bar divLeft">

								<span class="text-on-bar">  <i class="fa fa-bars me-3"></i> Danh mục sản phẩm</span>

								<?php include TEMPLATE . LAYOUT . 'second-menu.php' ?>

							</div>

							<div class="divRight flex-box align-items-center">

								<div class="menu">

									<ul class="primary-menu">

                                        <li><a class="<?= $custom->activeMenu('khuyen-mai') ?>" href="khuyen-mai"><i class="fa fa-gift me-2"></i>SẢN PHẨM KHUYẾN MÃI</a></li>
                                        <li><a class="<?= $custom->activeMenu('noi-bat') ?>" href="noi-bat"><i class="fa fa-star me-2"></i>SẢN PHẨM NỔI BẬT</a></li>
                                        <li><a class="<?= $custom->activeMenu('bang-gia') ?>" href="bang-gia"><i class="fa fa-check-square me-2"></i>BẢNG GIÁ</a></li>
                                        <li><a class="<?= $custom->activeMenu('catalogue') ?>" href="catalogue"><i class="fa fa-book-open me-2"></i>CATALOGUE</a></li>

										<li><a class="<?= $custom->activeMenu('chinh-sach') ?>" href="chinh-sach"><i class="fa fa-file me-2"></i>CHÍNH SÁCH</a></li>
										<li><a class="<?= $custom->activeMenu('tuyen-dung') ?>" href="tuyen-dung"><i class="fa fa-users me-2"></i>TUYỂN DỤNG</a></li>

									</ul>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>
	<!-- mmenu  -->
    <nav id="menu">
        <ul>
            <li><a class="<?= $custom->activeMenu() ?>" href=""><?= trangchu ?></a></li>
            <li><a class="<?= $custom->activeMenu('gioi-thieu') ?>" href="gioi-thieu">Giới Thiệu</a></li>
            <?php if (count($dichvuMenu)) { ?>
                <?php foreach ($dichvuMenu as $klist => $vlist) {  ?>
                    <li>

                        <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>

                    </li>

                    <li class="line"></li>

                <?php } ?>

            <?php } ?>

            <li><a class="<?= $custom->activeMenu('kien-thuc') ?>" href="kien-thuc">Kiến Thức</a></li>

            <li><a class="<?= $custom->activeMenu('thu-vien') ?>" href="thu-vien">Thư Viện</a></li>

            <li><a class="<?= $custom->activeMenu('lien-he') ?>" href="lien-he">Liên Hệ</a></li>

        </ul>

    </nav>

</div>