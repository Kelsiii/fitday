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
    if($type=="list"){
        $page = $_GET['page'];
        getPageN($page,$db);
    }
    if($type=="hot"){
        getHot($db);
    }
    if($type=="detail"){
        $id = $_GET['id'];
        getActivity($id,$db);
    }
    if($type=="join"){
        $id = $_GET['id'];
        joinActivity($id,$db);
    }
    if($type=="getjoin"){
        $id = $_GET['id'];
        getJoin($id,$db);
    }

    function getJoin($acid,$db){
        $searchsql = "Select * from user where userid in (Select userid from ActivityParticipation where activityid='$acid' order by createTime desc)";
        $result = $db->query($searchsql);
        $joinlist = '[';
        $count = 0 ;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $nickname = $row['nickname'];
            $userid = $row['userid'];
            $joinnode = array("userid"=>$userid,"nickname"=>$nickname);
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

    function joinActivity($acid,$db){
        $userid =  $_SESSION['users'];
        $searchsql = "Select * from ActivityParticipation where activityid='$acid' and userid = '$userid'";
        $result = $db->query($searchsql);
        if($row =$result->fetchArray(SQLITE3_ASSOC)){
            echo "你已参与过该活动啦！";
        }
        else{
            $now = date('Y-m-d H:m:s',time());
            $sql = "INSERT INTO ActivityParticipation (activityid,userid,createTime) VALUES ('$acid','$userid','$now')";
            $ret = $db->exec($sql);
            if(!$ret){
                echo $db->lastErrorMsg();
            } else {
                echo true;
            }
        }
    }
    
    function getActivity($id,$db){
        $searchsql = "Select * from Activity where rowid='$id'";
        $result = $db->query($searchsql);
        if($row =$result->fetchArray(SQLITE3_ASSOC)){
            $position = $row['position']."  ".$row['address'];
            $content = $row['content'];
            if($content=="")
                $content="发布者很懒，没有填写";
            $activityinfo = array('title'=>$row['title'],'time'=>$row['endTime'],'position'=>$position,'content'=>$content);
            foreach ( $activityinfo as $key => $value ) {  
                $activityinfo[$key] = urlencode ( $value );  
            }  
            echo urldecode ( json_encode ( $activityinfo ) );
        }
        else
            echo false;
    }
    
    function getPageN($n,$db){
        $start = ($n-1)*5;
        $end = $n*5;
        $checksql = "Select rowid,title,endTime,address,position from Activity order by createTime desc limit '$start','$end'";
        $result = $db->query($checksql);
        $activitylist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $time = date('Y-m-d H:m:s',strtotime($row['endTime']));
            $title = $row['title'];
            $address = $row['address'];
            $id = $row['rowid'];
            $position  = $row['position'];
            $state = "正在进行";
            if(strtotime($row['endTime'])<=time())
                $state="已结束";
            $joiner = 0;
            $joinsql = "select count(*) as joiner from ActivityParticipation where activityid = '$id'";
            $ret = $db->query($joinsql);
            if($joinrow = $ret->fetchArray(SQLITE3_ASSOC))
                $joiner = $joinrow['joiner'];
            
            $actnode = array("id"=>$id,"time"=>$time,"title"=>$title,"address"=>$address,"position"=>$position,"state"=>$state,"joiner"=>$joiner);
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

    function getHot($db){
        $sql = "Select rowid,title,endTime,address,position from Activity where rowid in (Select activityid from (Select activityid,count(userid) as joiner from ActivityParticipation group by activityid order by joiner desc) temp limit 4);";
        $result = $db->query($sql);
        $activitylist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $time = date('Y-m-d H:m:s',strtotime($row['endTime']));
            $title = $row['title'];
            $address = $row['address'];
            $id = $row['rowid'];
            $position  = $row['position'];
            
            $actnode = array("id"=>$id,"time"=>$time,"title"=>$title,"address"=>$address,"position"=>$position);
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

    $db->close();
?>