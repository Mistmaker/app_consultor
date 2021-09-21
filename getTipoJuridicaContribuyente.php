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
        $database->query("SELECT * FROM ven_tipojuridicacliente WHERE TPJ_TIPOCLIENTE=:id");
        $database->bind('id', $Id);
        $rows = $database->resultset();
    } else {
        $database->query("SELECT * FROM ven_tipojuridicacliente");
        $rows = $database->resultset();
    }

    echo json_encode($rows);

?>