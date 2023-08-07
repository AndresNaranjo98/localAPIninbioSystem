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

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $uname = filter_var($data->nombre_usuario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $pass = filter_var($data->contrasena, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $conn = new mysqli($_ENV['HOST_DB'], $_ENV['USER_DB'], $_ENV['PASS_DB'], $_ENV['NAME_DB']);

    $stmt = $conn->prepare("SELECT * FROM registros WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['contrasena'])) {
            $key = $_ENV['JWT_KEY']; // JWT KEY
            $issuedAt   = new DateTimeImmutable();
            $expire     = $issuedAt->modify('+2 days')->getTimestamp();      // Add 60 seconds
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
