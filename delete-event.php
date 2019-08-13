<?php
     require_once "classes/General.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
     $event = new General();
     echo json_encode($event->deleteEvent($_POST));
?>