<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['id'])) {
    $Id = htmlentities($_GET['id']);
}

$database = new Database();

if (isset($Id)) {
    $database->query('SELECT * FROM grupo_enlace WHERE id = :id');
    $database->bind('id', $Id);
    $rows = $database->single();
} else {
    $database->query("SELECT * FROM grupo_enlace");
    $rows = $database->resultset();
}

$database->closeConnection();

echo json_encode($rows);
