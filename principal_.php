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
$nombre = filter_var($jsonRegistros->nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$apellidos = filter_var($jsonRegistros->apellidos, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$correo = filter_var($jsonRegistros->correo, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
$contrasena = filter_var($jsonRegistros->contrasena, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$nombre_usuario = filter_var($jsonRegistros->nombre_usuario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tequilera = filter_var($jsonRegistros->id_tequilera, FILTER_SANITIZE_NUMBER_INT);
$hashed = password_hash(filter_var($contrasena, FILTER_SANITIZE_FULL_SPECIAL_CHARS), PASSWORD_DEFAULT);

require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$sentencia = $conexion->prepare("SELECT * FROM registros WHERE nombre_usuario = :nombre_usuario");
$sentencia->bindParam(':nombre_usuario', $nombre_usuario);
$sentencia->execute();
if ($sentencia->rowCount() > 0) {
    echo 'Este Usuario Ya Existe, Inténtalo Nuevamente';
} else {
    $sentencia1 = $conexion->prepare("INSERT INTO registros(nombre, apellidos, correo, contrasena, pass, nombre_usuario, id_tequilera)
    VALUES (:nombre, :apellidos, :correo, :hashed, :contrasena, :nombre_usuario, :tequilera)");
        $sentencia1->bindParam(':nombre', $nombre);
        $sentencia1->bindParam(':apellidos', $apellidos);
        $sentencia1->bindParam(':correo', $correo);
        $sentencia1->bindParam(':hashed', $hashed);
        $sentencia1->bindParam(':contrasena', $contrasena);
        $sentencia1->bindParam(':nombre_usuario', $nombre_usuario);
        $sentencia1->bindParam(':tequilera', $tequilera);
    $resultado = $sentencia1->execute();
    class Result
    {}

    $response = new Result();
    if ($response->resultado = 'OK') {

        $response->mensaje = 'Usuario Registrado';

    } else {
        $response->mensaje = 'Error Al Registrar Nuevo Usuario, Inténtalo Nuevamente';
    }
    echo json_encode($response);
}
?>