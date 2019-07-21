<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "Crud.php";
    class Auth extends Crud{
        public function __construct(){
            Parent::__construct();
        }
        private $data = array();
        private $errMessage;
        public function checkUser($username){
            $query = "SELECT email, first_name, last_name, phone, status, user_id, user_name,
                            user_type_id FROM users WHERE user_name = ?";
            $binder = array("s", "$username");
            $no = $this->read($query, $binder);
            if($no->num_rows > 0){
                $data['exists'] = true;
            }else{
                $data['exists'] = false;
            }
            return $data;
        }

        public function ifAcctExists($email, $phone){
            $query = "SELECT email FROM users WHERE email = ? OR phone = ?";
            $binder = array("ss", "$email", "$phone");
            $no = $this->read($query, $binder);
            if($no->num_rows > 0){
                $this->errMessage = "Duplicate registration is not allowed";
                return true;
            }
            //return false;
        }
        
        public function register($userDetails){
            $first_name = $_POST['fname'];
            $last_name = $_POST['lname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $username = $_POST['username'];
            $password = sha1($_POST['password']);
            $user_type_id = $_POST['usertype'];
            //check if email or phone exists
            if($this->ifAcctExists($email, $phone)){
                $data['message'] = $this->errMessage;
                return $data;
            }
            $query = "INSERT INTO users(first_name, last_name,email, phone, user_name, password, user_type_id,
                                         status) VALUES (?,?,?,?,?,?,?,?)";
            $binder = array("ssssssss", "$first_name", "$last_name", "$email", "$phone", "$username", "$password", 
                                            "$user_type_id", "NV");
            if($this->create($query, $binder)){
                $data['success'] = true;
                $data['message'] = "Registration Successful";
                // $link = "activate.php?t=".sha1($email);
                // $message = "<a href=$link>activate</a>";
                // $subject = "Account Activation";
                
            }
            return $data;
            $this->conn->close();

        }

        public function activateAccount($email){
            $query = "UPDATE users SET status = ? WHERE email = ?";
            $binder = array("ss", "activated", "$email");
            return $this->update($query, $binder);
        }

        public function login($userDetails){
            $username = $_POST['username'];
            $password = sha1($_POST['password']);
            $query = "SELECT * FROM users WHERE (user_name = ? OR email = ?) AND password = ?";//include option to check status
            $binder = array("sss", "$username", "$username", "$password");
            $stmt = $this->read($query, $binder);
            if($stmt->num_rows == 1){
                $st = $stmt->fetch_assoc();
                $data['message'] = $st;
                $data['success'] = true;
                $_SESSION['user'] = $st['user_id'];
                 
            }else{
                $data['success'] = false;
                $data['message'] = "Login failed";
            }
            return $data;
            $this->conn->close();
        }

        public function loadUserTypes(){
            $query = "SELECT user_type_id, user_type FROM user_type";
            return $this->read2($query);
            $this->conn->close();

        }

        public function userDetails(){
            $user_id = $_SESSION['user'];
            $data = array();
            $query = "SELECT email, first_name, last_name, phone, status, user_id, user_name,
            user_type_id, passport, school, sport, user_type FROM users JOIN user_type USING(user_type_id) WHERE user_id = ?";
            $binder = array("s", "$user_id");
            $stmt = $this->read($query, $binder);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }
    }

?>