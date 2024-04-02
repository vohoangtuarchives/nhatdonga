<div class="header">
    <div class="header-top">
        <div class="container">
            <div class="right_slogan">
                <span><i class="fas fa-map-marker-alt"></i> <?= $optsetting['phone'] ?></span>
                <span><i class="fas fa-phone"></i> <?= $optsetting['hotline'] ?></span>
            </div>
            <div class="search d-flex align-items-center justify-content-between">
                <input type="text" id="keyword" placeholder="Bạn cần tìm kiếm gì?"
                       onkeypress="doEnter(event,'keyword');"/>
                <p onclick="onSearch('keyword');">
                    <i class="fas fa-search"></i>
                </p>
            </div>
        </div>
    </div>
</div>
