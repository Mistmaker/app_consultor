<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Credentials: true');
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type");

// echo json_encode($_FILES);

// $postdata   = file_get_contents("php://input");
// $data    = json_decode($postdata);
// // $fileName = time() .'.'. $data->fileExtension;
// $fileName = str_replace('.' . $data->fileExtension, '', $data->fileName) . '.' . $data->fileExtension;

// // $path = __DIR__ . '\\documentos\\' . $fileName;
// $path = 'documentos/' . $fileName;
// // $result = file_put_contents($path, base64_decode($data->value));
// $result = move_uploaded_file(base64_decode($data->value), $path);

// if ($result) {
//     $datos = array('err' => false, "ruta" => $path);
// } else {
//     $datos = array('err' => $result);
// }

// echo json_encode($datos);

/**
 * Author : https://roytuts.com
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$result = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (isset($_FILES['file']['name'])) {
        if (0 < $_FILES['file']['error']) {
            echo 'Error during file upload ' . $_FILES['file']['error'];
        } else {
            $upload_path = 'documentos/materialcursos';
            // if (file_exists($upload_path .'/' . $_FILES['file']['name'])) {
            //     echo 'File already exists => ' . $upload_path . $_FILES['file']['name'];
            // } else {
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            // move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . $_FILES['file']['name']);
            // echo 'File successfully uploaded => "' . $upload_path . $_FILES['file']['name'];
            // $res = move_uploaded_file($_FILES['file']['tmp_name'], $upload_path .'/'. $_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . '/' . $_FILES['file']['name'])) {
                // echo 'File successfully uploaded => "' . $upload_path . $_FILES['file']['name'];
                $result = true;
            } else {
                // echo 'Error cargando => "' . $upload_path .'/'. $_FILES['file']['name']. ' - '. $_FILES['file']['tmp_name'];
                $result = false;
            }
            // }
        }
    } else {
        echo 'Please choose a file';
    }
    // echo nl2br("\n");
}

echo json_encode(array("resultado" => $result));
