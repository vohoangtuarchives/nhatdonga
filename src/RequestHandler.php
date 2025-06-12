<?php
class RequestHandler {
    public static function getParams() {
        return [
            'com' => !empty($_REQUEST['com']) ? htmlspecialchars($_REQUEST['com']) : '',
            'act' => !empty($_REQUEST['act']) ? htmlspecialchars($_REQUEST['act']) : '',
            'type' => !empty($_REQUEST['type']) ? htmlspecialchars($_REQUEST['type']) : '',
            'kind' => !empty($_REQUEST['kind']) ? htmlspecialchars($_REQUEST['kind']) : '',
            'val' => !empty($_REQUEST['val']) ? htmlspecialchars($_REQUEST['val']) : '',
            'variant' => !empty($_GET['variant']) ? htmlspecialchars($_GET['variant']) : '',
            'id_parent' => !empty($_REQUEST['id_parent']) ? htmlspecialchars($_REQUEST['id_parent']) : '',
            'id' => !empty($_REQUEST['id']) ? htmlspecialchars($_REQUEST['id']) : '',
            'curPage' => !empty($_GET['p']) ? htmlspecialchars($_GET['p']) : '1'
        ];
    }
}
