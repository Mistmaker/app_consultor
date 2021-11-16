<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['codigo'])) {
    $codigo = htmlentities($_GET['codigo']);
}


$database = new Database();
$database->query("SELECT id, detalle, porcentaje, codigo_anexo, categoria FROM listado_codigos_sugerencias WHERE codigo_formulario='$codigo' limit 1");
$rows = $database->single();
$database->closeConnection();

echo json_encode($rows);