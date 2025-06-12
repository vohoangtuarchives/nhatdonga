<header id="header">
	<div class="d-block d-lg-none">
		<div class="menu-m-top">
			<div class="wrapper">
				<div class="text-slide">
					<marquee behavior="scroll" direction="left">
						<span><?php echo $optsetting['address'] ?></span>
					</marquee>
				</div>
			</div>
		</div>
		<div class="logo-m-top">
			<div class="wrapper">
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
					<div class="wrapper">
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
					<div class="wrapper">
						<div class="flex-box">
							<div class="on-bar divLeft">
								<span class="text-on-bar">Danh mục sản phẩm</span>
								<?php include TEMPLATE . LAYOUT . 'second-menu.php' ?>
							</div>
							<div class="divRight flex-box align-items-center">
								<div class="menu">
									<ul class="primary-menu">
										<li><a class="<?= $custom->activeMenu() ?>" href=""><?= trangchu ?></a></li>
										<li><a class="<?= $custom->activeMenu('gioi-thieu') ?>" href="gioi-thieu">giới thiệu</a></li>
										<li>
											<a class="<?= $custom->activeMenu('san-pham') ?>" href="san-pham"><?= sanpham ?></a>
											<?= $custom->primaryMenu('san-pham', 'product') ?>
										</li>
										<li><a class="<?= $custom->activeMenu('tin-tuc') ?>" href="tin-tuc">tin tức</a></li>
										<li><a class="<?= $custom->activeMenu('lien-he') ?>" href="lien-he"><?= lienhe ?></a></li>
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

		</ul>
	</nav>
</header>