<?php
     require_once "classes/General.php";
     $team = new General();
     echo json_encode($team->getMyTeam());
?>