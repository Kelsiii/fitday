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
        
        if($type=="week"){
            $date=new DateTime();
            $date->modify('this week');
            $starttime=$date->format('Y-m-d');
        }
        if($type=="month"){
            $starttime=date('Y-m-01',$datetime);
        }
        if($type=="year"){
            $starttime=date('Y-01-01',$datetime);
        }
        
        $sql = "Select * from Sleep where userid = '$userid' and endTime<'$tomorrow' and endTime>='$starttime'";
        $result = $db->query($sql);
        $slptime = 0;
        $fullslp = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = $slptime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $fullslp = $fullslp + strtotime($row['fullend'])-strtotime($row['fullstart']);
        }
        if($slptime==0)
            echo 0;
        else
            echo floor($fullslp/$slptime*100);
    }

    if ($query=="line"){
        $sql = "Select * from Sleep where userid='$userid' order by endTime desc limit 10";
        $ret = $db->query($sql);
        $slparray = "";
        $fullslparray="";
        $datearray="";
        $count = 0;
        while($row =$ret->fetchArray(SQLITE3_ASSOC)){
            if($count>0)
                $fullslparray = ",".$fullslparray;
            $slptime = floor((strtotime($row['endTime'])-strtotime($row['startTime']))/60);
            $fullslp = floor((strtotime($row['fullend'])-strtotime($row['fullstart']))/60);
            $fullslparray = $fullslp.$fullslparray;
            $slparray = $slptime.$slparray;
            $slparray = ",".$slparray;
            $date = date('m-d',strtotime($row['endTime']));
            $datearray = $date.$datearray;
            $datearray = ",".$datearray;
            $count++;
        }
        echo $fullslparray.$slparray.$datearray;
    }

    function calTime($time){
        $hour = 0;
        $min = 0;
        $sec = 0;
        if(floor($time/3600)){
            $hour = floor($time/3600);
            $time = $time%3600;
        }
        if(floor($time/60)){
            $min = floor($time/60);
            $sec = $time%60;
        }
        else
            $sec = $time;
        if($hour<10)
            $hour = '0'.$hour;
        if($min<10)
            $min = '0'.$min;
        if($sec<10)
            $sec = '0'.$sec;
        return $hour.':'.$min.':'.$sec;
    }
?>