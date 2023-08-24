<?php
session_start();
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    die();
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    include_once './vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $key = $_ENV['JWT_KEY'];

    require_once 'verificacionToken.php';
    $bearer_token = get_bearer_token();
    // echo $bearer_token;
    $is_jwt_valid = is_jwt_valid($bearer_token, $key);

    if ($is_jwt_valid) {

        require_once 'conEjemplo.php';
        $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $jsonRegistros = json_decode(file_get_contents("php://input"));
        
        $idTina = $jsonRegistros->idTina;
        $tinaTR = $jsonRegistros->Consultar;
        $tinaIndividual = $jsonRegistros->tinaIndividual;
        $idTequilera = $jsonRegistros->tequilera;
        $tinasss = $jsonRegistros->tine;
        $verificarDatos;
        $respuestaSinDatos = ['mensaje' => 'No hay datos'];

        $limites = $conexion->prepare("SELECT
tempMayor,
tempMenor,
phMayor,
phMenor
FROM
IninbioSystems.actual
WHERE
id_tina = :tine
    AND id_tequilera = :tequilera
ORDER BY id DESC LIMIT 1;");
        $limites->bindParam(':tine', $tinasss);
        $limites->bindParam(':tequilera', $idTequilera);
        $limites->execute();
        while ($rows = $limites->fetchAll(PDO::FETCH_ASSOC)) {
            $tempMayor = $rows[0]["tempMayor"];
            $tempMenor = $rows[0]["tempMenor"];
            $phMayor = $rows[0]["phMayor"];
            $phMenor = $rows[0]["phMenor"];
            echo json_encode($rows, true);
        }

        if ($idTina != 0 && $idTequilera != 0) {
            $statement = $conexion->prepare("SELECT UNIX_TIMESTAMP(CONVERT_TZ(fecha, '+00:00', @@global.time_zone))*1000 as x, temperatura as y, pH AS z , brix AS w, alcvol AS v, eficiencia AS u, tempMayor, tempMenor, phMayor, phMenor FROM actual WHERE id_tina = :idTina AND id_tequilera = :idTequilera ORDER BY fecha ASC");
            $statement->bindParam(':idTina', $idTina);
            $statement->bindParam(':idTequilera', $idTequilera);
            $statement->execute();
            $verificarDatos = $statement->rowCount();
            // if($verificarDatos > 0){
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($results);
            // } else {
            //     http_response_code(200);
            //     echo json_encode($respuestaSinDatos);
            // }
        }
        if ($tinaIndividual != 0 && $tinaTR = 1 && $idTequilera != 0) {
            $statement = $conexion->prepare("SELECT UNIX_TIMESTAMP(CONVERT_TZ(fecha, '+00:00', @@global.time_zone))*1000 as x, temperatura as y, pH AS z , brix AS w, alcvol AS v, eficiencia AS u, tempMayor, tempMenor, phMayor, phMenor FROM actual WHERE id_tina = :tinaIndividual AND id_tequilera = :idTequilera ORDER BY fecha DESC LIMIT 0,1");
            $statement->bindParam(':tinaIndividual', $tinaIndividual);
            $statement->bindParam(':idTequilera', $idTequilera);
            $statement->execute();
            $verificarDatos1 = $statement->rowCount();
            // if($verificarDatos1 > 0){
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($results);
            // } else {
            //     http_response_code(200);
            //     echo json_encode($respuestaSinDatos);
            // }
        }
    } else {
        echo json_encode(array('error' => 'Access denied'));
    }
} else {
    http_response_code(400);
}

?>
