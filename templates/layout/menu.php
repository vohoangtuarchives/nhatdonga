<div class="w-menu <?= ($source == 'index') ? 'position-absolute' : ' ' ?>">
    <div class="menu">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="logo-header" href="">
                <img class='lazy'
                     onerror="this.src='thumbs/<?= $config['photo']['photo_static']['logo']['thumb'] ?>/assets/images/noimage.png';"
                     data-src='thumbs/<?= $config['photo']['photo_static']['logo']['thumb'] ?>/storage/upload/photo/<?= $logo["photo"] ?>'
                     alt='<?= $setting["name$lang"] ?>'/>
            </a>
            <ul class="menu d-flex align-items-center justify-content-end">
                <li class="col-auto <?php if ($com == 'index') echo 'active'; ?>"><a class="active transition" href=""
                                                                                     title="Trang chủ">Trang Chủ</a>
                </li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'gioi-thieu') echo 'active'; ?>"><a class="transition"
                                                                                          href="gioi-thieu"
                                                                                          title="Trang chủ">Giới
                        Thiệu</a></li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'san-pham') echo 'active'; ?>">
                    <a class="has-child  transition" href="san-pham" title="Tin tức">Sản Phẩm</a>
					<?php if (count($indexMenu)) { ?>
                        <ul>
							<?php foreach ($indexMenu as $klist => $vlist) {
								$spcat = $vlist['cat'];
								?>
                                <li>

                                    <a class="has-child" title="<?= $vlist['key']['name'] ?>"
                                       href="<?= $vlist['key']['slug'] ?>"><?= $vlist['key']['name'] ?></a>
									
									<?php if (!empty($spcat)) { ?>
                                        <ul>
											<?php foreach ($spcat as $kcat => $vcat) {
												$spitem = $vcat['items'] ?>
                                                <li>

                                                    <a class="has-child" title="<?= $vcat['key']['name'] ?>"
                                                       href="<?= $vcat['key']['slug'] ?>"><?= $vcat['key']['name'] ?></a>
													
													<?php if (!empty($spitem)) { ?>

                                                        <ul>
															
															<?php foreach ($spitem as $kitem => $vitem) {
																
																$spsub = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>

                                                                <li>

                                                                    <a class="has-child"
                                                                       title="<?= $vitem['name' . $lang] ?>"
                                                                       href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
																	
																	<?php if (!empty($spsub)) { ?>

                                                                        <ul>
																			
																			<?php foreach ($spsub as $ksub => $vsub) { ?>

                                                                                <li>

                                                                                    <a class="transition"
                                                                                       title="<?= $vsub['name' . $lang] ?>"
                                                                                       href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>

                                                                                </li>
																			
																			<?php } ?>

                                                                        </ul>
																	
																	<?php } ?>

                                                                </li>
															
															<?php } ?>

                                                        </ul>
													
													<?php } ?>

                                                </li>
											
											<?php } ?>

                                        </ul>
									
									<?php } ?>

                                </li>
							
							<?php } ?>
                        </ul>
					<?php } ?>
                </li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'dich-vu') echo 'active'; ?>">
                    <a class="has-child  transition" href="dich-vu" title="Tin Tức">Dịch Vụ</a>
                </li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'tin-tuc') echo 'active'; ?>">
                    <a class="has-child  transition" href="tin-tuc" title="Tin Tức">Tin Tức</a>
                </li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'du-an') echo 'active'; ?>">
                    <a class="has-child  transition" href="du-an" title="Dự Án">Dự Án</a>
                </li>
                <li class="mx-line"></li>
                <li class="col-auto <?php if ($com == 'lien-he') echo 'active'; ?>"><a class=" transition"
                                                                                       href="lien-he"
                                                                                       title="Liên hệ">Liên hệ</a></li>
            </ul>
        </div>
    </div>
    <div class="menu-res">
        <div class="menu-bar-res">
            <a id="hamburger" href="#menu" title="Menu"><span></span></a>
            <div class="search-res">
                <p class="icon-search transition"><i class="fa fa-search"></i></p>
                <div class="search-grid w-clear">
                    <input type="text" name="keyword-res" id="keyword-res" placeholder="Nhập từ khóa cần tìm..."
                           onkeypress="doEnter(event,'keyword-res');"/>
                    <p onclick="onSearch('keyword-res');"><i class="fa fa-search"></i></p>
                </div>
            </div>
        </div>
        <nav id="menu">
            <ul>
                <li class="col-auto <?php if ($com == 'index') echo 'active'; ?>"><a class="active transition" href=""
                                                                                     title="Trang chủ">Trang Chủ</a>
                </li>
                <li class="col-auto <?php if ($com == 'gioi-thieu') echo 'active'; ?>"><a class="transition"
                                                                                          href="gioi-thieu"
                                                                                          title="Trang chủ">Giới
                        Thiệu</a></li>
                <li class="col-auto <?php if ($com == 'san-pham') echo 'active'; ?>">
                    <a class="has-child  transition" href="san-pham" title="Tin tức">Sản Phẩm</a>
					<?php if (count($indexMenu)) { ?>
                        <ul>
							<?php foreach ($indexMenu as $klist => $vlist) {
								$spcat = $vlist['cat'];
								?>
                                <li>

                                    <a class="has-child" title="<?= $vlist['key']['name'] ?>"
                                       href="<?= $vlist['key']['slug'] ?>"><?= $vlist['key']['name'] ?></a>
									
									<?php if (!empty($spcat)) { ?>
                                        <ul>
											<?php foreach ($spcat as $kcat => $vcat) {
												$spitem = $vcat['items'] ?>
                                                <li>

                                                    <a class="has-child" title="<?= $vcat['key']['name'] ?>"
                                                       href="<?= $vcat['key']['slug'] ?>"><?= $vcat['key']['name'] ?></a>
													
													<?php if (!empty($spitem)) { ?>

                                                        <ul>
															
															<?php foreach ($spitem as $kitem => $vitem) {
																
																$spsub = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>

                                                                <li>

                                                                    <a class="has-child"
                                                                       title="<?= $vitem['name' . $lang] ?>"
                                                                       href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
																	
																	<?php if (!empty($spsub)) { ?>

                                                                        <ul>
																			
																			<?php foreach ($spsub as $ksub => $vsub) { ?>

                                                                                <li>

                                                                                    <a class="transition"
                                                                                       title="<?= $vsub['name' . $lang] ?>"
                                                                                       href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>

                                                                                </li>
																			
																			<?php } ?>

                                                                        </ul>
																	
																	<?php } ?>

                                                                </li>
															
															<?php } ?>

                                                        </ul>
													
													<?php } ?>

                                                </li>
											
											<?php } ?>

                                        </ul>
									
									<?php } ?>

                                </li>
							
							<?php } ?>
                        </ul>
					<?php } ?>
                </li>
                <li class="col-auto <?php if ($com == 'tin-tuc') echo 'active'; ?>">
                    <a class="has-child  transition" href="tin-tuc" title="Tin Tức">Tin Tức</a>
                </li>
                <li class="col-auto <?php if ($com == 'lien-he') echo 'active'; ?>"><a class=" transition"
                                                                                       href="lien-he"
                                                                                       title="Liên hệ">Liên hệ</a></li>
            </ul>
        </nav>
    </div>
</div>
