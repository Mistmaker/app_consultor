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
        $database->query('UPDATE enlaces SET  
                            ID_GRUPO = :ID_GRUPO,
                            TITULO = :TITULO,
                            LINK = :LINK,
                            FECHA_AGREGADO = :FECHA_AGREGADO
                            WHERE id = :id');
        $database->bind(':id', $request->id);
        $database->bind(':ID_GRUPO', $request->ID_GRUPO);
        $database->bind(':TITULO', $request->TITULO);
        $database->bind(':LINK', $request->LINK);
        $database->bind(':FECHA_AGREGADO', $hoy);
    } else {

        

        $database = new Database();
        $database->query("INSERT INTO enlaces (id,ID_GRUPO,TITULO,LINK,FECHA_AGREGADO) VALUES (:id,:ID_GRUPO,:TITULO,:LINK,:FECHA_AGREGADO)");

        $database->bind(':id', null);
        $database->bind(':ID_GRUPO', $request->ID_GRUPO);
        $database->bind(':TITULO', $request->TITULO);
        $database->bind(':LINK', $request->LINK);
        $database->bind(':FECHA_AGREGADO', $hoy);
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
