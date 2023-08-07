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

include_once './vendor/autoload.php';

use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $uname = $data->nombre_usuario;
    $pass = $data->contrasena;
    $conn = new mysqli('database-ininbioapp.cxr1cv8i9xxa.us-east-1.rds.amazonaws.com', 'admin', 'Ininbio123', 'IninbioSystems');

    $sql = $conn->query("SELECT * FROM registros WHERE nombre_usuario = '$uname'");
    if ($sql->num_rows > 0) {
        $user = $sql->fetch_assoc();
        if (password_verify($pass, $user['contrasena'])) {
            $key = "YOUR_SECRET_KEY"; // JWT KEY
            $issuedAt   = new DateTimeImmutable();
            $expire     = $issuedAt->modify('+30 minutes')->getTimestamp();      // Add 60 seconds
            $payload = array(
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'apellidos' => $user['apellidos'],
                'correo' => $user['correo'],
                'contrasena' => $user['contrasena'],
                'nombre_usuario' => $user['nombre_usuario'],
                'tequilera' => $user['id_tequilera'],
                'exp' => $expire
            );

            $token = JWT::encode($payload, $key, 'HS256');
            http_response_code(200);
            echo json_encode(array('token' => $token, 'usuario' => $user['nombre_usuario'],
                'tequilera' => $user['id_tequilera']));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Login Failed!'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Login Failed!'));
    }
}
else {
    http_response_code(400);
}
?>
