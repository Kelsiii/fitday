<?php

    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../fitdayDB.db');
        }
    }
    $db = new MyDB();
    if(!$db){
        echo $db->lastErrorMsg();
    }
    $idtocheck = $_GET['id'];
    $checksql = "Select * from user where userid = '$idtocheck'";
    $result = $db->query($checksql);
    if($result->fetchArray(SQLITE3_ASSOC))
        echo false;
    else
        echo true;
    $db->close();
?>