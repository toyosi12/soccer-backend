<?php
     require_once "classes/General.php";
     $teamMembers = new General();
     echo json_encode($teamMembers->getTeamMembers());
?>