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

    $query = $_GET['query'];
    $userid = $_SESSION['users'];
    if($query=="pie"){
        $type = $_GET['type'];
        $starttime = date('Y-m-d',time());
        $datetime=strtotime(date("Y-m-d",time()));//获取当前日期并转换成时间戳
        $tomorrow= date('Y-m-d',$datetime+86400);//在时间戳的基础上加一天(即60*60*24)
        
        $tar = 600;
        $tarsql = "Select targetCalorie from user where userid ='$userid'";
        $result = $db->query($tarsql);
        if($row =$result->fetchArray(SQLITE3_ASSOC)){
            $tar = $row['targetCalorie'];
        }
        if($type=="week"){
            $date=new DateTime();
            $date->modify('this week');
            $starttime=$date->format('Y-m-d');
            $tar = $tar*7;
        }
        if($type=="month"){
            $starttime=date('Y-m-01',$datetime);
            $tar = $tar*30;
        }
        if($type=="year"){
            $starttime=date('Y-01-01',$datetime);
            $tar = $tar*365;
        }
        
        $sql = "Select * from Exercise where userid = '$userid' and endTime<'$tomorrow' and endTime>='$starttime'";
        $result = $db->query($sql);
        $calorie = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $calorie = $calorie + $row['calorie'];
        }
        echo floor($calorie/$tar*100);
    }

    if ($query=="line"){
        $cals = "";
        for($i=1;$i<13;$i++){
            if($i>1)
                $cals = $cals.",";
            $year = date('Y',time());
            if($i<10)
                $month = "0".$i;
            else
                $month = $i;
            $day="01";
            $beginDate=$_date = date("Y-m-d",mktime(0,0,0,$month,$day,$year));
            $endDate = date('Y-m-d', strtotime("$beginDate +1 month"));
            $sql = "Select * from Exercise where userid = '$userid' and endTime<'$endDate' and endTime>='$beginDate'";
            $result = $db->query($sql);
            $calorie = 0;
            while($row =$result->fetchArray(SQLITE3_ASSOC)){
                $calorie = $calorie + $row['calorie'];
            }
            $calorie = floor($calorie);
            $cals = $cals.$calorie;
        }
        echo $cals;
    }
?>