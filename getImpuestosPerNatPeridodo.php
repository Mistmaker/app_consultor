<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// verifica que se reciba el parametro
if (!isset($_GET['id'])) {
	echo json_encode(array('err' => true, 'mensaje' => "Falta el Id"));
	die;
}

// limpia el parametro
$Id = htmlentities($_GET['id']);

//echo 'Id :'.$Id;

//verificar que exista en la base de datos

$database = new Database();

$database->query('SELECT * FROM impuesto_renta_pn WHERE id_periodo = :id ORDER BY fraccion_basica ASC');
$database->bind('id', $Id);
$rows = $database->resultset();
$database->closeConnection();

if ($rows) {
	echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
	echo json_encode(array('err' => true, 'mensaje' => "Periodo no tiene tabla de impuestos"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
