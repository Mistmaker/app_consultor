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
$database->query("SELECT id, validacion, parametro, respuesta FROM reg_ret_first WHERE validacion = '$tipo'");
$rows = $database->single();
$database->closeConnection();

echo json_encode($rows);