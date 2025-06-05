<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/Event.php';

$database = new Database();
$db = $database->getConnection();
$event = new Event($db);

$stmt = $event->read();
$num = $stmt->rowCount();

if($num > 0){
    $events_arr = array();
    $events_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $event_item = array(
            "id" => $id,
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "fecha" => $fecha,
            "tipo_evento" => $tipo_evento
        );
        array_push($events_arr["records"], $event_item);
    }
    http_response_code(200);
    echo json_encode($events_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron eventos."));
}
?>