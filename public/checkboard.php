<?php
require_once("../common.php");
require_once("../sqlite.class.php");
checkCookie();
$uripath = parse_url($_SERVER['REQUEST_URI'])['path'];
$querycode = explode("/", $uripath)[2];
if (!empty($querycode)) {
    $board = new boardDB("../board.db");
    $board->coutnInc($querycode);
    $result = $board->get($querycode);
    if (count($result) == 0) {
        // key不存在,直接跳转到首页
        header('location://' . $_SERVER['SERVER_NAME']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang='zh'>

<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>临时公告板</title>
</head>

<body style='padding: 10px;'>
    <div style='border-bottom: 1px solid #DCDFE6;padding-bottom: 10px;'>阅读数: <? echo $result[0]['GET_COUNT'] ?></div>
    <textarea id='ipt' readonly style='margin: 10px 0;border: 1px solid #DCDFE6;padding: 10px;display: block;width: 100%;box-sizing: border-box;outline: none;resize: none;color:rgb(75, 85, 99);'><? echo $result[0]['MESSAGE'] ?> </textarea>
    <button id='btn' style='border: 1px solid rgb(229, 231, 235);background-color: transparent;padding: 5px 20px;box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%);border-radius: 6px;color: rgb(107, 114, 128);'>复制</button>
</body>
<script type="text/javascript">
    const ipt = document.querySelector('#ipt')
    ipt.style.height = ipt.scrollHeight + 10 + 'px'
    const btn = document.querySelector('#btn')
    btn.addEventListener('click', () => {
        const input = document.querySelector('#ipt')
        input.select()
        if (document.execCommand('copy')) {
            document.execCommand('copy')
            btn.innerText = '复制成功'
        }
    })
</script>

</html>