<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// verifica que se reciba el parametro
if (!isset($_GET['idUsuario']) || !isset($_GET['idEvaluacion'])) {
	echo json_encode( array('err' => true,'mensaje'=>"Falta el Id") );
	die;
}

// limpia el parametro
$id_usuario = htmlentities($_GET['idUsuario']);
$id_categoria = htmlentities($_GET['idEvaluacion']);

//echo 'Id :'.$Id;

//verificar que exista en la base de datos

$database = new Database();

$database->query('SELECT IFNULL(COUNT(id_resultado),0) as Intentos FROM resultados_evaluacion WHERE id_categoria = :id_categoria AND id_usuario = :id_usuario');
$database->bind(':id_categoria', $id_categoria);
$database->bind(':id_usuario', $id_usuario);
$rows = $database->single();
$database->closeConnection();

if ( $rows ){
	echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}else{
	echo json_encode( array('err' => true,'mensaje'=>"Id no existe"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

?>