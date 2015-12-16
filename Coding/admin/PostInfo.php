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
        $posttype = $_GET['posttype'];
        getPostType($posttype,$db);
    }
    if($type=="detail"){
        $id = $_GET['id'];
        getPost($id,$db);
    }
    if($type=="give"){
        $id = $_GET['id'];
        giveAdvice($id,$db);
    }
    if($type=="getadvice"){
        $id = $_GET['id'];
        getAdvice($id,$db);
    }

    function getAdvice($postid,$db){
        $searchsql = "Select * from Advice where postid='$postid' order by createTime desc";
        $result = $db->query($searchsql);
        $advicelist = '[';
        $count = 0 ;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $advice = $row['advice'];
            $userid = $row['userid'];
            $sql = "Select nickname from user where userid='$userid'";
            $rec = $db->query($sql);
            if($row2 =$rec->fetchArray(SQLITE3_ASSOC)){
                $username = $row2['nickname'];
            }
            $advicenode = array("advice"=>$advice,"advisor"=>$username);
            foreach ( $advicenode as $key => $value ) {  
                $advicenode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $advicelist = $advicelist.',';
            $advicelist = $advicelist.urldecode(json_encode($advicenode));
            $count = $count+1;
        }
        $advicelist = $advicelist.']';
        echo $advicelist;
    }

    function giveAdvice($postid,$db){
        $userid =  $_SESSION['users'];
        $advice = $_POST['advice'];
        $now = date('Y-m-d H:m:s',time());
        $sql = "INSERT INTO Advice (postid,userid,createTime,advice) VALUES ('$postid','$userid','$now','$advice')";
        $ret = $db->exec($sql);
        if(!$ret){
            echo $db->lastErrorMsg();
        } else {
            echo true;
        }
        
    }
    
    function getPost($id,$db){
        $searchsql = "Select * from Forum where rowid='$id'";
        $result = $db->query($searchsql);
        if($row =$result->fetchArray(SQLITE3_ASSOC)){
            $username = "匿名用户";
            if($row['anonymous']==0){
                $userid = $row['userid'];
                $sql = "Select nickname from user where userid='$userid'";
                $rec = $db->query($sql);
                if($row2 =$rec->fetchArray(SQLITE3_ASSOC)){
                    $username = $row2['nickname'];
                }
            }
            $content = $row['content'];
            if($content=="")
                $content="发布者很懒，没有填写";
            $activityinfo = array('title'=>$row['title'],'time'=>date("Y-m-d H:m:s",strtotime($row['createTime'])),'user'=>$username,'content'=>$content);
            foreach ( $activityinfo as $key => $value ) {  
                $activityinfo[$key] = urlencode ( $value );  
            }  
            echo urldecode ( json_encode ( $activityinfo ) );
        }
        else
            echo false;
    }
    
    function getPostType($type,$db){
        $checksql = "Select rowid,title,content,userid,createTime,anonymous from Forum where type='$type' order by createTime desc limit 5";
        $result = $db->query($checksql);
        $activitylist = '[';
        $count = 0;
        while($row =$result->fetchArray(SQLITE3_ASSOC)){
            $username = "匿名用户";
            if($row['anonymous']==0){
                $userid = $row['userid'];
                $sql = "Select nickname from user where userid='$userid'";
                $rec = $db->query($sql);
                if($row2 =$rec->fetchArray(SQLITE3_ASSOC)){
                    $username = $row2['nickname'];
                }
            }
            $content = $row['content'];
            if($content=="")
                $content="发布者很懒，没有填写";
            $postnode = array('postid'=>$row['rowid'],'title'=>$row['title'],'time'=>date("Y-m-d H:m:s",strtotime($row['createTime'])),'user'=>$username,'content'=>$content);
            
            foreach ( $postnode as $key => $value ) {  
                $postnode[$key] = urlencode ( $value );  
            }
            if($count>0)
                $activitylist = $activitylist.',';
            $activitylist = $activitylist.urldecode(json_encode($postnode));
            
            $count = $count+1;
        }
        $activitylist = $activitylist.']';
        
        echo $activitylist;
    }

    

    $db->close();
?>