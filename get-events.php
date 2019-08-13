<?php
     require_once "classes/General.php";
     $events = new General();
     echo json_encode($events->getEvents());
?>