<header id="header">
	<div class="d-block d-lg-none">
		<div class="menu-m-top">
			<div class="wrapper">
				<div class="text-slide">
					<marquee behavior="scroll" direction="left">
						<span><?= $optsetting['address'] ?></span>
					</marquee>
				</div>
			</div>
		</div>
		<div class="logo-m-top">
			<div class="wrapper">
				<div class="logo-mobile text-center">
					<a href="<?= $config_base ?>">
						<img class="img-full" onerror="this.src='thumbs/248x98x1/assets/images/noimage.png';"
							src="thumbs/248x98x1/upload/photo/<?= $logo['photo'] ?>" alt="<?= $logo['photo'] ?>">
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="header-topbar">
		<div class="wrapper">
			<div class="row d-flex info-container justify-content-between">
				<div class="col-auto d-flex align-items-center">
					<span class="icon icon-45"><i class="fa fa-home"></i></span>
					<div>
						<p><?= $optsetting["address"] ?></p>
					</div>
				</div>
				<div class="col-auto d-flex align-items-center">
					<span class="icon icon-45"><i class="fa fa-phone"></i></span>
					<div>
						<p><?= $optsetting["phone"] ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="fix">
		<div class="block-menu">
			<div class="d-lg-none d-block">
				<div class="menu-m">
					<div class="wrapper">
						<div class="menu-m-inside">
							<div class="bar-menu">
								<a id="hamburger" href="#menu" title="Menu"><span></span></a>
							</div>
							<div class="search-res d-none">
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
			<div class="d-lg-block d-none">
				<div class="block_header_bottom">
					<div class="wrapper d-flex justify-content-between align-items-center">
						<div class="logo text-center">
							<a href="<?= $config_base ?>">
								<img class="img-full" onerror="this.src='thumbs/248x98x2/assets/images/noimage.png';"
									src="thumbs/248x98x2/upload/photo/<?= $logo['photo'] ?>" alt="<?= $logo['photo'] ?>">
							</a>
						</div>
						<div class="menu">
							<ul class="primary-menu">
								<li><a class="<?= $custom->activeMenu() ?>" href=""><?= trangchu ?></a></li>
								<li><a class="<?= $custom->activeMenu('gioi-thieu') ?>" href="gioi-thieu">Giới Thiệu</a></li>
								<li>
									<a class="<?= $custom->activeMenu('dich-vu') ?>" href="dich-vu">Dịch Vụ</a>
									
									<?php if (count($dichvuMenu)) { ?>
										<ul>
									<?php foreach ($dichvuMenu as $klist => $vlist) {  ?>
										<li>
											<a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
										</li>
										<li class="line"></li>
									<?php } ?>
									</ul>	
								<?php } ?>
								</li>
								<li><a class="<?= $custom->activeMenu('kien-thuc') ?>" href="kien-thuc">Kiến Thức</a></li>
								<li><a class="<?= $custom->activeMenu('tin-tuc') ?>" href="tin-tuc">Tin Tức</a></li>
								<li><a class="<?= $custom->activeMenu('lien-he') ?>" href="lien-he">Liên Hệ</a></li>

                                <li>
                                    <button class="border-0 datlich">
                                        <img src="assets/images/img/datlich.svg" alt="">
                                    </button>

                                </li>
								<li>
									<div class="search-res ms-3">
										<p class="icon-search transition"><i class="fa fa-search"></i></p>
										<form class="form-search search-grid w-clear">
											<input type="text" class="keyword" placeholder="<?= nhaptukhoatimkiem ?>" />
											<button type="submit"><i class="fa fa-search"></i></button>
										</form>
									</div>
								</li>

							</ul>
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
</header>