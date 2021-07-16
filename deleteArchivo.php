<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['id'])) {
    $Id = htmlentities($_GET['id']);
}


if (isset($Id)) {
    $database = new Database();

    $database->query('SELECT * FROM repositorio_archivos WHERE id = :id');
    $database->bind('id', $Id);
    $rows = $database->single();

    if ($rows) {
        $datos = explode("/", $rows["LINK_ARCHIVO"]);
        $archivo = end($datos);
        unlink('documentos/' . $archivo);
    }

    $database->query('DELETE FROM repositorio_archivos WHERE id = :id');
    $database->bind('id', $Id);
    $Hecho = $database->execute();
    $database->closeConnection();
    if ($Hecho == "1") {
        $respuesta = json_encode(array('err' => false, 'mensaje' => 'Registro eliminado', 'id' => $Id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $respuesta = json_encode(array('err' => true, 'mensaje' => $Hecho), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
} else {
    $respuesta = json_encode(array('err' => true, 'mensaje' => 'No se recibió el id'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}


echo $respuesta;
