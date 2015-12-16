<?php
    date_default_timezone_set("Asia/Shanghai");
    session_start();

    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../fitdayDB.db');
        }
    }
    $db = new MyDB();
    if(!$db){
        echo $db->lastErrorMsg();
    }

    $id =  $_SESSION['users'];
    $title =  $_POST['title'];
    $content =  $_POST['content'];
    $ano =  $_POST['ano'];
    $type = $_POST['type'];
    $now = date('Y-m-d H:m:s',time());
    

    $sql ="INSERT INTO Forum (userid,title,content,anonymous,createTime,type)
            VALUES ('$id','$title','$content','$ano','$now','$type')";
    
    $ret = $db->exec($sql);
    if(!$ret){
        echo $db->lastErrorMsg();
    } else {
        echo true;
    }
    
    
    $db->close();


?>