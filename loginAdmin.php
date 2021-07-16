<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retiorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

date_default_timezone_set('America/Bogota');

// echo json_encode($request);

try {
    $database = new Database();
    $database->query("SELECT id,'' as ExpToken FROM users WHERE usu_login= :usuario AND usu_password= :clave AND usu_estado='1'");
    $database->bind('usuario', $request->usu_login);
    $database->bind('clave', $request->usu_password);
    $rows = $database->single();
    $database->closeConnection();

    if ($rows) {
        $rows['token'] = $rows['usu_id'] . date("YmdHis");
        $rows['ExpToken'] = date("YmdHis", strtotime('1 hour'));
        $respuesta =  json_encode($rows);
    } else {
        $respuesta =  json_encode(array('err' => true, 'mensaje' => "Usuario no existe"));
    }
} catch (\Throwable $th) {
    //throw $th;
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th));
}

echo $respuesta;
