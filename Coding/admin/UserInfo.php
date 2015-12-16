<?php
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
    $type = $_GET['type'];
    if($type=="get"){
        $checksql = "Select * from user where userid = '$idtocheck'";
        $result = $db->query($checksql);
        if($row =$result->fetchArray(SQLITE3_ASSOC)){
            $typenum = $row['usertype'];
            $usertype="个人用户";
            if($typenum==1)
                $usertype="职业医生";
            if($typenum==2)
                $usertype="健身教练";
            
            $userinfo = array('userid'=>$row['userid'],'nickname'=>$row['nickname'],'usertype'=>$usertype,'bmitar'=>$row['targetBMI'],'cartar'=>$row['targetCalorie']);
            foreach ( $userinfo as $key => $value ) {  
                $userinfo[$key] = urlencode ( $value );  
            }  
            echo urldecode ( json_encode ( $userinfo ) );
        }
        else
            echo false;
    }
    else if($type=="modifybasic"){
        $nickname = $_POST['nickname'];
        $usertype = $_POST['usertype'];
        $usersql = "UPDATE user SET nickname='$nickname', usertype='$usertype' where userid='$idtocheck' ";
        $ret = $db->exec($usersql);
        if(!$ret){
            echo $db->lastErrorMsg();
        } else {
            echo true;
        }
    }
    else if($type=="modifyTarget"){
        $exetar = $_POST['exetar'];
        $bmitar = $_POST['bmitar'];
        $usersql = "UPDATE user SET targetBMI='$bmitar', targetCalorie='$exetar' where userid='$idtocheck' ";
        $ret = $db->exec($usersql);
        if(!$ret){
            echo $db->lastErrorMsg();
        } else {
            echo true;
        }
    }
    $db->close();
?>