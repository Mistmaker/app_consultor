<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    if (isset($_GET['id'])) {
        $Id = htmlentities($_GET['id']);
    }

    try {
        $database = new Database();

        if (isset($Id)) {
            $database->query("SELECT * FROM categoria_cuestionario WHERE id_categoria = :id");
            $database->bind('id', $Id);
            $rows = $database->single();
        }else{
            $database->query("SELECT * FROM categoria_cuestionario WHERE id_padre IS NULL ORDER BY id_categoria");
            $rows = $database->resultset();
            $database->closeConnection();
        }

        echo json_encode($rows);

    } catch (\Throwable $th) {
        throw $th;
    }

?>