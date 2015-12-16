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
    
    $type = $_GET['type'];
    if($type=="top3"){
        getTop3($db);        
    }
    if($type=="rank"){
        getRank($db);
    }
    if($type=="timeline"){
        $pagenum = $_GET['page'];
        getPageN($pagenum,$db);
    }
    function getRank($db){
        $userid = $_SESSION['users'];
        $nowdate = date('Y-m-d',time());
        $datetime=strtotime(date("Y-m-d",time()));//获取当前日期并转换成时间戳
        $tomorrow= date('Y-m-d',$datetime+86400);//在时间戳的基础上加一天(即60*60*24)
        $searchsql = "Select userid,sum(calorie) as allcal from Exercise where ( userid='$userid' or userid in (select friendid from Friendship where userid = '$userid')) and endTime<'$tomorrow' and endTime>='$nowdate' group by userid order by allcal desc";
        $result = $db->query($searchsql);
        $count = 1 ;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            if($userid==$row['userid'])
                echo $count;
            $count++;
        }
    }
    function getTop3($db){
        $userid = $_SESSION['users'];
        $nowdate = date('Y-m-d',time());
        $datetime=strtotime(date("Y-m-d",time()));//获取当前日期并转换成时间戳
        $tomorrow= date('Y-m-d',$datetime+86400);//在时间戳的基础上加一天(即60*60*24)
        $searchsql = "Select userid,sum(calorie) as allcal from Exercise where ( userid='$userid' or userid in (select friendid from Friendship where userid = '$userid')) and endTime<'$tomorrow' and endTime>='$nowdate' group by userid order by allcal desc limit 3";
        $result = $db->query($searchsql);
        $joinlist = '[';
        $count = 0 ;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $usersid = $row['userid'];
            $sql = "Select nickname from user where userid='$usersid'";
            $rec = $db->query($sql);
            if($row2 =$rec->fetchArray(SQLITE3_ASSOC)){
                $username = $row2['nickname'];
            }
            $joinnode = array("username"=>$username,"calorie"=>$row['allcal']);
            foreach ( $joinnode as $key => $value ) {  
                $joinnode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $joinlist = $joinlist.',';
            $joinlist = $joinlist.urldecode(json_encode($joinnode));
            $count = $count+1;
        }
        $joinlist = $joinlist.']';
        echo $joinlist;
    }
    function getPageN($n,$db){
        $start = ($n-1)*10;
        $end = $n*10;
        $userid = $_SESSION['users'];
        $checksql = "Select * from Exercise  where userid='$userid' or userid in (select friendid from Friendship where userid = '$userid') order by endTime desc limit '$start','$end'";
        $result = $db->query($checksql);
        $activitylist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $time = date('Y-m-d H:m:s',strtotime($row['endTime']));
            $calorie = $row['calorie'];
            $usersid = $row['userid'];
            $sql = "Select nickname from user where userid='$usersid'";
            $rec = $db->query($sql);
            if($row2 =$rec->fetchArray(SQLITE3_ASSOC)){
                $username = $row2['nickname'];
            }
            $exetype = $row['exerciseType'];
            if($exetype==1)
                $exetype = "慢跑";
            else if($exetype==2)
                $exetype = "骑行";
            else if($exetype==3)
                $exetype = "瑜伽";
            else if($exetype==4)
                $exetype = "散步";
            else
                $exetype = "健身";
            $actnode = array("username"=>$username,"time"=>$time,"exetype"=>$exetype,"calorie"=>$calorie);
            foreach ( $actnode as $key => $value ) {  
                $actnode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $activitylist = $activitylist.',';
            $activitylist = $activitylist.urldecode(json_encode($actnode));
            
            $count = $count+1;
        }
        $activitylist = $activitylist.']';
        
        echo $activitylist;
    }
?>