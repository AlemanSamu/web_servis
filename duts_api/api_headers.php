<?php
// api_headers.php
// Permite solicitudes desde cualquier origen. En producción, esto debería ser más restrictivo.
header("Access-Control-Allow-Origin: *");
// Define el tipo de contenido de la respuesta como JSON.
header("Content-Type: application/json; charset=UTF-8");
// Especifica los métodos HTTP que están permitidos.
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
// Especifica la duración en segundos durante la cual los resultados de una solicitud de pre-vuelo pueden ser cacheados.
header("Access-Control-Max-Age: 3600");
// Define las cabeceras HTTP que el cliente puede enviar.
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Si la solicitud es un método OPTIONS (solicitud de pre-vuelo de CORS),
// simplemente responde con un 200 OK y termina la ejecución.
// Esto es para que el navegador verifique los permisos antes de enviar la solicitud real (POST, PUT, DELETE).
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
