<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/Event.php';
include_once '../../models/User.php'; // Incluir para verificar el rol del usuario

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare event object
$event = new Event($db);
$user_obj = new User($db); // Objeto para verificar el rol

// get posted data
$data = json_decode(file_get_contents("php://input"));

// set ID property of event to be updated
$event->id = isset($data->id) ? htmlspecialchars(strip_tags($data->id)) : null;

// Check if user is logged in and is admin (simulated check)
// In a real application, you would use a JWT token or session for this.
// For now, we expect 'user_id' and 'rol' to be sent in the request body for this example.
// THIS IS A SIMPLIFIED EXAMPLE AND SHOULD BE REPLACED WITH PROPER AUTHENTICATION
$requesting_user_id = isset($data->id_usuario) ? htmlspecialchars(strip_tags($data->id_usuario)) : '';
$requesting_user_rol = '';

if (!empty($requesting_user_id)) {
    $user_obj->id = $requesting_user_id;
    if ($user_obj->read_single()) { // Use read_single to get user's real role from DB
        $requesting_user_rol = $user_obj->rol;
    }
}

if (empty($event->id) || $requesting_user_rol !== 'admin') {
    http_response_code(401); // Unauthorized
    echo json_encode(array("message" => "Acceso denegado. Se requiere ID de evento y rol de administrador."));
    exit();
}

// set event property values
$event->nombre = $data->nombre;
$event->descripcion = $data->descripcion;
$event->fecha = $data->fecha;
$event->tipo_evento = $data->tipo_evento;

// update the event
if ($event->update()) {
    http_response_code(200);
    echo json_encode(array("message" => "Evento actualizado exitosamente."));
} else {
    http_response_code(503); // Service unavailable
    echo json_encode(array("message" => "No se pudo actualizar el evento."));
}
?>
