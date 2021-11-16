<?php
// Retorna un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database class
include 'classes/database.class.php';

if (isset($_GET['emisor'])) {
    $emisor = htmlentities($_GET['emisor']);
}

if (isset($_GET['receptor'])) {
    $receptor = htmlentities($_GET['receptor']);
}


$database = new Database();
$database->query("SELECT id, receptor, emisor, renta, iva FROM reg_ret_third WHERE emisor='$emisor' and receptor='$receptor'");
$rows = $database->single();
$database->closeConnection();

echo json_encode($rows);