<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../mail/Exception.php';
require '../mail/PHPMailer.php';
require '../mail/SMTP.php';

function sendMail($email,$fname,$lname){
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug  = 0; 
    $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
    $mail->Port = 587; // TLS only
    $mail->SMTPSecure = 'tls'; // ssl is deprecated
    $mail->SMTPAuth = true;
    $mail->Username = 'musicaland.info@gmail.com'; // email
    $mail->Password = ''; // password
    $mail->setFrom('musicaland.info@gmail.com', 'MusicaLand'); // From email and name
    $mail->addAddress($email, $fname); // to email and name
    $mail->Subject = 'Welcome to MusicaLand';
    $mail->msgHTML("<h3>Welcome to MusicaLand</h3><div>Thank you for becoming a client at Musicaland!</div>");
    $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
    try{

        $mail->Send();
    } 
    catch (phpmailerException $e) {
        echo $e->errorMessage();  //PHPMailer error messages
    } 
    catch (Exception $e) {
        echo $e->getMessage();  // other error messages
    }
}
