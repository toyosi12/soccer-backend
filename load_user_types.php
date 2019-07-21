<?php
    require_once "classes/Auth.php";
    $userTypes = new Auth();
    $data = array();
    $stmt = $userTypes->loadUserTypes();
    while($row = $stmt->fetch_assoc()){
        $data[] = $row;
    }
    echo (json_encode($data));
?>