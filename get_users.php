<?php
     require_once "classes/General.php";
     $users = new General();
     echo json_encode($users->getUsers());
?>