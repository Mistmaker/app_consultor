<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

try {

    if (isset($request->idusuario)){
        
        $database = new Database();
        $database->query("SELECT * FROM usuarios where id = :id");
        $database->bind(':id', $request->idusuario);
        $usuario = $database->single();
        $database->closeConnection();

        $mail = new PHPMailer();
        $mail->IsSMTP();

        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "app.elconsultor@gmail.com";
        $mail->Password   = "consultor2021";

        $mail->IsHTML(true);
        $mail->AddAddress($usuario["EMAIL"], $usuario["EMAIL"]);
        $mail->SetFrom("app.elconsultor@gmail.com", "El consultor");
        $mail->AddReplyTo("app.elconsultor@gmail.com", "El consultor");
        // $mail->AddCC("cc-recipient-email", "cc-recipient-name");
        $mail->Subject = utf8_decode("Tus obligaciones");
        $content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";
        $html = "
            <div> Estimado(a): $request->razonsocial </div> <br>
            <div> Se le envía los sus obligaciones las cuales fueron generadas desde la app el consultor </div> <br>
        ";

        // Datos de la cabecera
        $html .= "
            <div> Ruc: $request->ruc </div> <br>
            <div> Razón Social: $request->razonsocial </div> <br>
            <div> Nombre Comercial: $request->nombrecomercial </div> <br>
            <div> Actividad Comercial: $request->actividad </div> <br>
            <div> Agente Retención: $request->agenteretencion </div> <br>
            <div> Contribuyente Especial: $request->contribuyenteespecial </div> <br>
            <div> Empresa Fantasma: $request->empresafantasma </div> <br>
            <div> Microempresa: $request->microempresa </div> <br>
            <hr>
            <div> Tipo de contribuyente: " . ($request->tipocontribuyente == 'pnno' ?  'PN No Obligado' : $request->tipocontribuyente == 'pnob' ? 'PN Obligado' : ('Persona Jurídica ' . ($request->conlucro == true ? 'con fines de lucro' : ($request->sinlucro == true ? 'sin fines de lucro' : '') )  ) ) . "</div> <br>
        ";

        if ($request->tipocontribuyente == 'pnno' || $request->tipocontribuyente == 'pnob') { 
            $html .= "<div> Afiliado al IESS: ". ($request->afiliado == 'S' ? 'Si' : 'No' ) ."</div> <br>";
            $html .= "<div> Régimen: ". ($request->regimen == 'G' ? 'General' : 'RISE' ) ."</div> <br>";
            $html .= "<div> Supera monto de activos para declaración patrimonial: ". ($request->superaMonto == 'S' ? 'Si' : 'No' ) ."</div> <br>";
        }
        if ($request->tipocontribuyente == 'pj') { 
            if ($request->conlucro == true) { 
                $html .= "<div> Contrata Auditoría: ". ($request->contrataAuditoria == 'S' ? 'Si' : 'No' ) ."</div> <br>";
            }
            if ($request->sinlucro == true) { 
            }
        }
        
        $html .= "<div> Región: ". ($request->region == 'C' ? 'Costa' : 'Sierra' ) ."</div> <br>";
        $html .= "<div> # de Afiliados: ". $request->numeroAfiliados ."</div> <br>";
        $html .= "<br>";
        $html .= "<div> A continuación se detallan tus obligaciones para el periodo actual:</div> <br>";
        
        $html .= "
        <table style='width: 100%;'>
            <thead>
                <tr>
                    <th>Entidad</th>
                    <th>Actividad</th>
                    <th>Fecha Presentación</th>
                </tr>
            </thead>
        <tbody>";
        
        foreach ($request->tareas as $mes) {
            $html .= "
            <tr style='height: 40px; background-color: #002060; color: white; text-align:center;' >
                <td colspan='3'> <b>". monthName($mes->mes) ." </b> </td>
            </tr>
            ";
            foreach ($mes->tareas as $tarea) {
                $html .= "
                <tr>
                    <td> $tarea->entidad </td>
                    <td> $tarea->actividad </td>
                    <td>". date('d/m/Y',strtotime($tarea->fecha))  ."</td>
                </tr>
                ";
                if ( count($tarea->tareasHijas) > 0){
                  $html .= "
                    <tr style='height: 25px; background-color: #25478A; color: white; text-align:center;' >
                        <td colspan='3'> <b> Subtareas para $tarea->actividad </b> </td>
                    </tr>
                  ";
                  foreach ($tarea->tareasHijas as $th) {
                    $html .= "
                    <tr>
                        <td> $th->entidad </td>
                        <td> $th->actividad </td>
                        <td>". date('d/m/Y',strtotime($th->fecha))  ."</td>
                    </tr>
                    ";
                  }
                  $html .= "
                    <tr style='height: 15px; background-color: #25478A; color: white; text-align:center;' >
                        <td colspan='3'> <b> </b> </td>
                    </tr>
                  ";
                }

            }
        }

        $html .= "</tbody></table>";

        // echo $html;

        // $mail->MsgHTML($content);
        $mail->MsgHTML($html);

        $mail->Encoding = 'base64';
        $mail->CharSet = 'UTF-8';

        if (!$mail->Send()) {
            $respuesta = json_encode(array('err' => true, 'mensaje' => "Error al enviar el correo"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            var_dump($mail);
        } else {
            $respuesta = json_encode(array('err' => false, 'mensaje' => "Listado de obligaciones enviado a tu correo ".$usuario["EMAIL"].", si no aparece revisa en spam"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    }

} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;

function monthName($mes = '')
{
    $nombre = '';
    switch ($mes) {
        case '1':
            $nombre = 'Enero';
            break;
          case '2':
            $nombre = 'Febrero';
            break;
          case '3':
            $nombre = 'Marzo';
            break;
          case '4':
            $nombre = 'Abril';
            break;
          case '5':
            $nombre = 'Mayo';
            break;
          case '6':
            $nombre = 'Junio';
            break;
          case '7':
            $nombre = 'Julio';
            break;
          case '8':
            $nombre = 'Agosto';
            break;
          case '9':
            $nombre = 'Septiembre';
            break;
          case '10':
            $nombre = 'Octubre';
            break;
          case '11':
            $nombre = 'Noviembre';
            break;
          case '12':
            $nombre = 'Diciembre';
            break;
          default:
            $nombre = '';
            break;
    }
    return $nombre;
}