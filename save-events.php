<?php
    require_once "classes/General.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $newEvents = new General();
    echo json_encode($newEvents->saveEvents($_POST));
?>