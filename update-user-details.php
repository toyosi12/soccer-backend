<?php   
        if(!isset($_SESSION)){
                session_start();
        }        
        require_once 'classes/Crud.php';
        $user_id = $_SESSION['user'];
        $updateProfile = new Crud();

        function compressImage($source, $destination, $quality){
                $info = getimagesize($source);

                if ($info['mime'] == 'image/jpeg') 
                  $image = imagecreatefromjpeg($source);
              
                elseif ($info['mime'] == 'image/gif') 
                  $image = imagecreatefromgif($source);
              
                elseif ($info['mime'] == 'image/png') 
                  $image = imagecreatefrompng($source);
              
                imagejpeg($image, $destination, $quality);
        }
        if(!isset($_FILES['passport'])){
                //if not with passport
                $_POST = json_decode(file_get_contents('php://input'),true);
        }else{
                //if with passport
                $_POST = json_decode($_POST['textData'], true);
                $target_dir = "upload/";
                $target_file = $target_dir . $user_id . basename($_FILES["passport"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Check if file already exists
                if (file_exists($target_file)) {
                        $data['success'] = false;
                        $data['message'] = "Sorry, file already exists.";
                        $uploadOk = 0;
                }
                // Check file size
                // if ($_FILES["passport"]["size"] > 200000) {//500000
                //         $data['success'] = false;
                //         $data['message'] = "Sorry, your file is too large. 200kb is the maximum";
                //         $uploadOk = 0;
                // }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        $data['success'] = false;
                        $data['message'] = "Sorry, only JPG, JPEG and PNG files are allowed.";
                        $uploadOk = 0;
                }
 
        }
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $school = $_POST['school'];
        $sport = $_POST['sport'];
        if(!isset($_FILES['passport'])){
        $query = "UPDATE users SET first_name=?, 
	last_name=?,phone=?, email=?, sport = ?, school = ? WHERE user_id=?";
        $binder = array("sssssss","$fname","$lname","$phone","$email", "$school", "$sport", "$user_id");
        $data = $updateProfile->update($query,$binder);
        
        }else{
                if($uploadOk === 1){
                        compressImage($_FILES["passport"]["tmp_name"], $target_file, 40);
                            $query = "UPDATE users SET first_name=?, 
                            last_name=?,phone=?, email=?, sport = ?, school = ?, passport = ? WHERE user_id=?";
                                $binder = array("ssssssss","$fname","$lname","$phone","$email", "$sport", "$school", "$target_file", "$user_id");

                                $data = $updateProfile->update($query,$binder);
                                
        
                                }
                }
        
        echo json_encode($data);
       
?>  
     