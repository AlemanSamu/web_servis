<?php
// required headers
include_once '../../api_headers.php'; // Incluye encabezados CORS y configuración de errores
include_once '../../config/database.php';
include_once '../../models/Event.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare event object
$event = new Event($db);

// set ID property of event to be read
$event->id = isset($_GET['id']) ? htmlspecialchars(strip_tags($_GET['id'])) : null;

// validate ID
if (empty($event->id)) {
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Se requiere el ID del evento."));
    exit();
}

// read the details of the event
if ($event->read_single()) {
    $event_arr = array(
        "id" => $event->id,
        "nombre" => $event->nombre,
        "descripcion" => $event->descripcion,
        "fecha" => $event->fecha,
        "tipo_evento" => $event->tipo_evento,
        
    );

    http_response_code(200);
    echo json_encode($event_arr);
} else {
    http_response_code(404); // Not found
    echo json_encode(array("message" => "Evento no encontrado."));
}
?>
