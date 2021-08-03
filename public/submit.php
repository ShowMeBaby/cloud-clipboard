<?php
require_once("../common.php");
require_once("../sqlite.class.php");
checkCookie();
checkIsPost();
$message = trim($_POST["message"], " ");
if (strlen($message) > 3000) {
    returnFail('公告内容不能超过3000字');
} elseif (strlen($message) == 0) {
    returnFail('公告内容不能为空');
}
$board = new boardDB("../board.db");
$querycode = getRandomStr(4);
$cookie = getCookie();
$board->add($querycode, $cookie, $message, getIP());
returnSuccess(['url' => buildBoardUrl($querycode)]);
