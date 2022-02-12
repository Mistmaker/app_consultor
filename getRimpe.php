<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    // verifica que se reciba el parametro
    if (!isset($_GET['id'])) {
        echo json_encode( array('err' => true,'mensaje'=>"Falta el Id") );
        die;
    }
    // limpia el parametro
    $Id = htmlentities($_GET['id']);

    // Include database class
    include 'classes/database_platos.class.php';

    $database = new DatabasePlatos();

    $database->query("SELECT * FROM sistema_catastro.rimpe WHERE ruc = :id");
    $database->bind('id', $Id);
    $rows = $database->single();
    $database->closeConnection();

    echo json_encode($rows);

?>