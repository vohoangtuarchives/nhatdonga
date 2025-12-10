<div class="content-wrapper">
    <div class="card">
        <div class="card-header"><?= (!empty($item)) ? 'Sửa chuyển hướng' : 'Thêm chuyển hướng' ?></div>
        <div class="card-body">
            <form method="post" action="index.php?com=redirect&act=save">
                <?php if (!empty($item)) { ?>
                    <input type="hidden" name="id" value="<?= $item['id'] ?>" />
                <?php } ?>
                <div class="mb-3">
                    <label class="form-label">From</label>
                    <input type="text" class="form-control" name="data[from]" value="<?= $item['from'] ?? '' ?>" placeholder="/duong-dan-cu" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">To</label>
                    <input type="text" class="form-control" name="data[to]" value="<?= $item['to'] ?? '' ?>" placeholder="/duong-dan-moi" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Code</label>
                    <select class="form-select" name="data[status_code]">
                        <option value="301" <?= ((int)($item['status_code'] ?? 301) === 301) ? 'selected' : '' ?>>301 Moved Permanently</option>
                        <option value="302" <?= ((int)($item['status_code'] ?? 301) === 302) ? 'selected' : '' ?>>302 Found</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="data[status]" value="hienthi" <?= (!empty($item['status']) && strstr($item['status'], 'hienthi')) ? 'checked' : '' ?> />
                        <label class="form-check-label">Hiển thị</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Lưu</button>
                <a class="btn btn-secondary" href="index.php?com=redirect&act=man">Quay lại</a>
            </form>
        </div>
    </div>
</div>

