<?php
session_start();
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Registros Monitoreo.xlsx"');
header('Cache-Control: max-age=0');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include_once './vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$key = $_ENV['JWT_KEY'];

require_once 'verificacionToken.php';
    $bearer_token = get_bearer_token();
    // echo $bearer_token;
    $is_jwt_valid = is_jwt_valid($bearer_token, $key);

    if ($is_jwt_valid) {

require 'vendor/autoload.php';
require_once 'conEjemplo.php';
$conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$idTina = filter_var($_GET["idTina"], FILTER_SANITIZE_NUMBER_INT);
$tequilera = filter_var($_GET["tequilera"], FILTER_SANITIZE_NUMBER_INT);
$idioma = filter_var($_GET["idioma"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$primer = filter_var($_GET["primer"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$segunda = filter_var($_GET["segunda"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tempMayor = [];
$tempMenor = [];
$phMayor = [];
$phMenor = [];

$limites = $conexion->prepare("SELECT 
tempMayor,
tempMenor,
phMayor,
phMenor
FROM
IninbioSystems.monitoreo
WHERE
id_tina = :idTina
    AND id_tequilera = :tequilera
    -- AND fecha BETWEEN '$primer' AND '$segunda'
ORDER BY id ASC;");
$limites->bindParam(':idTina', $idTina);
$limites->bindParam(':tequilera', $tequilera);
$limites->execute();
$rows = $limites->fetchAll(PDO::FETCH_ASSOC);
for($i = 0; $i < count($rows); $i++) {
    $tempMayor = $rows[$i]["tempMayor"];
    $tempMenor = $rows[$i]["tempMenor"];
    $phMayor = $rows[$i]["phMayor"];
    $phMenor = $rows[$i]["phMenor"];

    // echo 'Pasó '.$i . ' Vez ';
    echo $tempMayor.' , ';
    echo $tempMenor.' , ';
}
if ($idioma === 'es') {

    $query = $conexion->prepare("SELECT id, DATE_FORMAT(fecha,'%Y-%m-%d %H:%i:%s') AS fecha, brix, alcvol, eficiencia, temperatura, 
    pH, volumen, id_tina, id_tequilera, tempMayor, tempMenor, phMayor, phMenor
    FROM IninbioSystems.monitoreo WHERE id_tina = :idTina AND id_tequilera = :tequilera AND fecha BETWEEN :primer 
    AND :segunda 
    ORDER BY id ASC;");
        $query->bindParam(':idTina', $idTina);
        $query->bindParam(':tequilera', $tequilera);
        $query->bindParam(':primer', $primer);
        $query->bindParam(':segunda', $segunda);
    $query->execute();
    $excel = new Spreadsheet();
    $HojaActiva = $excel->getActiveSheet();
    $HojaActiva->setTitle("Registros Tina N° $idTina");

    $HojaActiva->getColumnDimension('A')->setAutoSize(true);
    $HojaActiva->setCellValue('A1', 'Fecha');
    $HojaActiva->getColumnDimension('B')->setAutoSize(true);
    $HojaActiva->setCellValue('B1', 'Temperatura °C');
    $HojaActiva->getColumnDimension('C')->setAutoSize(true);
    $HojaActiva->setCellValue('C1', 'pH');
    $HojaActiva->getColumnDimension('D')->setAutoSize(true);
    $HojaActiva->setCellValue('D1', '°Brix');
    $HojaActiva->getColumnDimension('E')->setAutoSize(true);
    $HojaActiva->setCellValue('E1', '%Alc.Vol');
    $HojaActiva->getColumnDimension('F')->setAutoSize(true);
    $HojaActiva->setCellValue('F1', 'Eficiencia de Fermentación (%)');
    $HojaActiva->getColumnDimension('G')->setAutoSize(true);
    $HojaActiva->setCellValue('G1', 'Temperatura Mayor');
    $HojaActiva->getColumnDimension('H')->setAutoSize(true);
    $HojaActiva->setCellValue('H1', 'Temperatura Menor');
    $HojaActiva->getColumnDimension('I')->setAutoSize(true);
    $HojaActiva->setCellValue('I1', 'pH Mayor');
    $HojaActiva->getColumnDimension('J')->setAutoSize(true);
    $HojaActiva->setCellValue('J1', 'pH Menor');
    $HojaActiva->getColumnDimension('K')->setAutoSize(true);
    $HojaActiva->setCellValue('K1', 'Número de Tina');
    $HojaActiva->getColumnDimension('L')->setAutoSize(true);
    $HojaActiva->setCellValue('L1', 'LIMITE PH MENOR');

    $fila = 2;
    $contentStartRow = 2;
    $lastBrix = null;

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        for ($x = 0; $x < count($rows); $x++) {
            $HojaActiva->setCellValue('A' . $fila, $rows[$x]['fecha']);
            $HojaActiva->setCellValue('B' . $fila, $rows[$x]['temperatura']);
            $HojaActiva->setCellValue('C' . $fila, $rows[$x]['pH']);
            $HojaActiva->setCellValue('D' . $fila, $rows[$x]['brix']);
            $HojaActiva->setCellValue('E' . $fila, $rows[$x]['alcvol']);
            $HojaActiva->setCellValue('F' . $fila, $rows[$x]['eficiencia']);
            $HojaActiva->setCellValue('G' . $fila, $rows[$x]['tempMayor']);
            $HojaActiva->setCellValue('H' . $fila, $rows[$x]['tempMenor']);
            $HojaActiva->setCellValue('I' . $fila, $rows[$x]['phMayor']);
            $HojaActiva->setCellValue('J' . $fila, $rows[$x]['phMenor']);
            $HojaActiva->setCellValue('K' . $fila, $rows[$x]['id_tina']);
            $HojaActiva->setCellValue('L' . $fila, $rows[$x]['phMenor']);
            if (!is_null($lastBrix) && $rows[$x]['brix'] == $lastBrix) {
                $HojaActiva->getStyle('D' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
            }
            $lastBrix = $rows[$x]['brix'];
            $fila++;
    // }
    $HojaActiva->removeRow($fila, 2);
    $HojaActiva->getStyle('A:L')->getAlignment()->setHorizontal('center');
    $HojaActiva->getStyle('A1:L1')->getFont()->setBold(true);
//$HojaActiva->getStyle('A:F')->getAllBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF000000'));

//CONDICIONAL PH
    $condicional = new Conditional();
    $condicional->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($rows[$x]['phMenor']);

    $condicional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

//CONDICIONAL PH
    $condicional1 = new Conditional();
    $condicional1->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($rows[$x]['phMayor']);

    $condicional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional1);
    $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA
    $condicional2 = new Conditional();
    $condicional2->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($rows[$x]['tempMayor']);

    $condicional2->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional2->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional2);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA
    $condicional3 = new Conditional();
    $condicional3->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($rows[$x]['tempMenor']);

    $condicional3->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional3->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional3);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);
    } //CIERRA EL FOR
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
    ob_end_clean();
    $writer->save('php://output');
    exit();
// }
} else if ($idioma === 'en') {
    $query = $conexion->prepare("SELECT id, DATE_FORMAT(fecha,'%Y-%m-%d %H:%i:%s') AS fecha, brix, alcvol, eficiencia, temperatura, 
    pH, volumen, id_tina, id_tequilera
    FROM IninbioSystems.monitoreo WHERE id_tina = :idTina AND id_tequilera = :tequilera AND fecha BETWEEN :primer 
    AND :segunda 
    ORDER BY id ASC;");
        $query->bindParam(':idTina', $idTina);
        $query->bindParam(':tequilera', $tequilera);
        $query->bindParam(':primer', $primer);
        $query->bindParam(':segunda', $segunda);
    $query->execute();
    $excel = new Spreadsheet();
    $HojaActiva = $excel->getActiveSheet();
    $HojaActiva->setTitle("Records Tina N° $idTina");

    $HojaActiva->getColumnDimension('A')->setAutoSize(true);
    $HojaActiva->setCellValue('A1', 'Date');
    $HojaActiva->getColumnDimension('B')->setAutoSize(true);
    $HojaActiva->setCellValue('B1', 'Temperature °C');
    $HojaActiva->getColumnDimension('C')->setAutoSize(true);
    $HojaActiva->setCellValue('C1', 'pH');
    $HojaActiva->getColumnDimension('D')->setAutoSize(true);
    $HojaActiva->setCellValue('D1', '°Brix');
    $HojaActiva->getColumnDimension('E')->setAutoSize(true);
    $HojaActiva->setCellValue('E1', '%Alc.Vol');
    $HojaActiva->getColumnDimension('F')->setAutoSize(true);
    $HojaActiva->setCellValue('F1', 'Fermentation Efficiency (%)');
    $HojaActiva->getColumnDimension('G')->setAutoSize(true);
    $HojaActiva->setCellValue('G1', 'Tina Number');

    $fila = 2;
    $contentStartRow = 2;
    $lastBrix = null;

    while ($rows = $query->fetchAll(PDO::FETCH_ASSOC)) {
        for ($x = 0; $x < count($rows); $x++) {
            $HojaActiva->setCellValue('A' . $fila, $rows[$x]['fecha']);
            $HojaActiva->setCellValue('B' . $fila, $rows[$x]['temperatura']);
            $HojaActiva->setCellValue('C' . $fila, $rows[$x]['pH']);
            $HojaActiva->setCellValue('D' . $fila, $rows[$x]['brix']);
            $HojaActiva->setCellValue('E' . $fila, $rows[$x]['alcvol']);
            $HojaActiva->setCellValue('F' . $fila, $rows[$x]['eficiencia']);
            $HojaActiva->setCellValue('G' . $fila, $rows[$x]['id_tina']);
            if (!is_null($lastBrix) && $rows[$x]['brix'] == $lastBrix) {
                $HojaActiva->getStyle('D' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
            }
            $lastBrix = $rows[$x]['brix'];
            $fila++;
        }
    }
    $HojaActiva->removeRow($fila, 2);
    $HojaActiva->getStyle('A:G')->getAlignment()->setHorizontal('center');
    $HojaActiva->getStyle('A1:G1')->getFont()->setBold(true);
//$HojaActiva->getStyle('A:F')->getAllBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF000000'));

//CONDICIONAL PH < 4
    $condicional = new Conditional();
    $condicional->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($phMenor);

    $condicional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional);
    $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL PH > 4.8
    $condicional1 = new Conditional();
    $condicional1->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($phMayor);

    $condicional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional1);
    $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA > 35
    $condicional2 = new Conditional();
    $condicional2->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($tempMayor);

    $condicional2->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional2->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional2);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA < 26
    $condicional3 = new Conditional();
    $condicional3->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($tempMenor);

    $condicional3->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional3->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional3);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
    ob_end_clean();
    $writer->save('php://output');
    exit();

    class Result
    {}

    $response = new Result();
    if ($response->resultado = 'OK') {

        $response->mensaje = 'Excel Generado';

    } else {
        $response->mensaje = 'No Se Pudo Generar El Excel';
    }
} else if ($idioma === 'de') {
    $query = $conexion->prepare("SELECT id, DATE_FORMAT(fecha,'%Y-%m-%d %H:%i:%s') AS fecha, brix, alcvol, eficiencia, temperatura, 
    pH, volumen, id_tina, id_tequilera
    FROM IninbioSystems.monitoreo WHERE id_tina = :idTina AND id_tequilera = :tequilera AND fecha BETWEEN :primer 
    AND :segunda 
    ORDER BY id ASC;");
        $query->bindParam(':idTina', $idTina);
        $query->bindParam(':tequilera', $tequilera);
        $query->bindParam(':primer', $primer);
        $query->bindParam(':segunda', $segunda);
    $query->execute();
    $excel = new Spreadsheet();
    $HojaActiva = $excel->getActiveSheet();
    $HojaActiva->setTitle("Eintragungen Tina Nr. $idTina");

    $HojaActiva->getColumnDimension('A')->setAutoSize(true);
    $HojaActiva->setCellValue('A1', 'Datum');
    $HojaActiva->getColumnDimension('B')->setAutoSize(true);
    $HojaActiva->setCellValue('B1', 'Temperatur °C');
    $HojaActiva->getColumnDimension('C')->setAutoSize(true);
    $HojaActiva->setCellValue('C1', 'pH');
    $HojaActiva->getColumnDimension('D')->setAutoSize(true);
    $HojaActiva->setCellValue('D1', '°Brix');
    $HojaActiva->getColumnDimension('E')->setAutoSize(true);
    $HojaActiva->setCellValue('E1', '%Alc.Vol');
    $HojaActiva->getColumnDimension('F')->setAutoSize(true);
    $HojaActiva->setCellValue('F1', 'Effizienz der Gärung (%)');
    $HojaActiva->getColumnDimension('G')->setAutoSize(true);
    $HojaActiva->setCellValue('G1', 'Tina-Nummer');

    $fila = 2;
    $contentStartRow = 2;
    $lastBrix = null;

    while ($rows = $query->fetchAll(PDO::FETCH_ASSOC)) {
        for ($x = 0; $x < count($rows); $x++) {
            $HojaActiva->setCellValue('A' . $fila, $rows[$x]['fecha']);
            $HojaActiva->setCellValue('B' . $fila, $rows[$x]['temperatura']);
            $HojaActiva->setCellValue('C' . $fila, $rows[$x]['pH']);
            $HojaActiva->setCellValue('D' . $fila, $rows[$x]['brix']);
            $HojaActiva->setCellValue('E' . $fila, $rows[$x]['alcvol']);
            $HojaActiva->setCellValue('F' . $fila, $rows[$x]['eficiencia']);
            $HojaActiva->setCellValue('G' . $fila, $rows[$x]['id_tina']);
            if (!is_null($lastBrix) && $rows[$x]['brix'] == $lastBrix) {
                $HojaActiva->getStyle('D' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
            }
            $lastBrix = $rows[$x]['brix'];
            $fila++;
        }
    }
    $HojaActiva->removeRow($fila, 2);
    $HojaActiva->getStyle('A:G')->getAlignment()->setHorizontal('center');
    $HojaActiva->getStyle('A1:G1')->getFont()->setBold(true);
//$HojaActiva->getStyle('A:F')->getAllBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF000000'));

//CONDICIONAL PH < 4
    $condicional = new Conditional();
    $condicional->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($phMenor);

    $condicional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional);
    $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL PH > 4.8
    $condicional1 = new Conditional();
    $condicional1->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($phMayor);

    $condicional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional1);
    $HojaActiva->getStyle('C' . $contentStartRow . ':C' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA > 35
    $condicional2 = new Conditional();
    $condicional2->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
        ->addCondition($tempMayor);

    $condicional2->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional2->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional2->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional2);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

//CONDICIONAL TEMPERATURA < 26
    $condicional3 = new Conditional();
    $condicional3->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition($tempMenor);

    $condicional3->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
    $condicional3->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
    $condicional3->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLACK);

    $contentEndRow = $fila - 1;
    $estiloCondicional = $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->getConditionalStyles();

    array_push($estiloCondicional, $condicional3);
    $HojaActiva->getStyle('B' . $contentStartRow . ':B' . $contentEndRow)
        ->setConditionalStyles($estiloCondicional);

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
    ob_end_clean();
    $writer->save('php://output');
    exit();

    class Result
    {}

    $response = new Result();
    if ($response->resultado = 'OK') {

        $response->mensaje = 'Excel Generado';

    } else {
        $response->mensaje = 'No Se Pudo Generar El Excel';
    }
}
// }
    } else {
        echo json_encode(array('error' => 'Access denied to this resource'));
    }
?>