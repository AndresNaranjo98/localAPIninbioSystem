<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require_once 'notificacionesVariables.php';
//require_once 'alertasApp.php';
require_once 'notificaciones_Variables.php';
$tina;
$numero_tina;

define('API_ACCESS_KEY', 'AAAArg8zmkk:APA91bEs2DMqpKSKdb2AfLwbpTPx8Y8wZcw-i3hwJ7TWphLZUc87HhDhiIhNIKIrdX88Zq2rxFd1sNhPYa9ACx76sen7eh54mweFrqSLvfOK6C6HPsUj9hlLZ0cb47uEl2zyq8xQkdEv');
/*$token = [
'ff_GkbgNQx6qjequUPaLMm:APA91bG0PtKjhypyZN52Abve0FH_K0hS1DGcNgEq-SSMUkje7Ag79-8yLWOeAxYgWBtT_8Cx8RmVFhvw1QdSK_J_iWd6HSVC1kRLQD1MSepB4GNWysJScWYVquM9hVOI1TdVTXhRi7Eg',
'cjofHmRKRZmJ3V-QBnWaXz:APA91bEbzafG8yYsxquCNKTezK5I3m5z9l8RJTshmj_pfT6rKu-dgSavbueBsdoNoepTgtEGTiusflEACwbrTPv9QQOcMDk4_xLfdHP7Mjw1SStbi6I7aPcRT6aPsqnl-K2KwRpRJNK_',
'dNmO845GT7CUIokrZ3KcmT:APA91bF8IICI1XN3LtyYyoYCp6f4IoQkycOqvIrZq1o_jOD479imeuQTuIMbvhq5e705TapT5Vcy8tBbxhFRNXeMIQIKouFHAvkX6nU7g_XPWJV6oP4pCvz_zUrcN8hVmO_A4FK_c4Hd',
'cuVQTfz6QtSiqN8IEb_mk8:APA91bHD4JeyEwA5PdXGrl0ZzgkFaOzWVGDEP0WO32IjNt5O_4St76a_h_ibKlyDuOLvuzKsv4_KQmrxx09tKePBGTpcu77W9W9jA5uXUBcBL3vjYDwVzeTl7y74qtt3p_3OFMlzkRUz',
'dqB-_dr3RJGdYji-vwhfQ3:APA91bFamhiTBBpi-9okjtg4iE2qiVRv3cTPPMlJLs01wtpUM1i0sRbcSv2wqbsg6dUBcInKQUldWw4F6PKK38siU8vsT0I3PSztT1Vr66h8yLXVPqFPY5DffBb5zyp588Dpvs4QGs4d'
];
 */

$tokenList = 'ezKFFS2RSGqkCtfu0e0Ose:APA91bE5aJ3OZHZLmkt-oNnxkaLOjMqQQW2Naep-9uHcgVJY7_CT0F1SOEgvgMhW-ZwbM5We43tU0XuANhNk8NMbbQX8RvataouwJ72F9X76DhXi5uFIrHsYx-FtIcYfv6_6ZQLvELAf';
$topicJoya = '/topics/Joya';
$topicAlmaMexico = '/topics/AlmaMexico';
$topicIninbioSystem = '/topics/IninbioSystem';

if (!function_exists('notificacionTemperatura')) {
function notificacionTemperatura()
{
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    if($GLOBALS["numero_tina"] == ""){
    } else {

    $notification = [
        'title' => 'Alerta De Fermentación',
        'body' => 'La Temperatura De La Tina ' .$GLOBALS["numero_tina"]. ' Está Por Encima De Los Rangos Establecidos',
        'sound' => 'sonidito.mp3',
        'color' => 'blue'
    ];
    //$extraNotificationData = ["message" => $notification,];

    // $data = [
    //     'notification_title' => 'Alerta De Fermentación',
    //     'notification_body' =>'La Temperatura De La Tina ' .$GLOBALS["numero_tina"]. ' Está Por Encima De Los Rangos Establecidos',
    //     'notification_foreground' => 'true',
    // ];

    $data = [
        // 'to' => $GLOBALS['tokenList'],
        'data' => [
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación',
            'custom_key' => 'Valor personalizado' // Puedes agregar más claves personalizadas aquí
        ]
    ];

    $fcmNotification = [
        //'registration_ids' => $GLOBALS['token'], //multple token array
        // 'to' =>$GLOBALS['tokenList'], //single token
        'priority' => 'high',
        // 'direct_boot_ok' => true,
        'to' => $GLOBALS['canal'], //single token
        'notification' => $notification,
        'data' => $notification
    ];

    $headers = [
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result;
}
}
}

if (!function_exists('notificacionPH')) {
function notificacionPH()
{
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    if($GLOBALS["numero_tina1"] == ""){
    } else {

    $notification = [
        'title' => 'Alerta De Fermentación',
        'body' => 'El pH De La Tina ' . $GLOBALS["numero_tina1"] . ' Está Por Encima De Los Rangos Establecidos',
        'sound' => 'sonidito.mp3',
        'color' => '#14faff'
    ];
    //$extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

    // $data = [
    //     "message" => $notification,
    //     "notification_foreground" => "true",
    // ];

    $data = [
        // 'to' => $GLOBALS['tokenList'],
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación',
            'notification_foreground' => 'true'
            // 'custom_key' => 'Valor personalizado' // Puedes agregar más claves personalizadas aquí
    ];

    $fcmNotification = [
        //'registration_ids' => $GLOBALS['token'], //multple token array
        // 'to' =>$GLOBALS['tokenList'], //single token
        // 'priority' => 'high',
        // 'direct_boot_ok' => true,
        //'to' => '/topics/Joya', //single token
        'to' => $GLOBALS['canal'],
        'data' => $data,
        'notification' => $notification,
    ];

    $headers = [
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result;
}
}
}

if (!function_exists('notificacionBrix')) {
function notificacionBrix()
{
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    if($GLOBALS["numero_tina2"] == ""){
    } else {

    $notification = [
        'title' => 'Alerta De Fermentación',
        'body' => 'Los °Brix De La Tina ' . $GLOBALS["numero_tina2"] . ' No Han Cambiado, Se Recomienda Verificar Su Fermentación',
        'sound' => 'sonidito.mp3',
        'color' => 'yellow'
    ];

    // $data = [
    //     "message" => $notification,
    //     "notification_foreground" => "true",
    // ];

    $data = [
        // 'to' => $GLOBALS['tokenList'],
        'data' => [
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación',
            'custom_key' => 'Valor personalizado' // Puedes agregar más claves personalizadas aquí
        ]
    ];

    //$extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

    $fcmNotification = [
        //'registration_ids' => $GLOBALS['token'], //multple token array
        // 'to' =>$GLOBALS['tokenList'], //single token
        'priority' => 'high',
        // 'direct_boot_ok' => true,
        //'to' => '/topics/Joya', //single token
        'to' => $GLOBALS['canal'],
        'notification' => $notification,
        'data' => $notification,
    ];

    $headers = [
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result;
}
}
}
?>
