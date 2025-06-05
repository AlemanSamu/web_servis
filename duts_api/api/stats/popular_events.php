<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/EventRegister.php';

$database = new Database();
$db = $database->getConnection();

$event_register = new EventRegister($db);

$minInscriptions = isset($_GET['min_inscriptions']) ? (int)htmlspecialchars(strip_tags($_GET['min_inscriptions'])) : 1; // Cambiado a 1 para facilitar pruebas iniciales
if ($minInscriptions < 0) $minInscriptions = 0;

$stmt = $event_register->getPopularEvents($minInscriptions);
$num = $stmt->rowCount();

if ($num > 0) {
    $events_arr = array();
    $events_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $event_item = array(
            "id" => $id,
            "nombre" => $nombre,
            "fecha" => $fecha,
            "tipo_evento" => $tipo_evento,
            "total_inscritos" => (int)$total_inscritos
        );
        array_push($events_arr["records"], $event_item);
    }
    http_response_code(200);
    echo json_encode($events_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron eventos con más de {$minInscriptions} inscritos."));
}
?>