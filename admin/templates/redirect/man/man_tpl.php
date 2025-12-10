<?php if (!empty($items)) { ?>
<div class="content-wrapper">
    <div class="card">
        <div class="card-header">Danh sách Redirects</div>
        <div class="card-body">
            <div class="mb-3">
                <a class="btn btn-primary" href="index.php?com=redirect&act=add">Thêm chuyển hướng</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status Code</th>
                        <th>Status</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $row) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['from'] ?></td>
                        <td><?= $row['to'] ?></td>
                        <td><?= $row['status_code'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <a class="btn btn-sm btn-info" href="index.php?com=redirect&act=edit&id=<?= $row['id'] ?>">Sửa</a>
                            <a class="btn btn-sm btn-danger" href="index.php?com=redirect&act=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa chuyển hướng?')">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?= $paging['html'] ?? '' ?>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="content-wrapper">
    <div class="card">
        <div class="card-header">Danh sách Redirects</div>
        <div class="card-body">
            <p>Chưa có dữ liệu</p>
            <a class="btn btn-primary" href="index.php?com=redirect&act=add">Thêm chuyển hướng</a>
        </div>
    </div>
</div>
<?php } ?>

