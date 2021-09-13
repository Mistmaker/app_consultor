<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Methods: POST');

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

$hoy = date("Y-m-d");
$id = 0;

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

try {

    if (isset($request->id_categoria)) {
        $id = $request->id_categoria;
        $database = new Database();
        $database->query('UPDATE categoria_cuestionario SET  
                            id_padre = :id_padre,
                            nombre = :nombre,
                            cantidad_preguntas = :cantidad_preguntas
                            WHERE id_categoria = :id');
        $database->bind(':id', $id);
        $database->bind(':id_padre', $request->id_padre);
        $database->bind(':nombre', $request->nombre);
        $database->bind(':cantidad_preguntas', $request->cantidad_preguntas);

        $Hecho = $database->execute();

        // actualizando o creando preguntas y respuestas

        if (isset($request->preguntas)) {

            $database->query("UPDATE banco_preguntas SET activo=0  WHERE id_categoria=:id_categoria");
            $database->bind(':id_categoria', $request->id_categoria);
            $database->execute();

            foreach ($request->preguntas as $pregunta) {

                if( isset($pregunta->id_pregunta) ) { // Pregunta ya existente - update
                    $database->query("UPDATE banco_preguntas SET id_categoria=:id_categoria, pregunta=:pregunta, activo=:activo, comentario=:comentario WHERE id_pregunta=:id_pregunta");
                    $database->bind(':id_pregunta', $pregunta->id_pregunta);
                    $database->bind(':id_categoria', $pregunta->id_categoria);
                    $database->bind(':pregunta', $pregunta->pregunta);
                    $database->bind(':comentario', $pregunta->comentario);
                    $database->bind(':activo', $pregunta->activo);
                    $database->execute();

                    if (isset($pregunta->respuestas)) {
                        foreach ($pregunta->respuestas as $respuesta) {

                            if (isset($respuesta->id_respuesta)){ // Respuesta ya existe - update

                                $database->query("UPDATE banco_preguntas_respuestas SET respuesta= :respuesta, respuesta_valida= :respuesta_valida, activo= :activo WHERE id_respuesta= :id_respuesta");
                                $database->bind(':respuesta', $respuesta->respuesta);
                                $database->bind(':respuesta_valida', $respuesta->respuesta_valida);
                                $database->bind(':activo', $respuesta->activo);
                                $database->bind(':id_respuesta', $respuesta->id_respuesta);
                                $database->execute();

                            } else { // Respuesta no existe - insert

                                $database->query("INSERT INTO banco_preguntas_respuestas (id_pregunta, respuesta, respuesta_valida) VALUES (:id_pregunta, :respuesta, :respuesta_valida)");
                                $database->bind(':id_pregunta', $respuesta->id_pregunta);
                                $database->bind(':respuesta', $respuesta->respuesta);
                                $database->bind(':respuesta_valida', $respuesta->respuesta_valida);
                                $database->execute();

                            }
                            
                        }
                    }

                }else { // pregunta no existente - insert
                    
                    $database->query("INSERT INTO banco_preguntas (id_categoria, pregunta) VALUES (:id_categoria, :pregunta)");
                    $database->bind(':id_categoria', $pregunta->id_categoria);
                    $database->bind(':pregunta', $pregunta->pregunta);
                    $database->execute();
                    $pregunta->id_pregunta= $database->lastInsertId();

                    if (isset($pregunta->respuestas)) {
                        foreach ($pregunta->respuestas as $respuesta) {
                            $respuesta->id_pregunta = $pregunta->id_pregunta;
                            $database->query("INSERT INTO banco_preguntas_respuestas (id_pregunta, respuesta, respuesta_valida) VALUES (:id_pregunta, :respuesta, :respuesta_valida)");
                            $database->bind(':id_pregunta', $respuesta->id_pregunta);
                            $database->bind(':respuesta', $respuesta->respuesta);
                            $database->bind(':respuesta_valida', $respuesta->respuesta_valida);
                            $database->execute();
                        }
                    }

                }

            }

        }

        // fin actualizando o creando preguntas y respuestas


    } else {

        $database = new Database();
        $database->query("INSERT INTO categoria_cuestionario (id_padre, nombre, cantidad_preguntas) VALUES (:id_padre, :nombre, :cantidad_preguntas)");

        $database->bind(':id_padre', $request->id_padre);
        $database->bind(':nombre', $request->nombre);
        $database->bind(':cantidad_preguntas', $request->cantidad_preguntas);

        $Hecho = $database->execute();

        $id = $database->lastInsertId();

        // guardando preguntas y respuestas

        if (isset($request->preguntas)) {

            foreach ($request->preguntas as $pregunta) {
                $pregunta->id_categoria = $id;
                $database->query("INSERT INTO banco_preguntas (id_categoria, pregunta) VALUES (:id_categoria, :pregunta)");
                $database->bind(':id_categoria', $pregunta->id_categoria);
                $database->bind(':pregunta', $pregunta->pregunta);
                $database->execute();
                $idPregunta = $database->lastInsertId();

                if (isset($pregunta->respuestas)) {
                    foreach ($pregunta->respuestas as $respuesta) {
                        $respuesta->id_pregunta = $idPregunta;
                        $database->query("INSERT INTO banco_preguntas_respuestas (id_pregunta, respuesta, respuesta_valida) VALUES (:id_pregunta, :respuesta, :respuesta_valida)");
                        $database->bind(':id_pregunta', $respuesta->id_pregunta);
                        $database->bind(':respuesta', $respuesta->respuesta);
                        $database->bind(':respuesta_valida', $respuesta->respuesta_valida);
                        $database->execute();
                    }
                }
            }

        }

        // fin guardado preguntas y respuestas
    }

    // $Hecho = $database->execute();

    // if (count($rows) <= 0) {
    //     $id = $database->lastInsertId();
    // }

    $database->closeConnection();

    if ($Hecho == "1") {
        $respuesta = json_encode(array('err' => false, 'mensaje' => 'Realizado con éxito', 'id' => $id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $respuesta = json_encode(array('err' => true, 'mensaje' => $Hecho), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;
