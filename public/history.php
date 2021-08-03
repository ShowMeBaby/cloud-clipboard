<?php
require_once("../common.php");
require_once("../sqlite.class.php");
checkCookie();
checkIsPost();
$cookie = getCookie();
$board = new boardDB("../board.db");
$result = array_map(function ($v) {
    return [
        'created_at' => date("Y-m-d H:i:s", $v['TIME']),
        'content' => $v['MESSAGE'],
        'url' =>  buildBoardUrl($v['ID'])
    ];
}, $board->getHistory($cookie));
returnSuccess($result);
