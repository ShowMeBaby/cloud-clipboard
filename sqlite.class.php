<?php
class boardDB
{
    private $sqliteResult;
    private $error = '';
    /*初始化创建数据表*/
    private $createTable = 'CREATE TABLE BOARD
        ( ID        TEXT PRIMARY KEY NOT NULL,
          COOKIE    TEXT             NOT NULL,
          MESSAGE   TEXT             NOT NULL,
          IP        TEXT             NOT NULL,
          GET_COUNT INT            NOT NULL,
          IS_DELETE INT            NOT NULL,
          TIME      INT              NOT NULL );';

    function __construct($fileName)
    {
        if (file_exists($fileName)) {
            //如果有数据库，则打开数据库
            $this->sqliteResult = new MyDB($fileName);
            if (!$this->sqliteResult) {
                die("Database error：" . $this->sqliteResult->lastErrorMsg());
            }
        } else {
            //如果没有数据库，则创建数据库，并且生成数据表及插入数据
            $this->sqliteResult = new MyDB($fileName);
            if (!$this->sqliteResult) {
                die("Database error：" . $this->sqliteResult->lastErrorMsg());
            }
            $this->execute($this->createTable);
        }
    }
    function add($id, $cookie, $message, $ip)
    {
        $time = time();
        $stmt = $this->sqliteResult->prepare("INSERT INTO BOARD (ID,COOKIE,MESSAGE,IP,GET_COUNT,IS_DELETE,TIME) VALUES (:id, :cookie, :message, :ip, 0, 0, :ctime);");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $stmt->bindValue(':cookie', $cookie, SQLITE3_TEXT);
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':ctime', $time, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    function fakedel($id)
    {
        $sqliteDelete = "UPDATE BOARD SET IS_DELETE = 1 WHERE ID = '$id';";
        return $this->execute($sqliteDelete);
    }

    function realdel($id)
    {
        $sqliteDelete = "DELETE FROM BOARD WHERE ID = '$id';";
        return $this->execute($sqliteDelete);
    }

    function coutnInc($id)
    {
        $sqliteUpdata = "UPDATE BOARD SET GET_COUNT = GET_COUNT + 1 WHERE IS_DELETE = 0 AND ID = '$id';";
        return $this->execute($sqliteUpdata);
    }

    function edit($id, $message)
    {
        $time = time();
        $stmt = $this->sqliteResult->prepare("UPDATE BOARD SET MESSAGE = :message, TIME = :ctime WHERE ID = :id;");
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $stmt->bindValue(':ctime', $time, SQLITE3_INTEGER);
        return $this->fetchArray($stmt->execute());
    }

    function get($id)
    {
        $stmt = $this->sqliteResult->prepare("SELECT * FROM BOARD WHERE IS_DELETE = 0 AND ID = :id;");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        return $this->fetchArray($stmt->execute());
    }

    function getHistory($cookie)
    {
        $time = time() - (24 * 60 * 60);
        $stmt = $this->sqliteResult->prepare("SELECT ID,MESSAGE,TIME FROM BOARD WHERE COOKIE = :cookie AND TIME > :ctime ORDER BY TIME DESC LIMIT 0,10;");
        $stmt->bindValue(':cookie', $cookie, SQLITE3_TEXT);
        $stmt->bindValue(':ctime', $time, SQLITE3_INTEGER);
        return $this->fetchArray($stmt->execute());
    }

    function getAll()
    {
        $sqliteSelect = "SELECT * FROM BOARD;";
        return $this->queryDB($sqliteSelect);
    }

    //此方法用于“增、删、改”
    function execute($sql)
    {
        return $this->error = $this->sqliteResult->exec($sql);
    }

    //此方法用于“查”
    function queryDB($sql)
    {
        $result = $this->sqliteResult->query($sql);
        return $this->fetchArray($result);
    }

    function fetchArray($result)
    {
        $i = 0;
        $arr = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $arr[$i] = $row;
            $i += 1;
        }
        return $arr;
    }

    function __destruct()
    {
        if ($this->error) {
            $errormsg = $this->sqliteResult->lastErrorMsg();
            if ($errormsg != 'not an error') {
                die("Database error：" . $errormsg);
            }
        }
        $this->sqliteResult->close();
    }
}

class MyDB extends SQLite3
{
    function __construct($fileName)
    {
        $this->open($fileName);
    }
}
