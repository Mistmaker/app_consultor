<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    try {
        $database = new Database();

        $database->query("SELECT * FROM grupo_curso WHERE id IN (SELECT ID_GRUPO FROM cursos) ORDER BY id");
        $rows = $database->resultset();
        $database->closeConnection();

        // $encabezado = array();
        $datos = array();

        // echo json_encode($rows);

        // echo count($rows);

        for ($x = 0; $x < count($rows); $x++)
        {
            // echo 'Item'.$x;
            $database = new Database();
            // $encabezado = $rows[$x];
            $database->query("SELECT * FROM cursos WHERE ID_GRUPO =".$rows[$x]["id"]);
            $enlaces = $database->resultset();
            $database->closeConnection();
            // $row = $row + array('enlaces' => $enlaces);
            $datos[] = array('grupo' => $rows[$x], 'enlaces' => $enlaces);
        }

        // $datos = $datos + $rows;

        // foreach ($rows as $row) {
        //     // echo '1';
        //     echo json_encode(array($row));
            
        //     $database = new Database();
        //     $database->query("SELECT * FROM enlaces WHERE ID_GRUPO =".$row["id"]);
        //     $enlaces = $database->resultset();
        //     $database->closeConnection();
        //     $row = $row + array('enlaces' => $enlaces);
        //     $datos = $datos + $row;
        //     // echo json_encode(array($row));
        // }

        // echo '<br>';
        echo json_encode($datos);

    } catch (\Throwable $th) {
        throw $th;
    }

    

?>