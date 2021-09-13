<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';

    // verifica que se reciba el parametro
    if (!isset($_GET['id'])) {
        echo json_encode( array('err' => true,'mensaje'=>"Falta el Id") );
        die;
    }

    // limpia el parametro
    $Id = htmlentities($_GET['id']);

    $database = new Database();

    $database->query("SELECT * FROM banco_preguntas WHERE id_categoria = :id AND activo = 1");
    $database->bind('id', $Id);
    $rows = $database->resultset();
    $database->closeConnection();

    for ($x = 0; $x < count($rows); $x++)
    {
        $database = new Database();
        $database->query("SELECT * FROM banco_preguntas_respuestas WHERE activo = 1 AND id_pregunta =".$rows[$x]["id_pregunta"]);
        $respuestas = $database->resultset();
        $database->closeConnection();
        $rows[$x]["respuestas"] = $respuestas;
    }

    echo json_encode($rows);

?>