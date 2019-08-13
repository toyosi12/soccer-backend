<?php
    require_once "classes/General.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $invite = new General();
    echo json_encode($invite->sendInvite($_POST));
?>