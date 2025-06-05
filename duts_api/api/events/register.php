<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/EventRegister.php';
include_once '../../models/User.php';
include_once '../../models/Event.php';

$database = new Database();
$db = $database->getConnection();
$event_register = new EventRegister($db);
$user_model = new User($db);
$event_model = new Event($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id_usuario) && !empty($data->id_evento)) {
    $event_register->id_usuario = $data->id_usuario;
    $event_register->id_evento = $data->id_evento;

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

    if ($event_register->register_event()) {
        http_response_code(201);
        echo json_encode(array("message" => "Registro en evento exitoso."));
    } else {
        http_response_code(409); // Conflicto (ya está registrado) o 503 (error BD)
        echo json_encode(array("message" => "No se pudo registrar en el evento o ya estaba registrado."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Datos incompletos para el registro en el evento."));
}
?>