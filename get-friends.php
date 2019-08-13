<?php
     require_once "classes/General.php";
     $friends = new General();
     echo json_encode($friends->getFriends());
?>