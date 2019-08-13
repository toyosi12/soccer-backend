<?php
    require_once "Db_conn.php";
    class Crud extends Db_conn{
        public function __construct(){
            parent::__construct();
        }
        
        public function create($query, $binder){
            $resp = array();
            $stmt = $this->conn->prepare($query) or die($this->conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
                $resp['error'] = mysqli_error($this->conn);
            }
            return $resp;
        }

        public function read($query,$binder){
            $stmt = $this->conn->prepare($query) or die($this->conn->error);
            $stmt->bind_param(...$binder);
            $stmt->execute();
            $data = $stmt->get_result();
            return $data;
        }

        public function read2($query){
            return $this->conn->query($query);
        }

        public function update($query, $binder){
            $resp = array();
            $stmt = $this->conn->prepare($query) or die($this->conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
            }
            return $resp;
        }

        public function delete($query, $binder){
            $resp = array();
            $stmt = $this->conn->prepare($query) or die($this->conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
            }
            return $resp;
        }

        

    }
?>