<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "Crud.php";
    class General extends Crud{
        public function __construct(){
            Parent::__construct();
        }

        public function getUsers(){
            $query = "SELECT user_name, email FROM users WHERE user_type_id = 1";//select only athletes
            $stmt = $this->read2($query);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }
    }

?>