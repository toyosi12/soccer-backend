<?php
    require_once "classes/General.php";
    $data = json_decode(file_get_contents('php://input'),true);
    $user = new General();
    echo json_encode($user->getCurrentUserDetails($data));
?>