<?php
// send_notification.php

function sendFCM($token, $title, $body, $data = []) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $serverKey = 'BAT-6FrjbiROUKRKGY1uKPkY_eEaZ7wOkroWCnHOjiTE_Czv5wvcut7buhmsppkGoIy7YLW_PReawVg5SLANSWU'; 

    $notification = [
        'title' => $title,
        'body' => $body,
    ];

    $fields = [
        'to' => $token,
        'notification' => $notification,
        'data' => $data
    ];

    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

// Recibir parámetros vía POST
$token = $_POST['token'] ?? '';
$title = $_POST['title'] ?? 'Notificación';
$body = $_POST['body'] ?? '';
$data = isset($_POST['data']) ? json_decode($_POST['data'], true) : [];

// Validar token
if ($token === '') {
    die('Error: no se recibió token.');
}

// Enviar notificación
$response = sendFCM($token, $title, $body, $data);
echo $response;
