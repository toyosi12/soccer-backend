<?php
    require_once "classes/Auth.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $newUser = new Auth();
    echo json_encode($newUser->register($_POST));
?>