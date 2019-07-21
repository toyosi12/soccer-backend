<?php
    require_once "classes/Auth.php";
    $activation = new Auth();
    $data = array();
    $stmt = $activation->activateAccount($_GET['t']);
    print_r($stmt);
?>