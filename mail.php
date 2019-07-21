<?php
        require_once 'classes/config.php';

       function send_mail($email,$message,$subject){
       //from home.php
      
require_once "Mail.php";
require_once ('Mail/mime.php');
require_once ('classes/config.php');

//these are the three lines you need to edit
$from = webmail;//webmail of sender
$to = $email;//email of recipient here
$subject =$subject;//subject of the email here


$headers = array ('From' => $from,'To' => $to, 'Subject' => $subject);

$text = ''; // text versions of email.
$html = "<html><body>$message <br></body></html>"; // html versions of email.

$crlf = "\n";

$mime = new Mail_mime($crlf);
$mime->setTXTBody($text);
$mime->setHTMLBody($html);

//do not ever try to call these lines in reverse order
$body = $mime->get();
$headers = $mime->headers($headers);

$host = webmail_host; // all scripts must use localhost
$username = webmail; //  your email address (same as webmail username)
$password = webmail_pass; // your password (same as webmail password)

$smtp = Mail::factory('smtp', array ('host' => $host, 'auth' => true,
'username' => $username,'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
}
else {
//header("Location:progress_resp.php");
$resp['success'] = true;

}
}
send_mail($email,$message,$subject);
?>