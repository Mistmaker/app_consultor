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
    $database->query('SELECT * FROM repositorio_archivos WHERE id = :id');
    $database->bind('id', $Id);
    $rows = $database->single();
} else {
    $database->query("SELECT r.*,e.LINK_ICONO,t.NOMBRE AS TipoNombre FROM repositorio_archivos r inner join extension_archivos e on e.id = r.ID_EXTENSION inner join tipo_repositorio t on t.id = r.ID_TIPO");
    $rows = $database->resultset();
}

$database->closeConnection();

echo json_encode($rows);
