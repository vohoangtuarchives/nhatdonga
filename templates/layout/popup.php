<div class="modal fade" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="popupModalLabel"><?= $popup['name'.$lang] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a href="<?= $popup['link'] ?>" target="_blank">
                    <?php $func->getImage(['class' => 'img-fluid', 'sizes' => '600x400x1', 'upload' => UPLOAD_PHOTO_L, 'image' => $popup['photo']]) ?>
                </a>
            </div>
        </div>
    </div>
</div>