<?php

session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    die();
} else if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    require 'vendor/autoload.php';

$jsonRegistros = json_decode(file_get_contents("php://input"));
$correo = filter_var($jsonRegistros->correo, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
$body = "<img src=https://i.postimg.cc/8Pr0z1sx/logo-sistema.png>"; 

include 'conEjemplo.php';

$queryusuario = mysqli_prepare($conn, "SELECT nombre_usuario, pass FROM registros WHERE correo = ?");
mysqli_stmt_bind_param($queryusuario, "s", $correo);
mysqli_stmt_execute($queryusuario);
$resultado = mysqli_stmt_get_result($queryusuario);
$nr = mysqli_num_rows($resultado);
if ($nr > 0) {
    $mostrar = mysqli_fetch_array($resultado);
    $enviarpass = $mostrar['pass'];
    $enviarusuario = $mostrar['nombre_usuario'];
    $mail = new PHPMailer(true);
    $mail->IsSMTP();

    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "andrescbtis84@gmail.com";
    $mail->Password = "rvlrsufhcmqsqpjy";
    $mail->CharSet = 'UTF-8';
    $mail->AddAddress($correo, $correo);
    $mail->SetFrom("andrescbtis84@gmail.com", "Ininbio System");
    $mail->Subject = "SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA";
    //$mail->Body = '<img src="http://3.138.161.216:80/Catalogos/logo_sistema.png">';
    //$mail->addStringAttachment(file_get_contents("http://3.138.161.216:80/Catalogos/logo_sistema.png"), "logo_sistema.png");
    //$mail->AddEmbeddedImage('./Catalogos/logo_sistema.png', 'logo_sistema', 'logo_sistema.png');
    //$mail->AddEmbeddedImage('./Catalogos/logo_sistema.png', 'logo_sistema');
    //$mail->AddEmbeddedImage('https://i.postimg.cc/8Pr0z1sx/logo-sistema.png', 'logo_2u');
    //$mail->addStringAttachment(file_get_contents("https://i.postimg.cc/8Pr0z1sx/logo-sistema.png"), "logo_sistema.png");
    $mail->IsHTML(true);
    $mail->Body = "<html>
    <body>
    <img src = 'https://i.postimg.cc/3wD5DKVP/logo-sistema.jpg' alt = 'Logo Ininbio' height='100px' width='100px'>
    </body>
    </html>";
    $content = "¡Hola " . $enviarusuario . "!<br> Tu password es: " . $enviarpass . "<br>
    Por favor no compartas este correo con ninguna otra persona.<br>" . "<br>" . $body;

    $mail->MsgHTML($content);

    if (!$mail->Send()) {
        echo "Error while sending Email.";
        var_dump($mail);
    } else {
        echo "Email sent successfully";
    }
} else {
    http_response_code(400);
    echo "El usuario no existe";
}
} 
else {
    http_response_code(400);
}
