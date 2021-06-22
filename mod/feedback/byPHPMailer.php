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
    $mail->SMTPDebug  = 0; //SMTP::DEBUG_SERVER;                //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->CharSet    = 'UTF-8';
    $mail->Host       = $web_host;                              //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = stored_value('site_owner_email');        //SMTP username
    if(empty($mail->Username)) die("'site_owner_email' is not set.");
    $mail->Password   = stored_value('site_owner_password');    //SMTP password
    if(empty($mail->Password)) die("'site_owner_password' is not set.");
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom($e, $nm );
//    die("$e, $nm");
    $mail->addAddress($to, '');                            //Add a recipient
//    $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo($e, $nm);
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
} catch (Exception $e) {
    $rz = translate('feedback_notsent');
}