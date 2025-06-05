<?php
// Incluir cabeceras CORS y configuración de la base de datos
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/Event.php';
include_once '../../models/User.php'; // ¡IMPORTANTE! Incluir la clase User para verificación de rol

// Instanciar base de datos y objetos
$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$user = new User($db); // ¡IMPORTANTE! Instanciar objeto User

// Obtener los datos enviados (JSON)
$data = json_decode(file_get_contents("php://input"));

// Asegurarse de que los datos no estén vacíos y que el ID de usuario esté presente
if (
    !empty($data->nombre) &&
    !empty($data->descripcion) &&
    !empty($data->fecha) &&
    !empty($data->tipo_evento) &&
    !empty($data->id_usuario) // ¡IMPORTANTE! Esperamos el ID del usuario que crea el evento
) {
    // Asignar el ID de usuario al modelo User para obtener sus detalles
    $user->id = $data->id_usuario;

    // Leer los detalles del usuario para obtener su rol
    if ($user->read_single()) { // El método read_single() ya carga el rol del usuario
        // ¡IMPORTANTE! Verificar si el rol del usuario es 'admin'
        if ($user->rol === 'admin') { // Asegúrate de que 'admin' sea el valor correcto para el rol de administrador
            // Establecer los valores de las propiedades del evento
            $event->nombre = $data->nombre;
            $event->descripcion = $data->descripcion;
            $event->fecha = $data->fecha;
            $event->tipo_evento = $data->tipo_evento;

            // Intentar crear el evento
            if ($event->create()) {
                // Establecer código de respuesta HTTP - 201 Creado
                http_response_code(201);
                echo json_encode(array("message" => "Evento creado exitosamente."));
            } else {
                // Si no se pudo crear el evento
                http_response_code(503); // Servicio no disponible
                echo json_encode(array("message" => "No se pudo crear el evento."));
            }
        } else {
            // Si el usuario no es administrador
            http_response_code(403); // Prohibido
            echo json_encode(array("message" => "Acceso denegado. Solo los administradores pueden crear eventos."));
        }
    } else {
        // Si el usuario no fue encontrado
        http_response_code(404); // No encontrado
        echo json_encode(array("message" => "Usuario que intenta crear el evento no encontrado."));
    }
} else {
    // Si faltan datos necesarios
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(array("message" => "No se pudo crear el evento. Datos incompletos o ID de usuario faltante."));
}
?>
