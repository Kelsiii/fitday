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
    $idtocheck = $_SESSION['users'];
    $getType = $_GET['type'];
    $listType = $_GET['listtype'];
    $nowdate = date('Y-m-d',time());
    $datetime=strtotime(date("Y-m-d",time()));//获取当前日期并转换成时间戳
    $tomorrow= date('Y-m-d',$datetime+86400);//在时间戳的基础上加一天(即60*60*24)
        
    if($getType=="today"){
        $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$nowdate'";
        $result = $db->query($checksql);
        $slptime = 0;
        $fullslp = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = $slptime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $fullslp = $fullslp + strtotime($row['fullend'])-strtotime($row['fullstart']);
        }
        $sleepdata = array('slptime'=>calTime($slptime),'full_slptime'=>calTime($fullslp));
        foreach ( $sleepdata as $key => $value ) {  
            $sleepdata[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $sleepdata ) );
       
    }
    else if($getType=="week"){
        $date=new DateTime();
        $date->modify('this week');
        $first_day_of_week=$date->format('Y-m-d');
        
        $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_week'";
        $result = $db->query($checksql);
        $slptime = 0;
        $fullslp = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = $slptime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $fullslp = $fullslp + strtotime($row['fullend'])-strtotime($row['fullstart']);
        }
        $sleepdata = array('slptime'=>calTime($slptime),'full_slptime'=>calTime($fullslp));
        foreach ( $sleepdata as $key => $value ) {  
            $sleepdata[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $sleepdata ) );
    }
    else if($getType=="month"){
        $first_day_of_month=date('Y-m-01',$datetime);
        $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_month'";
        $result = $db->query($checksql);
        $slptime = 0;
        
        $fullslp = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = $slptime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $fullslp = $fullslp + strtotime($row['fullend'])-strtotime($row['fullstart']);
        }
        $sleepdata = array('slptime'=>calTime($slptime),'full_slptime'=>calTime($fullslp));
        foreach ( $sleepdata as $key => $value ) {  
            $sleepdata[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $sleepdata ) );
    }
    else if($getType=="year"){
        $first_day_of_year=date('Y-01-01',$datetime);
        $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_year'";
        $result = $db->query($checksql);
        $slptime = 0;
        $fullslp = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = $slptime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $fullslp = $fullslp + strtotime($row['fullend'])-strtotime($row['fullstart']);
        }
        $sleepdata = array('slptime'=>calTime($slptime),'full_slptime'=>calTime($fullslp));
        foreach ( $sleepdata as $key => $value ) {  
            $sleepdata[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $sleepdata ) );
    }
    else if($getType=="list"){
        $checksql = "Select * from Sleep where userid = '$idtocheck' order by endTime desc limit 10";
        
        if($listType=="today"){
             $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$nowdate' order by endTime desc limit 10";
        }
        if($listType=="week"){
            $date=new DateTime();
            $date->modify('this week');
            $first_day_of_week=$date->format('Y-m-d');
        
            $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_week' order by endTime desc limit 10";
        }
        if($listType=="month"){
            $first_day_of_month=date('Y-m-01',$datetime);
            $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_month' order by endTime desc limit 10";
        }
        if($listType=="year"){
            $first_day_of_year=date('Y-01-01',$datetime);
            $checksql = "Select * from Sleep where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_year' order by endTime desc limit 10";
        }
        $result = $db->query($checksql);
        $exelist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $slptime = strtotime($row['endTime'])-strtotime($row['startTime']);
            $start = date('Y-m-d H:m:s',strtotime($row['startTime']));
            $end = date('Y-m-d H:m:s',strtotime($row['endTime']));
            $fullslp = calTime(strtotime($row['fullend'])-strtotime($row['fullstart']));
            $slpnode = array("start"=>$start,"end"=>$end,"slptime"=>calTime($slptime),"fullslptime"=>$fullslp);
            foreach ( $slpnode as $key => $value ) {  
                $slpnode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $exelist = $exelist.',';
            $exelist = $exelist.urldecode(json_encode($slpnode));
            
            $count = $count+1;
        }
        $exelist = $exelist.']';
        
        echo $exelist;
    }
    else
        echo false;
    
    
   

    $db->close();
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