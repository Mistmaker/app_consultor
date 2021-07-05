<?php
//incluir la clase de la Bdd
include_once("classes/database.class.php");

// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// verifica que se reciba el parametro
if (!isset($_GET['id'])) {
	echo json_encode( array('err' => true,'mensaje'=>"Falta el Id") );
	die;
}

// limpia el parametro
$Id = htmlentities($_GET['id']);

//echo 'Id :'.$Id;

//verificar que exista en la base de datos

$database = new Database();

$database->query('SELECT * FROM noticias WHERE id = :id');
$database->bind('id', $Id);
$rows = $database->single();
$database->closeConnection();

// The Regular Expression filter
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";


// for ($i=0; $i < count($rows); $i++) { 
	// The Text you want to filter for urls
	$text = $rows["CUERPO"];

	// Check if there is a url in the text
	if(preg_match($reg_exUrl, $text, $url)) {

		// make the urls hyper links
		$text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $text);

	} else {

		// if no urls in the text just return the text
		// echo $text;

	}

	$rows["CUERPO"] = $text;

// }

echo json_encode($rows);

// if ( $rows ){
// 	echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
// }else{
// 	echo json_encode( array('err' => true,'mensaje'=>"Id no existe"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
// }

?>