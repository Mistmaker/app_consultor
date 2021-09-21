<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['tipo'])) {
    $tipo = htmlentities($_GET['tipo']);
}

$database = new Database();
$database->query("SELECT id,imagen,redirecciona_link,estado FROM banners_publicidad WHERE estado = 1 AND dispositivo_destino = '$tipo'");
$rows = $database->resultset();
$database->closeConnection();

    for ($x = 0; $x < count($rows); $x++)
    {
        $rows[$x]["imagen"] = base64_encode($rows[$x]["imagen"]);
        // print_r(base64_encode($rows[$x]["imagen"]));
    }


echo json_encode($rows);
