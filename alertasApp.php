<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    die();
} else if($_SERVER['REQUEST_METHOD'] == "POST"){

    require_once 'verificacionToken.php';
    $bearer_token = get_bearer_token();
    // echo $bearer_token;
    $is_jwt_valid = is_jwt_valid($bearer_token);

    if($is_jwt_valid){

require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$jsonRegistros = json_decode(file_get_contents("php://input"));
$tipoAlerta = $jsonRegistros->tipoAlerta;
$ultimoDato;
$penultimoDato;
$concat = "";
$tempMayor;
$tempMenor;
$phMayor;
$phMenor;

$tinas = $conexion->prepare("SELECT DISTINCT num_tina AS num_tina FROM IninbioSystems.tinas WHERE id_tequilera = 1");
$tinas->execute();
$resultado = $tinas->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultado as $row) {
    $tina = $row['num_tina'];

    $limites = $conexion->prepare("SELECT 
tempMayor,
tempMenor,
phMayor,
phMenor
FROM
IninbioSystems.actual
WHERE
id_tina = '$tina'
    AND id_tequilera = 1
ORDER BY id DESC LIMIT 1;");
$limites->execute();
while ($rows = $limites->fetchAll(PDO::FETCH_ASSOC)) {
    $tempMayor = $rows[0]["tempMayor"];
    $tempMenor = $rows[0]["tempMenor"];
    $phMayor = $rows[0]["phMayor"];
    $phMenor = $rows[0]["phMenor"];
}
    // echo $tina;

    if ($tipoAlerta == 1) {

        $statement = $conexion->prepare("SELECT id_tina FROM actual WHERE id_tina = $tina AND(temperatura >= '$tempMayor' OR temperatura <= '$tempMenor') AND id_tequilera = 1 ORDER BY fecha DESC LIMIT 0,1");
        $statement->execute();
        $result = $statement->rowCount();
        // $results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
        // print_r($results1);

        if ($result > 0) {
            $results1 = $statement->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            $concat = $concat . "#" . $results1['id_tina'] . ",";
            $json = trim($concat, ',');
            //$json = json_encode($results1);
        } else {
            // http_response_code(400);
            // echo 'No Hay Datos';
        }

    } else if ($tipoAlerta == 2) {

        $statement = $conexion->prepare("SELECT id_tina FROM actual WHERE id_tina = $tina AND(pH >= '$phMayor' OR pH <= '$phMenor') AND id_tequilera = 1 ORDER BY fecha DESC LIMIT 0,1");
        $statement->execute();
        $result = $statement->rowCount();
        //$results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
        // print_r($result);

        if ($result > 0) {
            $results1 = $statement->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $concat = $concat . "#" . $results1[0]['id_tina'] . ",";
            $json = trim($concat, ',');
            //echo $concat;
        } else {
            // http_response_code(400);
            // echo 'No Hay Datos';
        }

    } else if ($tipoAlerta == 3) {

        $statement1 = $conexion->prepare("SELECT brix AS w, id_tina FROM actual WHERE id_tina = '$tina' AND id_tequilera = 1 ORDER BY fecha ASC");
        $statement1->execute();
        $results2 = $statement1->fetchAll(PDO::FETCH_ASSOC);
        $ultimoDato = end($results2);
        $penultimoDato = array_values(array_slice($results2, -2))[0];
        // print_r($penultimoDato);

        if ($ultimoDato != 0 && $penultimoDato != 0) {
            if ($ultimoDato['w'] === $penultimoDato['w']) {
                http_response_code(200);
                $concat = $concat . "#" . $row['num_tina'] . ",";
                $json = trim($concat, ',');
                // echo json_encode($row['num_tina']);
            }
        } else {
            // http_response_code(400);
            // echo 'Valores Vienen Vacíos En La Tina' . $tina;
        }
    }
}
echo json_encode($json, true);
} else {
    echo json_encode(array('error' => 'Access denied'));}
} else {
    http_response_code(400);
}
?>