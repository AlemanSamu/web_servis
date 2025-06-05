<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/EventRegister.php';
include_once '../../models/Event.php'; // Incluir Event para validar si existe

$database = new Database();
$db = $database->getConnection();

$event_register = new EventRegister($db);
$event_model = new Event($db); // Instancia del modelo Event

$eventId = isset($_GET['event_id']) ? htmlspecialchars(strip_tags($_GET['event_id'])) : null;

if (!empty($eventId)) {
    // Verificar si el evento existe
    $event_model->id = $eventId;
    if (!$event_model->read_single()) {
        http_response_code(404);
        echo json_encode(array("message" => "El evento especificado no existe."));
        exit();
    }

    $stmt = $event_register->getUsersRegisteredInEvent($eventId);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $users_arr = array();
        $users_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $user_item = array(
                "id" => $id,
                "nombres" => $nombres,
                "apellidos" => $apellidos,
                "username" => $username,
                "email" => $email
            );
            array_push($users_arr["records"], $user_item);
        }
        http_response_code(200);
        echo json_encode($users_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron usuarios inscritos en este evento."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Se requiere el ID del evento."));
}
?>