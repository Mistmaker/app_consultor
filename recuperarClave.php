<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

try {
    $mail = new PHPMailer();
    $mail->IsSMTP();

    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "mistmaker21@gmail.com";
    $mail->Password   = "Megamanx-10";

    $mail->IsHTML(true);
    $mail->AddAddress("diegoalexander_15@hotmail.com", "diegoalexander_15@hotmail.com");
    $mail->SetFrom("mistmaker21@gmail.com", "El consultor");
    $mail->AddReplyTo("mistmaker21@gmail.com", "MakerMaster");
    // $mail->AddCC("cc-recipient-email", "cc-recipient-name");
    $mail->Subject = utf8_decode("Recuperar contraseña");
    $content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";

    $mail->MsgHTML($content);
    if (!$mail->Send()) {
        $respuesta = json_encode(array('err' => true, 'mensaje' => "Error while sending Email."), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        var_dump($mail);
    } else {
        $respuesta = json_encode(array('err' => false, 'mensaje' => "Email sent successfully"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;
