<?php
    require_once "classes/General.php";
    $data = json_decode(file_get_contents('php://input'),true);
    $chats = new General();
    echo json_encode($chats->getChats($data));
?>