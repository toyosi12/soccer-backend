<?php
    require_once "classes/Auth.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $userExists = new Auth();
    echo json_encode($userExists->checkUser($_POST['data']));
?>