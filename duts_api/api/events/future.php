<?php
// required headers
include_once '../../api_headers.php'; // Incluye encabezados CORS y configuración de errores

// include database and object files
include_once '../../config/database.php';
include_once '../../models/Event.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare event object
$event = new Event($db);

// query future events
// Se corrigió el nombre del método de readFuture() a readFutureEvents()
$stmt = $event->readFutureEvents();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0){
    // events array
    $events_arr = array();
    $events_arr["records"] = array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to just $name only
        extract($row);

        $event_item = array(
            "id" => $id,
            "nombre" => $nombre,
            "descripcion" => html_entity_decode($descripcion),
            "fecha" => $fecha,
            "tipo_evento" => $tipo_evento
            // "created_at" => $created_at // Comentar si no existe en la tabla de eventos
        );

        array_push($events_arr["records"], $event_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show events data in json format
    echo json_encode($events_arr);
} else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no events found
    echo json_encode(
        array("message" => "No se encontraron eventos futuros.")
    );
}
?>
