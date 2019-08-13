<?php
     require_once "classes/General.php";
     $_POST = json_decode(file_get_contents('php://input'),true);
     $notification = new General();
     echo json_encode($notification->newNotification($_POST));
?>