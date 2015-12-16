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
    $userid = $_SESSION['users'];
    $type = $_GET['type'];
    if($type=="set"){
        $weight = $_GET['weight'];
        $height = $_GET['height'];
        $now = date('Y-m-d H:m:s',time());
        $bmisql = "INSERT INTO BMI VALUES ('$userid','$now','$height','$weight') ";
        $ret = $db->exec($bmisql);
        if(!$ret){
            echo $db->lastErrorMsg();
        } else {
            echo "Records created successfully\n";
        }
        $usersql = "UPDATE user SET height='$height', weight='$weight' where userid='$userid' ";
        $ret = $db->exec($usersql);
        if(!$ret){
            echo $db->lastErrorMsg();
        } else {
            echo "Records updated successfully\n";
        }
    }
    
    if($type=="get"){
        $sql = "Select height,weight from user where userid = '$userid'";
        $ret = $db->query($sql);
        if($row =$ret->fetchArray(SQLITE3_ASSOC)){
            $bmi=$row['weight']/($row['height']*$row['height'])*10000;
            echo sprintf("%.1f",$bmi);
        }
    }
    
    if($type=="line"){
        $sql = "Select * from BMI where userid='$userid' order by createTime desc limit 10";
        $ret = $db->query($sql);
        $bmiarray = "";
        $datearray="";
        $count = 0;
        while($row =$ret->fetchArray(SQLITE3_ASSOC)){
            if($count>0)
                $bmiarray = ",".$bmiarray;
            $bmi=$row['weight']/($row['height']*$row['height'])*10000;
            $bmi=sprintf("%.1f",$bmi);
            $bmiarray = $bmi.$bmiarray;
            $date = date('Y-m-d',strtotime($row['createTime']));
            $datearray = $date.$datearray;
            $datearray = ",".$datearray;
            $count++;
        }
        echo $bmiarray.$datearray;
    }
    $db->close();
?>