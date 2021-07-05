<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    try {
        $database = new Database();

        $database->query("SELECT * FROM categorias WHERE id IN (SELECT ID_PADRE FROM categorias) ORDER BY id");
        $rows = $database->resultset();
        $database->closeConnection();

        $datos = array();

        for ($x = 0; $x < count($rows); $x++)
        {
            $database = new Database();
            $database->query("SELECT * FROM categorias WHERE ID_PADRE =".$rows[$x]["id"]);
            $enlaces = $database->resultset();
            $database->closeConnection();
            $datos[] = array('NOMBRE' => $rows[$x]["NOMBRE"], 'IMAGEN_LINK' => $rows[$x]["IMAGEN_LINK"], 'categorias' => $enlaces);
        }

        echo json_encode($datos);

    } catch (\Throwable $th) {
        throw $th;
    }

    // $database = new Database();

    // $database->query("SELECT * FROM categorias");
    // $rows = $database->resultset();
    // $database->closeConnection();

    // echo json_encode($rows);

?>