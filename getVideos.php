<?php
    // Retorna un json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Include database class
    include 'classes/database.class.php';


    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    $database = new Database();

    $database->query("SELECT * FROM videos_guia_practica");
    $rows = $database->resultset();
    $database->closeConnection();




    $database = new Database();

    $database->query("SELECT * FROM grupo_video WHERE id IN (SELECT ID_GRUPO FROM videos_guia_practica) ORDER BY id");
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
        $database->query("SELECT * FROM videos_guia_practica WHERE ID_GRUPO =".$rows[$x]["id"]);
        $videos = $database->resultset();
        $database->closeConnection();
        // $row = $row + array('videos' => $videos);




        for ($i=0; $i < count($videos); $i++) { 
            // The Text you want to filter for urls
            $text = $videos[$i]["DESCRIPCION"];
    
            // Check if there is a url in the text
            if(preg_match($reg_exUrl, $text, $url)) {
    
                // make the urls hyper links
                $text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $text);
    
            } else {
    
                // if no urls in the text just return the text
                // echo $text;
    
            }
    
            $videos[$i]["DESCRIPCION"] = $text;
    
        }








        $datos[] = array('grupo' => $rows[$x], 'videos' => $videos);
    }













    


    // for ($i=0; $i < count($rows); $i++) { 
    //     // The Text you want to filter for urls
    //     $text = $rows[$i]["DESCRIPCION"];

    //     // Check if there is a url in the text
    //     if(preg_match($reg_exUrl, $text, $url)) {

    //         // make the urls hyper links
    //         $text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $text);

    //     } else {

    //         // if no urls in the text just return the text
    //         // echo $text;

    //     }

    //     $rows[$i]["DESCRIPCION"] = $text;

    // }

    echo json_encode($datos);

?>