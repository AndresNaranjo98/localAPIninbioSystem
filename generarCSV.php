<?php

//Se asigna un nombre al archivo Excel
$filename = "Registros " . date('Y-m-d') . ".csv";

//Se establecen las cabeceras de conexión para indicar que se trabajará sobre un archivo Excel
header('Content-Type:  text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

//Se obtienen los registros desde la base de datos
$query = $conexion->query("SELECT * FROM monitoreo ORDER BY id ASC");

if ($query->rowCount() > 0) {
    $delimiter = ",";

    //Se crea un puntero para el archivo Excel
    $f = fopen('php://memory', 'w');

    //Se establecen los títulos de las cabeceras
    $fields = array('Fecha', 'Temperatura', 'pH', 'brix', 'AlcVol', 'Eficiencia');
    fputcsv($f, $fields, $delimiter);

    //Se pintan los valores obtenidos de la consulta
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $lineData = array($row['fecha'], $row['temperatura'], $row['pH'], $row['brix'], $row['alcvol'], $row['eficiencia']);
        fputcsv($f, $lineData, $delimiter);
    }

    //Variables para volver al archivo Excel
    fseek($f, 0);
    fpassthru($f);
}
exit;
?>