<div id="topbar">
    <div class="header-topbar">
        <div class="wrapper">
            <div class="w-100 d-flex justify-content-between align-items-center">
                <div>

                </div>
                <div class="d-flex">
                    <div class="col-auto d-flex align-items-center">
                        <span class="icon icon-45"><i class="fa fa-home"></i></span>
                        <div>
                            <p><?= $optsetting["email"] ?></p>
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
    </div>
</div>
<div id="section-logo">
    <div class="wrapper d-flex justify-content-between align-items-center">
        <a href="<?= $config_base ?>">
            <img class="img-fluid" onerror="this.src='thumbs/128x55x1/assets/images/noimage.png';"
                 src="thumbs/124x50x1/upload/photo/<?= $logo['photo'] ?>" alt="<?= $logo['photo'] ?>">
        </a>
        <div class="section-menu">
            <?php include __DIR__."/menu.php" ?>
        </div>
    </div>
</div>