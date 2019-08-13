<?php
    require_once "classes/General.php";
    $_POST = json_decode(file_get_contents('php://input'),true);
    $newTeam = new General();
    echo json_encode($newTeam->createTeam($_POST));
?>