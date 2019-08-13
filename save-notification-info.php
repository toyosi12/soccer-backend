<?php
    require 'classes/Crud.php';
    $_data = json_decode(file_get_contents('php://input'),true);
    $q = new Crud();
    $user_id = $_data['user_id'];
    $sub = json_encode($_data['sub']);
    $query = "UPDATE users SET notification_json = ? WHERE user_id = ?";
    $binder = array("ss", "$sub", "$user_id");
    $stmt = $q->update($query, $binder);
    echo json_encode($stmt);
    
?>