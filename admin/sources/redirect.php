<?php

if (!defined('SOURCES')) die('Error');

$linkMan = "index.php?com=redirect&act=man";
$linkAdd = "index.php?com=redirect&act=add";
$linkEdit = "index.php?com=redirect&act=edit";
$linkSave = "index.php?com=redirect&act=save";
$linkDelete = "index.php?com=redirect&act=delete";

function ensureRedirectsTable($d)
{
    $sql = "CREATE TABLE IF NOT EXISTS #_redirects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        `from` VARCHAR(255) NOT NULL,
        `to` VARCHAR(255) NOT NULL,
        status_code INT NOT NULL DEFAULT 301,
        status VARCHAR(255) DEFAULT '',
        date_created INT DEFAULT 0,
        date_updated INT DEFAULT 0,
        UNIQUE KEY uniq_from (`from`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $d->rawQuery($sql);
}

ensureRedirectsTable($d);

switch ($act) {
    case "man":
        $curPage = (int)($_GET['p'] ?? 1);
        $perPage = 20;
        $start = ($curPage - 1) * $perPage;
        $keyword = trim($_GET['keyword'] ?? '');
        $where = " where 1";
        $params = [];
        if ($keyword !== '') {
            $where .= " and (`from` like ? or `to` like ?)";
            $kw = "%" . $keyword . "%";
            $params[] = $kw; $params[] = $kw;
        }
        $items = $d->rawQuery("select * from #_redirects" . $where . " order by id desc limit {$start},{$perPage}", $params);
        $countRow = $d->rawQueryOne("select count(*) as c from #_redirects" . $where, $params);
        $total = (int)($countRow['c'] ?? 0);
        $paging = $func->paging($total, $perPage, $curPage, $linkMan);
        $template = "redirect/man/man_tpl";
        break;

    case "add":
    case "edit":
        $id = (int)($_GET['id'] ?? 0);
        $item = [];
        if ($act === 'edit' && $id) {
            $item = $d->rawQueryOne("select * from #_redirects where id = ? limit 0,1", [$id]);
        }
        $template = "redirect/man/man_add_tpl";
        break;

    case "save":
        if (!empty($_POST)) {
            $id = (int)($_POST['id'] ?? 0);
            $data = $_POST['data'] ?? [];
            foreach ($data as $k => $v) {
                if (is_string($v)) $data[$k] = SecurityHelper::sanitize($v);
            }
            $data['status_code'] = (int)($data['status_code'] ?? 301);
            $data['status'] = $data['status'] ?? '';
            $data['date_updated'] = time();
            if ($id) {
                $d->where('id', $id);
                $saved = $d->update('redirects', $data);
            } else {
                $data['date_created'] = time();
                $saved = $d->insert('redirects', $data);
            }
            $func->transfer($saved ? "Lưu chuyển hướng thành công" : "Lưu chuyển hướng thất bại", $linkMan, $saved);
        } else {
            $func->transfer("Không có dữ liệu", $linkMan, false);
        }
        break;

    case "delete":
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $d->where('id', $id);
            $d->delete('redirects');
        }
        $func->transfer("Xóa chuyển hướng", $linkMan);
        break;

    default:
        $template = "redirect/man/man_tpl";
}
