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
        } else {
            $database->query("SELECT *,(SELECT c.nombre FROM categoria_cuestionario c WHERE c.id_categoria = categoria_cuestionario.id_padre) as grupo FROM categoria_cuestionario WHERE id_padre IS NOT NULL");
            $rows = $database->resultset();
            $database->closeConnection();
        }

        echo json_encode($rows);

    } catch (\Throwable $th) {
        throw $th;
    }

?>