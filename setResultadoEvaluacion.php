<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

$hoy = date("Y-m-d H:i:s");

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

try {

    if ( isset($request->id_categoria) && !isset($request->puntaje) ) {
        $database = new Database();
        $database->query('INSERT INTO resultados_evaluacion (id_categoria, id_usuario, fecha_resultado, puntaje) 
        VALUES (:id_categoria, :id_usuario, :fecha_resultado, :puntaje)');

        $database->bind(':id_categoria', $request->id_categoria);
        $database->bind(':id_usuario', $request->id_usuario);
        $database->bind(':fecha_resultado', $hoy);
        $database->bind(':puntaje', 0);

        $Hecho = $database->execute();

        //Obteniendo el id insertado
        $id = $database->lastInsertId();

        $respuesta = json_encode( array('err' => false, 'id' => $id),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    elseif ( isset($request->id_categoria) && isset($request->puntaje) ) {

        $database = new Database();
        $database->query('UPDATE resultados_evaluacion SET puntaje = :puntaje WHERE id_resultado = :id_resultado');
        $database->bind(':puntaje', $request->puntaje);
        $database->bind(':id_resultado', $request->id_resultado);

        $Hecho = $database->execute();

        //Obteniendo el id insertado
        $id = $request->id_resultado;
        // $database = new Database();
        // $database->query('INSERT INTO resultados_evaluacion (id_categoria, id_usuario, fecha_resultado, puntaje) 
        // VALUES (:id_categoria, :id_usuario, :fecha_resultado, :puntaje)');

        // $database->bind(':id_categoria', $request->id_categoria);
        // $database->bind(':id_usuario', $request->id_usuario);
        // $database->bind(':fecha_resultado', $hoy);
        // $database->bind(':puntaje', $request->puntaje);

        // $Hecho = $database->execute();

        // //Obteniendo el id insertado
        // $id = $database->lastInsertId();

        // Guardando respuestas erroneas

        if ( isset($request->respuestasErroneas) ) {
            foreach ($request->respuestasErroneas as $respuesta) {
                $database->query('INSERT INTO resultados_evaluacion_resp (id_resultado, id_respuesta) VALUES (:id_resultado, :id_respuesta)');
                $database->bind(':id_resultado', $id);
                $database->bind(':id_respuesta', $respuesta->id_respuesta);
                $database->execute();
            }
        }

        // Fin guardado respuestas erroneas

        $database->closeConnection();

        if ($Hecho == "1" ) {
            $respuesta = json_encode( array('err' => false, 'mensaje' => 'Resultado registrado con ??xito, a su correo le llegar?? el resultado de la evaluaci??n.'),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $database = new Database();
            $database->query('SELECT * FROM usuarios WHERE id = :id');
            $database->bind('id', $request->id_usuario);
            $rows = $database->single();
            $database->closeConnection();

            $nombreUsuario = $rows["NOMBRES"];
            $correoUsuario = $rows["EMAIL"];

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
            $mail->AddAddress($correoUsuario, $nombreUsuario);
            $mail->SetFrom("app.elconsultor@gmail.com", "El consultor");
            $mail->AddReplyTo("app.elconsultor@gmail.com", "El consultor");
            // $mail->AddCC("cc-recipient-email", "cc-recipient-name");
            $mail->Subject = "Resultado evaluaci??n";
            $content = "El resultado de la evaluaci??n <b>$request->tituloEvaluacion</b> realizada el $hoy es de: <b>$request->puntaje/$request->cantidadPreguntas</b>
                <br><br>
                Por favor no responder a este mensaje.
            ";

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

        } else{
            $respuesta = json_encode( array('err' => true, 'mensaje' => $Hecho),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    } else {
        $respuesta = json_encode( array('err' => true, 'mensaje' => 'No se recibi?? ning??n dato'),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $th) {
	$respuesta = json_encode( array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;


?>
