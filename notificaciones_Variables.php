<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');
// $method = $_SERVER['REQUEST_METHOD'];
// if ($method == "OPTIONS") {
//     die();
// }
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'notificaciones_Push.php';
require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$ultimoDato;
$penultimoDato;
$concat = "";
$concat1 = "";
$concat2 = "";
$temperatura;
$ph;
$tempMayor;
$tempMenor;
$phMayor;
$phMenor;
$tequilera = $_GET['tequilera'];

$tinas = $conexion->prepare("SELECT DISTINCT num_tina AS num_tina FROM IninbioSystems.tinas WHERE id_tequilera = '$tequilera'");
$tinas->execute();
$resultado = $tinas->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultado as $row) {
    $tina = $row['num_tina'];

    $limites = $conexion->prepare("SELECT
    temperatura,
    pH,
    tempMayor,
    tempMenor,
    phMayor,
    phMenor
    FROM
    IninbioSystems.actual
    WHERE
    id_tina = '$tina'
        AND id_tequilera = '$tequilera'
    ORDER BY id DESC LIMIT 1;");
    $limites->execute();
    while ($rows = $limites->fetchAll(PDO::FETCH_ASSOC)) {
        $tempMayor = $rows[0]["tempMayor"];
        $tempMenor = $rows[0]["tempMenor"];
        $phMayor = $rows[0]["phMayor"];
        $phMenor = $rows[0]["phMenor"];
        $temperatura = $rows[0]["temperatura"];
        $ph = $rows[0]["pH"];

    // $statement = $conexion->prepare("SELECT id_tina FROM actual WHERE id_tina = $tina AND(temperatura >= '$tempMayor' OR temperatura <= '$tempMenor') AND id_tequilera = 1 ORDER BY fecha DESC LIMIT 0,1");
    // $statement->execute();
    // $result = $statement->rowCount();
    //$results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
    //print_r($results1);

    // if ($result > 0) {
    //     $results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
    //     http_response_code(200);
    if($temperatura >= $tempMayor || $temperatura <= $tempMenor){
        $concat = $concat . "#" . $tina . ",";
        // echo json_encode($results1);
        //echo "#".$numero_tina.", ";
        // $GLOBALS["numero_tina"]=$GLOBALS["numero_tina"]." #"$GLOBALS["numero_tina"].",";
        //$numero_tina = json_encode($results1[0]['id_tina']);
    } else {
        // http_response_code(400);
        //echo 'No Hay Datos';
    }

    // $statement = $conexion->prepare("SELECT id_tina FROM actual WHERE id_tina = $tina AND(pH >= '$phMayor' OR pH <= '$phMenor') AND id_tequilera = 1 ORDER BY fecha DESC LIMIT 0,1");
    // $statement->execute();
    // $result = $statement->rowCount();
    //$results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
    //print_r($results1);

    // if ($result > 0) {
    //     $results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
    //     http_response_code(200);
    
    if($ph >= $phMayor || $ph <= $phMenor){
        $concat1 = $concat1 . "#" . $tina . ",";
        // echo json_encode($results1);
    } else {
        // http_response_code(400);
        // echo 'No Hay Datos';
    }

    $statement1 = $conexion->prepare("SELECT brix AS w, id_tina FROM actual WHERE id_tina = '$tina' AND id_tequilera = '$tequilera' ORDER BY fecha ASC");
    $statement1->execute();
    $results2 = $statement1->fetchAll(PDO::FETCH_ASSOC);
    $ultimoDato = end($results2);
    $penultimoDato = array_values(array_slice($results2, -2))[0];

    if ($ultimoDato != 0 && $penultimoDato != 0) {
        if ($ultimoDato['w'] === $penultimoDato['w']) {
            http_response_code(200);
            $concat2 = $concat2 . "#" . $row['num_tina'] . ",";
            // echo json_encode($row['num_tina']);
        }
    } else {
        // http_response_code(400);
        // echo 'Valores Vienen Vacíos En La Tina' . $tina;
    }
}
}

$canal;
if($tequilera == 1){
    $canal = '/topics/IninbioSystem';
} else if($tequilera == 2){
    $canal = '/topics/Joya';
} else if($tequilera == 3){
    $canal = '/topics/AlmaMexico';
}

$numero_tina = $concat;
echo $numero_tina;
notificacionTemperatura();

$numero_tina1 = $concat1;
echo $numero_tina1;
notificacionPH();

$numero_tina2 = $concat2;
echo $numero_tina2;
notificacionBrix();
?>