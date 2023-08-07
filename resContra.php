<?php
$cabeceras = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$cabeceras .= 'From: Ininbio Systems <andrescbtis84@gmail.com>' . "\r\n";

include 'conEjemplo.php';

$correo = 'andres_naranjo98@protonmail.com';

$queryusuario = mysqli_query($conn, "SELECT nombre_usuario, pass FROM registros WHERE correo = '$correo'");
$nr = mysqli_num_rows($queryusuario);
if ($nr == 1) {
    $mostrar = mysqli_fetch_array($queryusuario);
    $enviarpass = $mostrar['pass'];
    $enviarusuario = $mostrar['nombre_usuario'];

    $paracorreo = $correo;
    $titulo = "SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA";
    $mensaje = "Hola ". $enviarusuario."<br> Tu password es: " . $enviarpass."<br>Por favor no compartas este correo con ninguna otra persona.";
    //$tucorreo = "From: andrescbtis84@gmail.com";

    if (mail($paracorreo, $titulo, $mensaje, $cabeceras)) {
        echo "Contraseña enviada correctamente";
    } else {
        echo "Error al enviar la contraseña";
    }
} else {
    echo "Este correo no se encuentra registrado a ninguna cuenta";
}
?>