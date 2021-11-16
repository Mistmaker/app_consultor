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

    if (isset($request->ruc) && isset($request->correo) ) {

        $database = new Database();

        $database->query('SELECT * FROM usuarios WHERE EMAIL = :correo AND NUMERO_RUC = :ruc');
        $database->bind('correo', $request->correo);
        $database->bind('ruc', $request->ruc);
        $rows = $database->single();
        $database->closeConnection();

        if($rows){

            // Enviando resultado al correo
            $mail = new PHPMailer();
            $mail->IsSMTP();
            
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->SMTPAutoTLS = false; 
            $mail->Port = 25; 

            $mail->SMTPDebug  = 0;
            // $mail->SMTPAuth   = TRUE;
            // $mail->SMTPSecure = "tls";
            // $mail->Port       = 587;
            // $mail->Host       = "smtp.gmail.com";
            $mail->Username   = "app.elconsultor@gmail.com";
            $mail->Password   = "consultor2021";

            $mail->IsHTML(true);
            $mail->AddAddress($request->correo, $rows["NOMBRES"]);
            $mail->SetFrom("app.elconsultor@gmail.com", "El consultor");
            $mail->AddReplyTo("app.elconsultor@gmail.com", "El consultor");
            // $mail->AddCC("cc-recipient-email", "cc-recipient-name");
            $mail->Subject = "Recuperación de contraseña";
            $content = '
            Estimado(a) <b>'.$rows["NOMBRES"] .'</b> <br> 
            Su clave para la aplicación El Consultor es:
            <h3> '.$rows["CLAVE"] .' </h3>
            <br><br>
            Por favor no responder a este mensaje y no comparta su clave con nadie.
            ';

            $mail->MsgHTML($content);

            $mail->Encoding = 'base64';
            $mail->CharSet = 'UTF-8';
            if (!$mail->Send()) {
                $respuesta = json_encode(array('err' => true, 'mensaje' => "Error enviando resultados al correo."), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                var_dump($mail);
            } else {
                // $respuesta = json_encode(array('err' => false, 'mensaje' => "Correo enviado correctamente"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            // Fin de envio al correo

            $respuesta = json_encode($rows);
        }

    } else {
        $respuesta = json_encode(array('err' => true, 'mensaje' => 'No se recibió ningún dato'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;
