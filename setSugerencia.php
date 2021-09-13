<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
// $idUsuario = '';

try {

    if ( isset($request->sugerencia) ) {

        // $database = new Database();
        // $database->query("SELECT * FROM usuarios where NUMERO_RUC = :ruc");
        // $database->bind(':ruc', $request->ruc);
        // $row = $database->sigle();
        // $idUsuario = $row["id"];
        // $database->closeConnection();

        $database = new Database();
        $database->query('INSERT INTO sugerencias (NUMERO_RUC, SUGERENCIA, FECHA_ENVIO) 
        VALUES (:ruc, :sugerencia, :fecha)');

        $timestamp = strtotime($request->fechaEnvio);
        $request->fechaEnvio= date("Y-m-d", $timestamp);

        $database->bind(':ruc', $request->ruc);
        $database->bind(':sugerencia', $request->sugerencia);
        $database->bind(':fecha', $request->fechaEnvio);
        $Hecho = $database->execute();
        $database->closeConnection();

        if ($Hecho == "1" ) {
            $respuesta = json_encode( array('err' => false, 'mensaje' => 'Sugerencia enviada con éxito'),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else{
            $respuesta = json_encode( array('err' => true, 'mensaje' => $Hecho),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    } else {
        $respuesta = json_encode( array('err' => true, 'mensaje' => 'No se recibió ningún dato'),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $th) {
	$respuesta = json_encode( array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;


?>
