<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['tipo'])) {
    $tipo = htmlentities($_GET['tipo']);
}

$database = new Database();
$database->query("SELECT id, concepto, tipo, respuesta, iva FROM reg_ret_second WHERE concepto = '$tipo'");
$rows = $database->resultset();
$database->closeConnection();

echo json_encode($rows);