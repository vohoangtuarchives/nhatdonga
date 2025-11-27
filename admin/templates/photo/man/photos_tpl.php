<?php
    $linkMan = "index.php?com=photo&act=man_photo&type=".$type;
    $linkAdd = "index.php?com=photo&act=add_photo&type=".$type;
    $linkEdit = "index.php?com=photo&act=edit_photo&type=".$type;
    $linkDelete = "index.php?com=photo&act=delete_photo&type=".$type;
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="Bảng điều khiển">Bảng điều khiển</a></li>
                <li class="breadcrumb-item active">Quản lý hình ảnh - video</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="card-footer text-sm sticky-top">
        <a class="btn btn-sm bg-gradient-primary text-white" href="<?=$linkAdd?>" title="Thêm mới"><i class="fas fa-plus mr-2"></i>Thêm mới</a>
        <a class="btn btn-sm bg-gradient-danger text-white" id="delete-all" data-url="<?=$linkDelete?>" title="Xóa tất cả"><i class="far fa-trash-alt mr-2"></i>Xóa tất cả</a>
    </div>
    <div class="card card-primary card-outline text-sm mb-0">
        <div class="card-header">
            <h3 class="card-title">Danh sách <?=isset($config['photo']['man_photo'][$type]['title_main_photo']) ? $config['photo']['man_photo'][$type]['title_main_photo'] : 'Hình ảnh'?></h3>
        </div>
        <div class="card-body table-responsive p-0">
            <?php 
            // Thu thập tất cả status unique từ tất cả items (trước khi render table)
            $allStatusKeys = [];
            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!empty($item['status'])) {
                        $statusArray = explode(',', $item['status']);
                        foreach ($statusArray as $statusKey) {
                            $statusKey = trim($statusKey);
                            if (!empty($statusKey) && !in_array($statusKey, $allStatusKeys)) {
                                $allStatusKeys[] = $statusKey;
                            }
                        }
                    }
                }
            }
            // Merge với config check_photo nếu có
            $checkFields = isset($config['photo']['man_photo'][$type]['check_photo']) ? $config['photo']['man_photo'][$type]['check_photo'] : [];
            // Thêm các status từ config vào danh sách nếu chưa có
            foreach ($checkFields as $key => $value) {
                if (!in_array($key, $allStatusKeys)) {
                    $allStatusKeys[] = $key;
                }
            }
            // Sắp xếp để đảm bảo thứ tự nhất quán
            sort($allStatusKeys);
            // Tạo mảng checkFields với tất cả status
            $allCheckFields = [];
            foreach ($allStatusKeys as $statusKey) {
                // Ưu tiên label từ config, nếu không có thì tự tạo
                if (isset($checkFields[$statusKey])) {
                    $allCheckFields[$statusKey] = $checkFields[$statusKey];
                } else {
                    // Tự tạo label từ key (ucfirst, thay - bằng space)
                    $allCheckFields[$statusKey] = ucfirst(str_replace(['-', '_'], ' ', $statusKey));
                }
            }
            ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="align-middle" width="5%">
                            <div class="custom-control custom-checkbox my-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectall-checkbox">
                                <label for="selectall-checkbox" class="custom-control-label"></label>
                            </div>
                        </th>
                        <th class="align-middle text-center" width="10%">STT</th>
                        <?php if(isset($config['photo']['man_photo'][$type]['avatar_photo']) && $config['photo']['man_photo'][$type]['avatar_photo'] == true) { ?>
                        	<th class="align-middle text-center" width="8%">Hình</th>
				        <?php } ?>
                        <?php if(isset($config['photo']['man_photo'][$type]['name_photo']) && $config['photo']['man_photo'][$type]['name_photo'] == true) { ?>
				        	<th class="align-middle" style="width:30%">Tiêu đề</th>
				        <?php } ?>
				        <?php if(isset($config['photo']['man_photo'][$type]['link_photo']) && $config['photo']['man_photo'][$type]['link_photo'] == true) { ?>
				        	<th class="align-middle">Link</th>
				        <?php } ?>
				        <?php if(isset($config['photo']['man_photo'][$type]['video_photo']) && $config['photo']['man_photo'][$type]['video_photo'] == true) { ?>
				        	<th class="align-middle">Link video</th>
				        <?php } ?>
				        <?php 
				        // Hiển thị header cho tất cả status
				        foreach($allCheckFields as $key => $value) { ?>
						  	<th class="align-middle text-center"><?=$value?></th>
						<?php } ?>
                        <th class="align-middle text-center">Thao tác</th>
                    </tr>
                </thead>
                <?php if(empty($items)) { ?>
                    <tbody><tr><td colspan="100" class="text-center">Không có dữ liệu</td></tr></tbody>
                <?php } else { ?>
                    <tbody>
                        <?php for($i=0;$i<count($items);$i++) { ?>
                            <tr>
                                <td class="align-middle">
                                    <div class="custom-control custom-checkbox my-checkbox">
                                        <input type="checkbox" class="custom-control-input select-checkbox" id="select-checkbox-<?=$items[$i]['id']?>" value="<?=$items[$i]['id']?>">
                                        <label for="select-checkbox-<?=$items[$i]['id']?>" class="custom-control-label"></label>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <input type="number" class="form-control form-control-mini m-auto update-numb" min="0" value="<?=$items[$i]['numb']?>" data-id="<?=$items[$i]['id']?>" data-table="photo">
                                </td>
                                <?php if(isset($config['photo']['man_photo'][$type]['avatar_photo']) && $config['photo']['man_photo'][$type]['avatar_photo'] == true) { ?>
	                                <td class="align-middle text-center">
	                                    <a href="<?=$linkEdit?>&id=<?=$items[$i]['id']?>" title="<?=$items[$i]['namevi']?>">
                                            <?php
                                            // Sử dụng thumb nhỏ để tối ưu tốc độ load
                                            // Nếu có thumb_photo trong config thì dùng, nếu không thì dùng 100x100x1
                                            $thumbSize = isset($config['photo']['man_photo'][$type]['thumb_photo']) 
                                                ? $config['photo']['man_photo'][$type]['thumb_photo'] 
                                                : '100x100x1';
                                            echo $func->getImage([
                                                'class' => 'rounded img-preview', 
                                                'sizes' => $thumbSize, 
                                                'upload' => UPLOAD_PHOTO_L, 
                                                'image' => $items[$i]['photo'], 
                                                'alt' => $items[$i]['namevi']
                                            ]);
                                            ?>
                                        </a>
	                                </td>
	                            <?php } ?>
                                <?php if(isset($config['photo']['man_photo'][$type]['name_photo']) && $config['photo']['man_photo'][$type]['name_photo'] == true) { ?>
	                                <td class="align-middle">
	                                    <a class="text-dark text-break" href="<?=$linkEdit?>&id=<?=$items[$i]['id']?>" title="<?=$items[$i]['namevi']?>"><?=$items[$i]['namevi']?></a>
	                                </td>
	                            <?php } ?>
                                <?php if(isset($config['photo']['man_photo'][$type]['link_photo']) && $config['photo']['man_photo'][$type]['link_photo'] == true) { ?>
                                	<td class="align-middle"><?=$items[$i]['link']?></td>
                                <?php } ?>
                                <?php if(isset($config['photo']['man_photo'][$type]['video_photo']) && $config['photo']['man_photo'][$type]['video_photo'] == true) { ?>
                                	<td class="align-middle"><?=$items[$i]['link_video']?></td>
                                <?php } ?>
                                <?php 
                                // Parse status string (dạng "hienthi,noibat" hoặc "hienthi") thành array
                                $status_string = !empty($items[$i]['status']) ? trim($items[$i]['status']) : '';
                                $status_array = [];
                                if (!empty($status_string)) {
                                    // Tách các status bằng dấu phẩy và loại bỏ khoảng trắng
                                    $status_array = array_map('trim', explode(',', $status_string));
                                    $status_array = array_filter($status_array); // Loại bỏ phần tử rỗng
                                }
                                // Sử dụng cùng allCheckFields đã tạo ở header
                                // Hiển thị checkbox cho tất cả status
                                foreach($allCheckFields as $key => $value) { ?>
                                    <td class="align-middle text-center">
                                        <div class="custom-control custom-checkbox my-checkbox">
                                            <input type="checkbox" class="custom-control-input show-checkbox" id="show-checkbox-<?=$key?>-<?=$items[$i]['id']?>" data-table="photo" data-id="<?=$items[$i]['id']?>" data-attr="<?=$key?>" <?=(in_array($key, $status_array)) ? 'checked' : ''?>>
                                            <label for="show-checkbox-<?=$key?>-<?=$items[$i]['id']?>" class="custom-control-label"></label>
                                        </div>
                                    </td>
                                <?php } ?>
                                <td class="align-middle text-center text-md text-nowrap">
                                    <a class="text-primary mr-2" href="<?=$linkEdit?>&id=<?=$items[$i]['id']?>" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                    <a class="text-danger" id="delete-item" data-url="<?=$linkDelete?>&id=<?=$items[$i]['id']?>" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </div>
    <?php if(!empty($paging)) { ?>
        <div class="card-footer text-sm pb-0">
            <?=$paging?>
        </div>
    <?php } ?>
    <div class="card-footer text-sm">
        <a class="btn btn-sm bg-gradient-primary text-white" href="<?=$linkAdd?>" title="Thêm mới"><i class="fas fa-plus mr-2"></i>Thêm mới</a>
        <a class="btn btn-sm bg-gradient-danger text-white" id="delete-all" data-url="<?=$linkDelete?>" title="Xóa tất cả"><i class="far fa-trash-alt mr-2"></i>Xóa tất cả</a>
    </div>
</section>