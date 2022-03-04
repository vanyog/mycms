<?php
// Based on example from PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $idir.'_PHPMailer/src/Exception.php';
require $idir.'_PHPMailer/src/PHPMailer.php';
require $idir.'_PHPMailer/src/SMTP.php';

//Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0; //SMTP::DEBUG_SERVER;                 //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->CharSet = 'UTF-8';
    $mail->Host       = $web_host;                              //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $fe;                                    //SMTP username
    $mail->Password   = $pw;                                    //SMTP password
    if(empty($mail->Password)) die("'userreg_password_".$d['type']."' OR 'site_owner_password' is not set.");
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom($fe, stored_value('site_owner_name','Mailer') );
    $mail->addAddress($e, '');                            //Add a recipient
//    $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo($fe, '');
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('bcc@example.com');

    //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $sb;
    $mail->Body    = $ms;
//    $mail->AltBody = $ms;

    $mail->send();
//    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}<br>";
    die;
}