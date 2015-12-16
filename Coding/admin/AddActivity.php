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
    $actime =  $_POST['time'];
    $content =  $_POST['description'];
    $address = $_POST['position'];
    $now = date('Y-m-d H:m:s',time());
    
    $searchsql = "Select position from user where userid='$id'";
    $result = $db->query($searchsql);
    $position = "南京";
    if($row =$result->fetchArray(SQLITE3_ASSOC)){
        $position = $row['position'];
    }

    $sql ="INSERT INTO Activity (userid,title,address,content,endTime,createTime,position)
            VALUES ('$id','$title','$address','$content','$actime','$now','$position')";
    
    $ret = $db->exec($sql);
    if(!$ret){
        echo $db->lastErrorMsg();
    } else {
        echo true;
    }
    
    
    $tmp = $db->query("Select rowid from Activity where userid='$id' and createTime='$now'");
    if($row =$tmp->fetchArray(SQLITE3_ASSOC)){
        $acid = $row['rowid'];
        $now = date('Y-m-d H:m:s',time());
        $sql = "INSERT INTO ActivityParticipation (activityid,userid,createTime) VALUES ('$acid','$id','$now')";
        $ret = $db->exec($sql);
    }
    
    
    $db->close();


?>