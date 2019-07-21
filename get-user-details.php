<?php
     require_once "classes/Auth.php";
     $user = new Auth();
     echo json_encode($user->userDetails());
?>