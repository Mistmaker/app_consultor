<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    try {
        // $database = new Database();
        // $database->query("SELECT * FROM tipo_repositorio WHERE id IN (SELECT ID_TIPO FROM repositorio_archivos) ORDER BY id");
        // $rows = $database->resultset();
        // $database->closeConnection();

        // $datos = array();

        // for ($x = 0; $x < count($rows); $x++)
        // {
        //     $database = new Database();
        //     $database->query("SELECT * FROM repositorio_archivos WHERE ID_TIPO =".$rows[$x]["id"]);
        //     $enlaces = $database->resultset();
        //     $database->closeConnection();
        //     $datos[] = array('grupo' => $rows[$x], 'enlaces' => $enlaces);
        // }

        // echo json_encode($datos);

        $sql = "SELECT * FROM tipo_repositorio WHERE id IN (SELECT ID_PADRE FROM tipo_repositorio)";
        $sqlgrupos = "SELECT * FROM tipo_repositorio WHERE id NOT IN (SELECT id FROM tipo_repositorio WHERE id IN (SELECT ID_PADRE FROM tipo_repositorio)) AND id IN (SELECT ID_TIPO FROM repositorio_archivos)";

        $database = new Database();
        $database->query($sql);
        $rows = $database->resultset();
        $database->closeConnection();

        // echo json_encode($rows);

        $datos = array();
        $grupos = array();
        $enlacesGrupo = array();

        for ($i=0; $i < count($rows); $i++) { 
            // echo json_encode($rows[$i]["id"]);
            $database = new Database();
            $database->query("SELECT * FROM tipo_repositorio WHERE id IN (SELECT ID_TIPO FROM repositorio_archivos) AND ID_PADRE=".$rows[$i]["id"]);
            $rows2 = $database->resultset();
            $database->closeConnection();
            // echo json_encode($rows2);

            
            for ($j=0; $j < count($rows2); $j++) { 
                $database = new Database();
                $database->query("SELECT *,(SELECT LINK_ICONO FROM extension_archivos WHERE extension_archivos.id = repositorio_archivos.ID_EXTENSION) as LINK_ICONO FROM repositorio_archivos WHERE ID_TIPO=".$rows2[$j]["id"]);
                $rows3 = $database->resultset();
                // $enlacesGrupo[] = $rows3;
                $database->closeConnection();
                array_push($rows2[$j], $rows3);
            }
            array_push($rows[$i], $rows2);

            // $grupos[] = array('grupo' => $rows[$i], 'subgrupo' => $rows2, 'enlaces' => $rows3 );
        }

        echo json_encode($rows);
        // echo json_encode($rows2);
        // echo json_encode($enlacesGrupo);


    } catch (\Throwable $th) {
        throw $th;
    }

    

?>