<?php

header('Content-Type: application/json');

$url = "https://dolitoday.com/api/rate";

// Ejecutar curl CLI (NO PHP curl)
$cmd = "curl -s " . escapeshellarg($url);

$response = shell_exec($cmd);

if (!$response) {
    http_response_code(500);
    echo json_encode([
        "error" => "No se pudo obtener datos"
    ]);
    exit;
}

// Validar JSON
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
        "error" => "Respuesta inválida",
        "raw" => $response
    ]);
    exit;
}

// Opcional: limpiar salida
echo json_encode($data);
