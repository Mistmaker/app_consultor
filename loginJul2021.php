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
setlocale(LC_TIME, 'spanish');

$hoy = date("Y-m-d");

// echo json_encode($request);

try {
    $database = new Database();
    $database->query("SELECT *,'' as ExpToken FROM usuarios WHERE NUMERO_RUC= :ruc AND CLAVE= :clave");
    $database->bind('ruc', $request->NUMERO_RUC);
    $database->bind('clave', $request->CLAVE);
    $rows = $database->single();

    if ($rows) {
        $id = $rows['id'];
        $database->query("SELECT * FROM menu_usuario where id_usuario = :id");
        $database->bind(':id', $id);
        $menus = $database->resultset();
        if (count($menus) <= 0) {
            $database->query("INSERT INTO menu_usuario VALUES (null,1," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,2," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,3," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,4," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,5," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,6," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,7," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,8," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,9," . $id . ",'2')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,10," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,11," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,12," . $id . ",'1')");
            $Hecho = $database->execute();
            sleep(1);
        }
    }

    $database->closeConnection();

    if ($rows) {
        $rows['token'] = $rows['id'] . date("YmdHis");
        $rows['ExpToken'] = date("YmdHis", strtotime('1 hour'));
        $respuesta =  json_encode($rows);

        $database = new Database();
        $database->query("UPDATE usuarios set ULTIMO_INICIO_SESION = :hoy, CUENTA_SESION=CUENTA_SESION+1 WHERE id = :idUsuario");
        $database->bind(':hoy', $hoy);
        $database->bind(':idUsuario', $rows["id"]);
        $database->execute();
        $database->closeConnection();
    } else {
        $respuesta =  json_encode(array('err' => true, 'mensaje' => "Usuario o contraseña incorrectos"));
    }
} catch (\Throwable $th) {
    //throw $th;
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th));
}

echo $respuesta;
