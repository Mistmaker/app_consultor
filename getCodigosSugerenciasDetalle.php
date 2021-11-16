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
$database->query("SELECT id, detalle, porcentaje, codigo_formulario, codigo_anexo, categoria FROM listado_codigos_sugerencias WHERE categoria = '$tipo'");
$rows = $database->resultset();
$database->closeConnection();

echo json_encode($rows);