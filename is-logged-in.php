<?php
    if(!isset($_SESSSION)){
        session_start();
    }
    if(isset($_SESSION['user'])){
        echo '{"status" : true}';
    }else{
        echo '{"status" : false}';
    }
?>