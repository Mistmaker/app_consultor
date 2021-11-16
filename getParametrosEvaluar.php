<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['obligado'])) {
    $obligado = htmlentities($_GET['obligado']);
}

if (isset($_GET['especial'])) {
    $especial = htmlentities($_GET['especial']);
}

if (isset($_GET['micro'])) {
    $micro = htmlentities($_GET['micro']);
}

if (isset($_GET['agente'])) {
    $agente = htmlentities($_GET['agente']);
}

$database = new Database();
$database->query("SELECT respuesta FROM reg_ret_third WHERE obligado='$obligado' and especial='$especial' and microempresa='$micro' and agenteret='$agente'");
$rows = $database->single();
$database->closeConnection();

echo json_encode($rows);