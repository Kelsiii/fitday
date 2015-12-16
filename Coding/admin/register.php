<?php
    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../fitdayDB.db');
        }
    }
    $db = new MyDB();
    if(!$db){
        echo $db->lastErrorMsg();
    }

    $id =  $_POST['userid'];
    $password =  $_POST['password'];
    $nickname =  $_POST['nickname'];
    $usertype =  $_POST['usertype'];
    $position = $_POST['position'];
    $sql ="INSERT INTO user (userid,nickname,password,usertype,position)
            VALUES ('$id','$nickname','$password','$usertype','$position')";
    
    $ret = $db->exec($sql);
    if(!$ret){
        echo $db->lastErrorMsg();
    } else {
        echo "注册成功！";
    }
    $db->close();


?>