<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

$hoy = date("Y-m-d");
$id = 0;

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

try {

    if (isset($request->id)) {
        $id = $request->id;
        $database = new Database();
        $database->query('UPDATE repositorio_archivos SET  
                            ID_TIPO = :ID_TIPO,
                            ID_EXTENSION = :ID_EXTENSION,
                            NOMBRE = :NOMBRE,
                            LINK_ARCHIVO = :LINK_ARCHIVO,
                            FECHA_ACTUALIZADO = :FECHA_ACTUALIZADO 
                            WHERE id = :id');
        $database->bind(':id', $request->id);
        $database->bind(':ID_TIPO', $request->ID_TIPO);
        $database->bind(':ID_EXTENSION', $request->ID_EXTENSION);
        $database->bind(':NOMBRE', $request->NOMBRE);
        $database->bind(':LINK_ARCHIVO', $request->LINK_ARCHIVO);
        $database->bind(':FECHA_ACTUALIZADO', $hoy);
    } else {

        $database = new Database();
        $database->query("INSERT INTO repositorio_archivos(id,ID_TIPO,ID_EXTENSION,NOMBRE,LINK_ARCHIVO,FECHA_ACTUALIZADO) VALUES (:id ,:ID_TIPO ,:ID_EXTENSION ,:NOMBRE ,:LINK_ARCHIVO ,:FECHA_ACTUALIZADO )");

        $database->bind(':id', $request->id);
        $database->bind(':ID_TIPO', $request->ID_TIPO);
        $database->bind(':ID_EXTENSION', $request->ID_EXTENSION);
        $database->bind(':NOMBRE', $request->NOMBRE);
        $database->bind(':LINK_ARCHIVO', $request->LINK_ARCHIVO);
        $database->bind(':FECHA_ACTUALIZADO', $hoy);
    }

    $Hecho = $database->execute();

    if (count($rows) <= 0) {
        $id = $database->lastInsertId();
    }

    $database->closeConnection();

    if ($Hecho == "1") {
        $respuesta = json_encode(array('err' => false, 'mensaje' => 'Realizado con éxito', 'id' => $id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $respuesta = json_encode(array('err' => true, 'mensaje' => $Hecho), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;
