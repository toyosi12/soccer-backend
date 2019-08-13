<?php
     require_once "classes/General.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
     $user = new General();
     echo json_encode($user->deleteUser($_POST['user_id']));
?>