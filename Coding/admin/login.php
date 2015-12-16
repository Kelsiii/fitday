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
    $checksql = "Select * from user where userid = '$id'";
    $result = $db->query($checksql);
    if($row = $result->fetchArray(SQLITE3_ASSOC)){
        if($row['password']!=$password)
            echo "密码错误";
        else{
            session_start();
            // store session data
            $_SESSION['users']=$id;
            echo true;
            
        }
    } else {
        echo "该用户不存在";
    }
    $db->close();


?>