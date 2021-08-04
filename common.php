<?php

/**
 * 返回请求结果
 */
function returnSuccess($data)
{
    header('Content-type:text/json');
    echo json_encode(['message' => 'ok', 'data' => $data]);
    exit();
}

function returnFail($errMsg)
{
    header('HTTP/1.1 400 Bad Request');
    header('Content-type:text/json');
    echo json_encode(['error_message' => $errMsg]);
    exit();
}

/**
 * 查看是否为POST请求,不是则跳转至站点首页
 */
function checkIsPost()
{
    if (!isPost()) {
        // key不存在,直接跳转到首页
        header('location://' . $_SERVER['SERVER_NAME']);
        exit();
    }
}


/**
 * 根据查询码构建公告板地址
 * @return string       返回IP
 */
function buildBoardUrl($querycode)
{
    return "http://" . $_SERVER['SERVER_NAME'] . "/b/" . $querycode;
}

/**
 * 查看用户cookie,如果没有则创建
 * @return string       返回IP
 */
function checkCookie()
{
    $cookie_auth = $_COOKIE['auth'];
    if (strlen($cookie_auth) != 10) {
        setcookie("auth", getRandomStr(10), time() + 31 * 24 * 60 * 60);
    }
}

/**
 * 获取用户cookie,防注入
 */
function getCookie()
{
    return preg_replace('/[^A-Za-z0-9]/', '_', $_COOKIE['auth']);
}

/**
 * 获得客户端IP
 * @return string       返回IP
 */
function getIP()
{
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/**
 * 获得随机字符串
 * @param $len          需要的长度
 * @param $special      是否需要特殊符号
 * @return string       返回随机字符串
 */
function getRandomStr($len, $special = false)
{
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );

    if ($special) {
        $chars = array_merge($chars, array(
            "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
            "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
            "}", "<", ">", "~", "+", "=", ",", "."
        ));
    }

    $charsLen = count($chars) - 1;
    shuffle($chars);                            //打乱数组顺序
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
    }
    return $str;
}
/**
 * 判断当前请求是否是post
 * @return int       布尔值
 */
function isPost()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}
