<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/User.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// set user ID to be deleted
$user->id = isset($data->id) ? htmlspecialchars(strip_tags($data->id)) : null;

// --- Verificación de Rol de Administrador ---
// Se espera que 'id_usuario' y 'rol' del usuario que hace la solicitud sean enviados en el cuerpo del JSON.
// En un sistema de producción, esto debería hacerse con tokens de sesión/JWT validados en el backend.
// Aquí, asumimos que los datos enviados desde el frontend son correctos para la verificación de este ejemplo.
$requesting_user_id = isset($data->requesting_user_id) ? htmlspecialchars(strip_tags($data->requesting_user_id)) : '';
$requesting_user_rol = '';

if (!empty($requesting_user_id)) {
    // Cargar los datos del usuario que hace la solicitud para verificar su rol real desde la DB
    $temp_user_obj = new User($db);
    $temp_user_obj->id = $requesting_user_id;
    if ($temp_user_obj->read_single()) {
        $requesting_user_rol = $temp_user_obj->rol;
    }
}

// Verificar si se proporcionó un ID de usuario a eliminar y si el solicitante es un administrador
if (empty($user->id)) {
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Se requiere el ID del usuario a eliminar."));
    exit();
}

if ($requesting_user_rol !== 'admin') {
    http_response_code(401); // Unauthorized
    echo json_encode(array("message" => "Acceso denegado. Solo los administradores pueden eliminar usuarios."));
    exit();
}
// --- Fin Verificación de Rol de Administrador ---


// delete the user
if ($user->delete()) {
    http_response_code(200);
    echo json_encode(array("message" => "Usuario eliminado exitosamente."));
} else {
    http_response_code(503); // Service unavailable
    echo json_encode(array("message" => "No se pudo eliminar el usuario."));
}
?>
