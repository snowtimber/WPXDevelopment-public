<?php
require 'PHPMailer-master/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->CharSet = 'UTF-8';
$mail->Host = '10.35.1.100';  // Specify main and backup SMTP servers
$mail->SMTPAuth = false;                               // Enable SMTP authentication
//$mail->Username = 'Logan.Meyer@wpxenergy.com';                 // SMTP username
//$mail->Password = 'pw';                           // SMTP password
//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//$mail->Port = 587;                                    // TCP port to connect to

$mail->From = 'logan.meyer@wpxenergy.com';
$mail->FromName = 'Logan Meyer';
$mail->addAddress('logan.meyer@wpxenergy.com', 'Logan Testing');     // Add a recipient
//$mail->addAddress('logan.meyer@wpxenergy.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('logan.meyer@wpxenergy.com');
$mail->addBCC('logan.meyer@wpxenergy.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold! From Logan 1</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo '<br><br>Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>
