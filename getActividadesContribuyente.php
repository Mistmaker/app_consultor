<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database_parametros.class.php';

    if (isset($_GET['id'])) {
        $Id = htmlentities($_GET['id']);
    }

    $database = new DatabaseParam();

    if (isset($Id)) {
        $database->query("SELECT * FROM age_actividad A INNER JOIN age_entidad E ON E.ENT_CODIGO = A.ENT_CODIGO WHERE A.ACT_CODIGO=:id");
        $database->bind('id', $Id);
        $rows = $database->single();
    } else {
        $database->query("SELECT * FROM age_actividad A INNER JOIN age_entidad E ON E.ENT_CODIGO = A.ENT_CODIGO");
        $rows = $database->resultset();
    }

    echo json_encode($rows);

?>