<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    try {
        $database = new Database();

        $database->query("SELECT * FROM categoria_cuestionario WHERE id_categoria IN (SELECT id_padre FROM categoria_cuestionario WHERE activo='1') ORDER BY id_categoria");
        $rows = $database->resultset();
        $database->closeConnection();

        $datos = array();

        for ($x = 0; $x < count($rows); $x++)
        {
            $database = new Database();
            $database->query("SELECT * FROM categoria_cuestionario WHERE id_padre =".$rows[$x]["id_categoria"] . " AND activo='1'");
            $subCategoria = $database->resultset();
            $database->closeConnection();
            $rows[$x]["evaluaciones"] = $subCategoria;
        }
        echo json_encode($rows);

    } catch (\Throwable $th) {
        throw $th;
    }

?>