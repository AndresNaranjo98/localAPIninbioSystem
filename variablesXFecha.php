<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Cache-Control: max-age=0');

require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$tina = $_GET['numeroTina'];
$fechas;
$fechasParseadas;

$query = $conexion->prepare("SELECT * FROM valoresOutRange WHERE (variableFueraValor = 'temperatura' OR variableFueraValor = 'pH') AND id_tina = '$tina' AND id_tequilera = 1;");
$query->execute();
$result = $query->rowCount();

if ($result > 0) {
    $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    // for($i = 0; $i < count($resultado); $i++){
    //     $fechas = $resultado[$i]['fecha'];
    //     echo json_encode($fechas, true);
    // }
    echo json_encode($resultado, true); 
} else {
     http_response_code(200);
     echo 'No Hay Datos';
}

?>