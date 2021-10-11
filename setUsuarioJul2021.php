<?php
include_once("classes/database.class.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

$hoy = date("Y-m-d");
$id = 0;

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

try {

    if (isset($request->NUMERO_RUC)) {


        $database = new Database();
        $database->query("SELECT * FROM usuarios where NUMERO_RUC = :ruc");
        $database->bind(':ruc', $request->NUMERO_RUC);
        $rows = $database->resultset();
        $database->closeConnection();
        if (count($rows) > 0) {
            $id = $rows[0]["id"];
            // RUC YA REGISTRADO, TALVEZ VOLVIÓ A INSTALAR LA APLICACIÓN O LA COLOCA EN UN NUEVO DISPOSITIVO
            // OPCIONES: ACTUALIZAR O DEVOLVER DATOS
            $database = new Database();
            $database->query('UPDATE usuarios SET  
                            NOMBRES = :nombres,
                            TELEFONO = :telefono,
                            EMAIL = :email,
                            CIUDAD = :ciudad,
                            EMAIL_ALTERNO = :emailAlterno,
                            TIPO_USO = :tipoUso,
                            UID = :uid,
                            ORIGEN = :origen,
                            CLAVE = :clave,
                            OS = :os
                            WHERE NUMERO_RUC = :ruc');
            $database->bind(':ruc', $request->NUMERO_RUC);
            $database->bind(':nombres', $request->NOMBRES);
            $database->bind(':telefono', $request->TELEFONO);
            $database->bind(':ciudad', $request->CIUDAD);
            $database->bind(':email', $request->EMAIL);
            $database->bind(':emailAlterno', $request->EMAIL_ALTERNO);
            $database->bind(':tipoUso', $request->TIPO_USO);

            if (isset($request->CLAVE)) {
                // $database->bind(':uid', $request->UID === null ? $rows["UID"] : $request->UID);
                if (isset($request->UID)) {
                    $database->bind(':uid', $request->UID);
                } else {
                    $database->bind(':uid', $rows[0]["UID"]);
                }
                $database->bind(':origen', $request->ORIGEN === null ? $rows["ORIGEN"] : $request->ORIGEN);
                $database->bind(':clave', $request->CLAVE);
                $database->bind(':os', $request->OS === null ? $rows["OS"] : $request->OS);
            } else if (isset($request->UID) || isset($request->ORIGEN)) {
                if (isset($request->UID)) {
                    $database->bind(':uid', $request->UID);
                } else {
                    $database->bind(':uid', $rows[0]["UID"]);
                }
                $database->bind(':origen', $request->ORIGEN === null ? $rows["ORIGEN"] : $request->ORIGEN);
                $database->bind(':clave', null);
                $database->bind(':os', $request->OS === null ? $rows["OS"] : $request->OS);
            } else {
                $database->bind(':uid', null);
                $database->bind(':origen', null);
                $database->bind(':clave', null);
                $database->bind(':os', null);
            }
        } else {

            $database = new Database();

            $database->query("
            INSERT INTO usuarios
            (NUMERO_RUC, NOMBRES, TELEFONO, CIUDAD, EMAIL, EMAIL_ALTERNO, TIPO_USO, PREMIUM, UID, FECHA_REGISTRO, FECHA_CADUCIDAD, CLAVE, ORIGEN, OS)
            VALUES
            ( :ruc, :nombres, :telefono, :ciudad, :email, :emailAlterno, :tipoUso, null, :uid, :fecha, null, :clave, :origen, :os)
            ");

            // $database->bind(':fecha', $request->FECHA_REGISTRO);
            $database->bind(':fecha', $hoy);
            $database->bind(':ruc', $request->NUMERO_RUC);
            $database->bind(':nombres', $request->NOMBRES);
            $database->bind(':telefono', $request->TELEFONO);
            $database->bind(':ciudad', $request->CIUDAD);
            $database->bind(':email', $request->EMAIL);
            $database->bind(':emailAlterno', $request->EMAIL_ALTERNO);
            $database->bind(':tipoUso', $request->TIPO_USO);
            if (isset($request->CLAVE)) {
                $database->bind(':uid', $request->UID);
                $database->bind(':origen', $request->ORIGEN);
                $database->bind(':clave', $request->CLAVE);
                $database->bind(':os', $request->OS);
            } else if (isset($request->UID) || isset($request->ORIGEN)) {
                if (isset($request->UID)) {
                    $database->bind(':uid', $request->UID);
                } else {
                    $database->bind(':uid', 'no set');
                }
                $database->bind(':origen', $request->ORIGEN === null ? $rows["ORIGEN"] : $request->ORIGEN);
                $database->bind(':clave', null);
                $database->bind(':os', $request->OS === null ? $rows["OS"] : $request->OS);
            } else {
                $database->bind(':uid', null);
                $database->bind(':origen', null);
                $database->bind(':clave', null);
                $database->bind(':os', null);
            }
        }


        $Hecho = $database->execute();

        if (count($rows) <= 0) {
            $id = $database->lastInsertId();
        }

        $database->query("SELECT * FROM menu_usuario where id_usuario = :id");
        $database->bind(':id', $id);
        $menus = $database->resultset();
        if (count($menus) <= 0) {
            $database->query("INSERT INTO menu_usuario VALUES (null,1," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,2," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,3," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,4," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,5," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,6," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,7," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,8," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,9," . $id . ",'3')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,10," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,11," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,12," . $id . ",'1')");
            $Hecho = $database->execute();
            $database->query("INSERT INTO menu_usuario VALUES (null,13," . $id . ",'1')");
            $Hecho = $database->execute();
        }

        sleep(1);

        $database->closeConnection();

        if ($Hecho == "1") {
            $respuesta = json_encode(array('err' => false, 'mensaje' => 'Usuario Registrado', 'id' => $id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $respuesta = json_encode(array('err' => true, 'mensaje' => $Hecho), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }


        // $database = new Database();
        // $database->query('INSERT INTO usuarios (NUMERO_RUC, APELLIDOS, NOMBRES, TELEFONO, EMAIL, EMAIL_ALTERNO, TIPO_USO) 
        // VALUES (:ruc, :apellidos, :nombres, :telefono, :email, :emailAlterno, :tipoUso)');
        // $database->bind(':ruc', $request->ruc);
        // $database->bind(':apellidos', $request->apellidos);
        // $database->bind(':nombres', $request->nombres);
        // $database->bind(':telefono', $request->telefono);
        // $database->bind(':email', $request->email);
        // $database->bind(':emailAlterno', $request->emailAlterno);
        // $database->bind(':tipoUso', $request->tipoUso);
        // $Hecho = $database->execute();
        // $database->closeConnection();

        // if ($Hecho == "1" ) {
        // 	$respuesta = json_encode( array('err' => false, 'mensaje' => 'Usuario Registrado'),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        // }else{
        // 	$respuesta = json_encode( array('err' => true, 'mensaje' => $Hecho),JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        // }
    } else {
        $respuesta = json_encode(array('err' => true, 'mensaje' => 'No se recibió ningún dato'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $th) {
    $respuesta = json_encode(array('err' => true, 'mensaje' => $th), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo $respuesta;

// insert into menu_usuario values 
// (null,1,1230,'1'),
// (null,2,1230,'1'),
// (null,3,1230,'1'),
// (null,4,1230,'1'),
// (null,5,1230,'1'),
// (null,6,1230,'1'),
// (null,7,1230,'1'),
// (null,8,1230,'1'),
// (null,9,1230,'1'),
// (null,10,1230,'1'),
// (null,11,1230,'1'),
// (null,12,1230,'1');