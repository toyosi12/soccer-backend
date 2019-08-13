<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';
    $to = $recipient;
    $subject = $subject;
    $message = $message;
    $altmess = "";
    function sendmail($to,$nameto,$subject,$message,$altmess)  {
      $from  = "hello@ionicbasis.com";
      $mail = new PHPMailer();  
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();   
      $mail->SMTPAuth   = true;   
      $mail->Host       = "server148.web-hosting.com";
      $mail->Port       = 465;
      $mail->Username   = $from;  
      $mail->Password   = "ionicbasis";
      $mail->SMTPSecure = "ssl";     
      $mail->setFrom($from,$namefrom);   
      $mail->addCC($from,$namefrom);      
      $mail->Subject  = $subject;
      $mail->AltBody  = $altmess;
      $mail->Body = $message;
        $mail->addAddress($to, $nameto);
      return $mail->send();
    }
    $sendmail = sendmail($to, "", $subject, $message, $altmess);
?>