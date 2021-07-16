<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    $database = new Database();

    $database->query("SELECT * FROM tipo_repositorio WHERE ID_PADRE IS NOT NULL");
    $rows = $database->resultset();
    $database->closeConnection();

    echo json_encode($rows);

?>