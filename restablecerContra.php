<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$jsonRegistros = json_decode(file_get_contents("php://input"));
$contrasena = $jsonRegistros->contrasena;
$usuario = $jsonRegistros->nombre_usuario;
$hashed = password_hash($contrasena, PASSWORD_DEFAULT);

require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$sentencia = $conexion->prepare("SELECT * FROM registros WHERE nombre_usuario = '$usuario'");
$sentencia->execute();
if ($sentencia->rowCount() > 0) {
    $sentencia1 = $conexion->prepare("UPDATE registros SET contrasena = ? WHERE nombre_usuario = '$usuario'");
    $resultado1 = $sentencia1->execute(array($hashed));
    class Result
    {}

    $response = new Result();
    if ($response->resultado = 'OK') {

        $response->mensaje = 'datos modificados';

    } else {
        $response->mensaje = 'datos no modificados';
    }
    echo json_encode($response);
} else {
    echo "El usuario no existe";
}
?>