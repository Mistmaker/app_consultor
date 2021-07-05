<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database_tareas.class.php';

    $database = new Database();

    $database->query("SELECT * FROM tareas");
    $rows = $database->resultset();
    $database->closeConnection();

    echo json_encode($rows);

?>