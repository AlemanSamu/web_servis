<?php
// Incluye los encabezados CORS y el manejador de la base de datos
include_once '../../api_headers.php';
include_once '../../config/database.php';
// Incluye la clase del modelo EventRegister
include_once '../../models/EventRegister.php';
include_once '../../models/User.php';
include_once '../../models/Event.php';

// Instancia la base de datos y obtiene la conexión
$database = new Database();
$db = $database->getConnection();

// Instancia el objeto EventRegister y los modelos User y Event para validación
$event_register = new EventRegister($db);
$user_model = new User($db);
$event_model = new Event($db);

// Obtiene los datos enviados en el cuerpo de la solicitud (JSON)
$data = json_decode(file_get_contents("php://input"));

// Verifica que los IDs de usuario y evento estén presentes
if (!empty($data->id_usuario) && !empty($data->id_evento)) {
    // Asigna los IDs a las propiedades del objeto EventRegister
    $event_register->id_usuario = $data->id_usuario;
    $event_register->id_evento = $data->id_evento;

    // Verifica si el usuario y el evento existen antes de intentar desregistrar
    $user_model->id = $data->id_usuario;
    $event_model->id = $data->id_evento;

    if (!$user_model->read_single()) {
        http_response_code(404);
        echo json_encode(array("message" => "El usuario no existe."));
        exit();
    }
    if (!$event_model->read_single()) {
        http_response_code(404);
        echo json_encode(array("message" => "El evento no existe."));
        exit();
    }

    // Intenta desregistrar al usuario del evento
    if ($event_register->unregister_event()) {
        // Si se desregistra exitosamente, responde con un código 200 (OK)
        http_response_code(200);
        echo json_encode(array("message" => "Desregistro del evento exitoso."));
    } else {
        // Si no se puede desregistrar (ej. no estaba registrado), responde con un código 503 (Service Unavailable)
        http_response_code(503);
        echo json_encode(array("message" => "No se pudo desregistrar del evento o el usuario no estaba registrado en este evento."));
    }
} else {
    // Si los IDs están incompletos, responde con un código 400 (Bad Request)
    http_response_code(400);
    echo json_encode(array("message" => "Datos incompletos para el desregistro del evento."));
}
?>