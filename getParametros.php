<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// verifica que se reciba el parametro
if (isset($_GET['id'])) {
	// limpia el parametro
	$Id = htmlentities($_GET['id']);
}


$database = new Database();

if (isset($Id)) {
	$database->query('SELECT * FROM parametros WHERE id = :id');
	$database->bind('id', $Id);
	$rows = $database->single();
} else {
	$database->query('SELECT * FROM parametros');
	$rows = $database->resultset();
}

$database->closeConnection();

if ($rows) {
	echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
	echo json_encode(array('err' => true, 'mensaje' => "Parámetro no existe"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
