<?php

function strMenuActivo($strMenu1, $strMenu2) {
    if ($strMenu1 == $strMenu2) {
        $respuesta = 'class="active"';
    } else {
        $respuesta = "";
    }
    return $respuesta;
}

//SI LA VARIABLE ESTA VACIA O NO SETA DECLARADA MANDARA CERO SIEMPRE, ES COMO EL VAL DE VISUAL BASIC 6.0

function esCero($value) {

    if (empty($value)) {
        return "0";
    } else {
        return $value;
    }
}

// CONVIERTE FECHA HTML5 A MYSQLDATETIME 
function fechaMySQLDateTimeHTML5($fecha) {

    return date("Y-m-d", strtotime($fecha))  ." ". date("H:i:s", strtotime($fecha));
}
// CONVIERTE FECHA MYSQLDATETIME A HTML5
function fechaMySQLADateHTML5($fecha) {

    return date("Y-m-d", strtotime($fecha));
}

//FECHA SQL PARA GUARDAR EN BASE DE DATOS

function fechaSQL($fecha) {

    return date("Ymd", strtotime($fecha));
}

// CONVIERTE FECHA MYSQLDATETIME A HTML5
function fechaMySQLADateTimeHTML5($fecha) {

    return date("Y-m-d", strtotime($fecha)) . "T" . date("H:i:s", strtotime($fecha));
}

function agregarMinutos($fecha, $minutos) {


    return date("Y/m/d h:i:s", strtotime($fecha . "+ $minutos minutes"));

    /*
      $fecha1= new DateTime($fecha);
      $fecha1->add(new DateInterval('PT10H30S'));
      return $date->format('Y-m-d H:i:s') . "\n";
     * 
     */
}

function fechaHoraActualSQL($fecha) {

    return date("Y-m-d h:i:s ", strtotime($fecha));
}


function fechaHoraActualSQLFinal($fecha) {

    return date("Y-m-d  ", strtotime($fecha))."23:59:59";
}


//CONVIERTE LA FECHA EN PERIODO
function fechaPeriodo($fecha) {

    return date("Ym", strtotime($fecha));
}

//OBTIENE FECHA ACTUAL
function fechaActual() {

    return date("Y/m/d");
}

//OBTIENE FECHA ACTUAL QUITAR DIAS
function fechaActualRestarDias($dias) {

    $fechaActual = date("Y/m/d");
    return date("Y/m/d",strtotime($fechaActual."- 30 days")); 
}

//OBTIENE FECHA ACTUAL
function fechaActualGuion() {

    return date("Y-m-d");
}


//OBTIENE FECHA HORA ACTUAL

function fechaHoraActual() {

    return date("Y-m-d H:i:s ", time());
}

//DIFERENCIA ENTRE MINUTOS
function diferenciaMinutos($fecha_i, $fecha_f) {
    $minutos = (strtotime($fecha_i) - strtotime($fecha_f)) / 60;

    $minutos = abs($minutos);
    $minutos = floor($minutos);

    return $minutos;
}

function strSellar($llave, $password, $cadenaOriginal) {


    $archivoPem = "/tmp/llave.key.pem";
    $comando = "openssl pkcs8 -inform DER -in $llave -passin pass:$password -out $archivoPem";

    exec($comando);
    $sello = "ok";

    //Sellar
    $archivo = openssl_pkey_get_private(file_get_contents($archivoPem));
    $sig = "";
    openssl_sign($cadenaOriginal, $sig, $archivo, OPENSSL_ALGO_SHA256);

    $sello = base64_encode($sig);

    return $sello;
}

//SOLO DIA
function dia($fecha) {

    return date("d", strtotime($fecha));
}

//SOLO MES
function mes($fecha) {

    return date("m", strtotime($fecha));
}

//SOLO AÑO
function año($fecha) {

    return date("Y", strtotime($fecha));
}

/**
 * Fecha Humanizada
 * @param type $fecha
 * @return type
 */
function fechaHumanizada($fecha) {
    
    $dias["Monday"] = lang('boilerplateDates.Monday');
    $dias["Tuesday"] = lang('boilerplateDates.Tuesday');
    $dias["Wednesday"] = lang('boilerplateDates.Wednesday');
    $dias["Thursday"] = lang('boilerplateDates.Thursday');
    $dias["Friday"] = lang('boilerplateDates.Friday');
    $dias["Saturday"] =lang('boilerplateDates.Saturday');
    $dias["Sunday"] = lang('boilerplateDates.Sunday');
    
    $meses["January"] = lang('boilerplateDates.January');
    $meses["February"] =lang('boilerplateDates.February');
    $meses["March"] = lang('boilerplateDates.March');
    $meses["April"] = lang('boilerplateDates.April');
    $meses["May"] = lang('boilerplateDates.May');
    $meses["June"] = lang('boilerplateDates.June');
    $meses["July"] = lang('boilerplateDates.July');
    $meses["August"] = lang('boilerplateDates.August');
    $meses["September"] = lang('boilerplateDates.September"');
    $meses["October"] = lang('boilerplateDates.October');
    $meses["November"] = lang('boilerplateDates.November');
    $meses["December"] = lang('boilerplateDates.December');
    
    $fechaHumanizada = $dias[date("l", strtotime($fecha))] 
                        .", "
                        .date("d", strtotime($fecha))." de "
                        . $meses[date("F", strtotime($fecha))] . " de "
                        .date("Y", strtotime($fecha));
                            
    return  $fechaHumanizada;
}

//GENERA UUID
function generaUUID() {


    $uuid = service('uuid');
    $uuid4 = $uuid->uuid4();
    $string = $uuid4->toString();

    return $string;
}

function satinizar($var, $type) {
    switch ($type) {
        case 'html':
            $safe = htmlspecialchars($var);
            break;
        case 'sql':
            $safe = mysql_real_escape_string($var);
            break;
        case 'file':
            $safe = preg_replace('/(\/|-|_)/', '', $var);
            break;
        case 'shell':
            $safe = escapeshellcmd($var);
            break;
        default:
            $safe = htmlspecialchars($var);
    }
    return $safe;
}

function limpiaCadena($cadena) {

    $cadena = str_replace('"', "", $cadena);
    $cadena = str_replace('\n', "", $cadena);
    $cadena = str_replace('\t', "", $cadena);
    $cadena = trim($cadena);

    $cadena = preg_replace("[\n|\r|\n\r]", "", $cadena);

    $descripcion = preg_replace("[\n|\r|\n\r]", "", $descripcion);

    return $cadena;
}


// CONVIERTE FECHA MYSQLDATETIME A HTML5
function fechaParaMigraciones($fecha) {

    return date("Y-m-d", strtotime($fecha)) . date("His", strtotime($fecha));
    
}