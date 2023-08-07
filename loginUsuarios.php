<?php

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
            $payload = array(
                'id' => $user['id'],
                'nombre_usuario' => $user['nombre_usuario'],
                'contrasena' => $user['contrasena'],
            );

            $token = JWT::encode($payload, $key);
            http_response_code(200);
            echo json_encode(array('token' => $token, 'nombre_usuario' => $user['nombre_usuario']));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Login Failed!'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Login Failed!'));
    }
}
?>