<?php
    require_once "classes/Auth.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $user = new Auth();
    $data = array();
    $data = $user->login($_POST);

    echo (json_encode($data));
?>