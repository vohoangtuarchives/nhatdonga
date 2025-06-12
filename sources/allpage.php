<?php
if (!defined('SOURCES')) die("Error");

/* Query allpage */
$splist = $cache->get("SELECT id, name$lang, slug$lang, type, photo from #_product_list where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) order by numb", array('san-pham'), 'result', 7200);
$favicon = $cache->get("select photo from #_photo where type = ? and act = ? and find_in_set('hienthi',status) limit 0,1", array('favicon', 'photo_static'), 'fetch', 7200);
$logo = $cache->get("select id, photo, options from #_photo where type = ? and act = ? limit 0,1", array('logo', 'photo_static'), 'fetch', 7200);
$banner = $cache->get("select photo from #_photo where type = ? and act = ? limit 0,1", array('banner', 'photo_static'), 'fetch', 7200);
$social = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('social'), 'result', 7200);
$footer = $cache->get("select name$lang, content$lang from #_static where type = ? limit 0,1", array('footer'), 'fetch', 7200);
$txtDichVu = $cache->get("select name$lang, content$lang from #_static where type = ? limit 0,1", array('txt-dich-vu'), 'fetch', 7200);
$txtDKNT = $cache->get("select name$lang, content$lang from #_static where type = ? limit 0,1", array('txt-dknt'), 'fetch', 7200);
$screenshot   = $cache->get("SELECT id, photo,options from table_photo where type = ? ", array('screenshot'), 'fetch', 7200);
$slider = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('slide'), 'result', 7200);
$chinhsach = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('chinh-sach'), 'result', 7200);
$dichvulist = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news_list where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('dich-vu'), 'result', 7200);
$dichvuMenu = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('dich-vu'), 'result', 7200);
$doitac = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('doitac'), 'result', 7200);

$dichvuFooter = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news_list where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('dich-vu'), 'result', 7200);
$slogan = $setting['sloganvi'] ?? '';
/* Get statistic */
$counter = $statistic->getCounter();
$online = $statistic->getOnline();
$link_video = $cache->get("select id, photo, link_video from #_photo where type = ? and act = ? limit 0,1", array('video', 'photo_static'), 'fetch', 7200);
/* Newsletter */
if (isset($_POST['submit-newsletter'])) {
    $responseCaptcha = $_POST['recaptcha_response_newsletter'];
    $resultCaptcha = $func->checkRecaptcha($responseCaptcha);
    $scoreCaptcha = (!empty($resultCaptcha['score'])) ? $resultCaptcha['score'] : 0;
    $actionCaptcha = (!empty($resultCaptcha['action'])) ? $resultCaptcha['action'] : '';
    $testCaptcha = (!empty($resultCaptcha['test'])) ? $resultCaptcha['test'] : false;
    $dataNewsletter = (!empty($_POST['dataNewsletter'])) ? $_POST['dataNewsletter'] : null;

    /* Valid data */
    if (empty($dataNewsletter['email'])) {
        $flash->set('error', 'Email không được trống');
    }

    if (!empty($dataNewsletter['email']) && !$func->isEmail($dataNewsletter['email'])) {
        $flash->set('error', 'Email không hợp lệ');
    }

    $error = $flash->get('error');

    if (!empty($error)) {
        $func->transfer($error, $configBase, false);
    }
    $testCaptcha = true;
    /* Save data */
    if (($scoreCaptcha >= 0.5 && $actionCaptcha == 'Newsletter') || $testCaptcha == true) {
        $data = array();
        $data['email'] = htmlspecialchars($dataNewsletter['email']);
        $data['phone'] = (!empty($dataNewsletter['phone'])) ? $dataNewsletter['phone'] : '';
        $data['fullname'] = (!empty($dataNewsletter['fullname'])) ? $dataNewsletter['fullname'] : '';
        $data['address'] = (!empty($dataNewsletter['address'])) ? $dataNewsletter['address'] : '';
        $data['subject'] = (!empty($dataNewsletter['subject'])) ? $dataNewsletter['subject'] : '';
        $data['content'] = (!empty($dataNewsletter['content'])) ? $dataNewsletter['content'] : '';
        $data['date_created'] = time();
        $data['type'] = (!empty($dataNewsletter['type'])) ? $dataNewsletter['type'] : 'dangkynhantin';

        /* Gán giá trị gửi email */
        $strThongtin = '';
        $emailer->set('tennguoigui', $data['fullname']);
        $emailer->set('emailnguoigui', $data['email']);
        $emailer->set('dienthoainguoigui', $data['phone']);
        $emailer->set('diachinguoigui', $data['address']);
        $emailer->set('tieudelienhe', $data['subject']);
        $emailer->set('noidunglienhe', $data['content']);
        if ($emailer->get('tennguoigui')) {
            $strThongtin .= '<span style="text-transform:capitalize">' . $emailer->get('tennguoigui') . '</span><br>';
        }
        if ($emailer->get('emailnguoigui')) {
            $strThongtin .= '<a href="mailto:' . $emailer->get('emailnguoigui') . '" target="_blank">' . $emailer->get('emailnguoigui') . '</a><br>';
        }
        if ($emailer->get('diachinguoigui')) {
            $strThongtin .= '' . $emailer->get('diachinguoigui') . '<br>';
        }
        if ($emailer->get('dienthoainguoigui')) {
            $strThongtin .= 'Tel: ' . $emailer->get('dienthoainguoigui') . '';
        }
        $emailer->set('thongtin', $strThongtin);

        /* Defaults attributes email */
        $emailDefaultAttrs = $emailer->defaultAttrs();

        /* Variables email */
        $emailVars = array(
            '{emailTitleSender}',
            '{emailInfoSender}',
            '{emailSubjectSender}',
            '{emailContentSender}'
        );
        $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

        /* Values email */
        $emailVals = array(
            $emailer->get('tennguoigui'),
            $emailer->get('thongtin'),
            $emailer->get('tieudelienhe'),
            $emailer->get('noidunglienhe')
        );
        $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);

        /* Send email admin */
        $arrayEmail = null;
        $subject = "Thư liên hệ từ " . $setting['name' . $lang];
        $message = str_replace($emailVars, $emailVals, $emailer->markdown('newsletter/admin'));
        $file = 'file_attach';

        if ($emailer->send("admin", $arrayEmail, $subject, $message, $file)) {
            /* Send email customer */
            $arrayEmail = array(
                "dataEmail" => array(
                    "name" => $emailer->get('tennguoigui'),
                    "email" => $emailer->get('emailnguoigui')
                )
            );
            $subject = "Thư liên hệ từ " . $setting['name' . $lang];
            $message = str_replace($emailVars, $emailVals, $emailer->markdown('newsletter/customer'));
            $file = 'file_attach';

            if ($d->insert('newsletter', $data)) {
                $id_insert = $d->getLastInsertId();

                if ($func->hasFile("file_attach")) {
                    $fileUpdate = array();
                    $file_name = $func->uploadName($_FILES['file_attach']["name"]);

                    if ($file_attach = $func->uploadImage("file_attach", '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.DOC|.DOCX|.PDF|.RAR|.ZIP|.PPT|.PPTX|.xls|.xlsx|.jpg|.png|.gif|.JPG|.PNG|.GIF', UPLOAD_FILE_L, $file_name)) {
                        $fileUpdate['file_attach'] = $file_attach;
                        $d->where('id', $id_insert);
                        $d->update('newsletter', $fileUpdate);
                        unset($fileUpdate);
                    }
                }
                $func->transfer("Thông tin đã được gửi. Chúng tôi sẽ liên hệ với bạn sớm.", $configBase);
            } else {
                $func->transfer("Đăng ký nhận tin thất bại. Vui lòng thử lại sau.", $configBase, false);
            }

            if ($emailer->send("customer", $arrayEmail, $subject, $message, $file)) $func->transfer("Gửi liên hệ thành công", $configBase);
        } else {
            $func->transfer("Gửi liên hệ thất bại. Vui lòng thử lại sau.", $configBase, false);
        }
    } else {
        $func->transfer("Thông tin không được gửi đi. Vui lòng thử lại sau.", $configBase, false);
    }
}
