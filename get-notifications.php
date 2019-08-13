<?php
     require_once "classes/General.php";
     $notification = new General();
     echo json_encode($notification->getNotifications());
?>