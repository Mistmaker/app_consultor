<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// verifica que se reciba el parametro
if (!isset($_GET['num'])) {
	echo json_encode( array('err' => true,'mensaje'=>"Falta el numero") );
	die;
}

// limpia el parametro
$Id = htmlentities($_GET['num']);

//echo 'Id :'.$Id;

//verificar que exista en la base de datos

$database = new Database();

$database->query('SELECT * FROM usuarios WHERE NUMERO_RUC = :num');
$database->bind(':num', $Id);
$rows = $database->single();
$database->closeConnection();

if ( $rows ){
	echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}else{
	echo json_encode( array('err' => true,'mensaje'=>"Numero no existe"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

?>