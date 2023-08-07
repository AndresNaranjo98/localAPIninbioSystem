<?php
session_start();
class MvcController
{

    #LLAMADA A LA PLANTILLA
    #---------------------------

    public function plantilla()
    {

        if (isset($_SESSION['usuario']) && isset($_SESSION['contrasena']) && ($_SESSION['valida'] == true)) {
            include "views/template.php";
        } else {
            include "views/login.php";
        }

    }

    #INTERACCIÓN DEL USUARIO
    #---------------------------

    public function enlacesPaginasController()
    {

        /*isset significa que trae contenido.*/

        if (isset($_GET["action"])) {

            $enlacesController = $_GET["action"];

        } else {

            $enlacesController = "index";

        }

        $respuesta = EnlacesPaginas::enlacesPaginasModel($enlacesController);

        include $respuesta;
    }

}
?>