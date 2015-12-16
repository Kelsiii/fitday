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
        $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$nowdate'";
        $result = $db->query($checksql);
        $exetime = 0;
        $calorie = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $exetime = $exetime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $calorie = $calorie + $row['calorie'];
        }
        $todayexe = array('exetime'=>calTime($exetime),'calorie'=>$calorie);
        foreach ( $todayexe as $key => $value ) {  
            $todayexe[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $todayexe ) );
       
    }
    else if($getType=="week"){
        $date=new DateTime();
        $date->modify('this week');
        $first_day_of_week=$date->format('Y-m-d');
        
        $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_week'";
        $result = $db->query($checksql);
        $exetime = 0;
        $calorie = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $exetime = $exetime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $calorie = $calorie + $row['calorie'];
        }
        $todayexe = array('exetime'=>calTime($exetime),'calorie'=>$calorie);
        foreach ( $todayexe as $key => $value ) {  
            $todayexe[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $todayexe ) );
    }
    else if($getType=="month"){
        $first_day_of_month=date('Y-m-01',$datetime);
        $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_month'";
        $result = $db->query($checksql);
        $exetime = 0;
        $calorie = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $exetime = $exetime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $calorie = $calorie + $row['calorie'];
        }
        $todayexe = array('exetime'=>calTime($exetime),'calorie'=>$calorie);
        foreach ( $todayexe as $key => $value ) {  
            $todayexe[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $todayexe ) );
    }
    else if($getType=="year"){
        $first_day_of_year=date('Y-01-01',$datetime);
        $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_year'";
        $result = $db->query($checksql);
        $exetime = 0;
        $calorie = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $exetime = $exetime + strtotime($row['endTime'])-strtotime($row['startTime']);
            $calorie = $calorie + $row['calorie'];
        }
        $todayexe = array('exetime'=>calTime($exetime),'calorie'=>$calorie);
        foreach ( $todayexe as $key => $value ) {  
            $todayexe[$key] = urlencode ( $value );  
        }  
        echo urldecode ( json_encode ( $todayexe ) );
    }
    else if($getType=="list"){
        $checksql = "Select * from Exercise where userid = '$idtocheck' order by endTime desc limit 10";
        
        if($listType=="today"){
             $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$nowdate' order by endTime desc limit 10";
        }
        if($listType=="week"){
            $date=new DateTime();
            $date->modify('this week');
            $first_day_of_week=$date->format('Y-m-d');
        
            $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_week' order by endTime desc limit 10";
        }
        if($listType=="month"){
            $first_day_of_month=date('Y-m-01',$datetime);
            $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_month' order by endTime desc limit 10";
        }
        if($listType=="year"){
            $first_day_of_year=date('Y-01-01',$datetime);
            $checksql = "Select * from Exercise where userid = '$idtocheck' and endTime<'$tomorrow' and endTime>='$first_day_of_year' order by endTime desc limit 10";
        }
        $result = $db->query($checksql);
        $exelist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $date = date('Y-m-d',strtotime($row['endTime']));
            $exetime = strtotime($row['endTime'])-strtotime($row['startTime']);
            $calorie = $row['calorie'];
            $type = $row['exerciseType'];
            if($type==1)
                $type = "慢跑";
            else if($type==2)
                $type = "骑行";
            else if($type==3)
                $type = "瑜伽";
            else if($type==4)
                $type = "散步";
            else
                $type = "健身";
            
            $exenode = array("date"=>$date,"exetime"=>calTime($exetime),"calorie"=>$calorie,"type"=>$type);
            foreach ( $exenode as $key => $value ) {  
                $exenode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $exelist = $exelist.',';
            $exelist = $exelist.urldecode(json_encode($exenode));
            
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