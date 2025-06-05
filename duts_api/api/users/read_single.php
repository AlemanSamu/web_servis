<?php
// required headers
include_once '../../api_headers.php'; // Asegúrate de que este archivo tenga los display_errors activados

// include database and object files
include_once '../../config/database.php';
include_once '../../models/User.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new User($db);

// set ID property of user to be read
// Limpiar y sanitizar el ID desde GET
$user->id = isset($_GET['id']) ? htmlspecialchars(strip_tags($_GET['id'])) : null;

// Si no se proporciona un ID, enviar una respuesta de error JSON y detener la ejecución
if(empty($user->id)){
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Se requiere el ID del usuario."));
    exit(); // Detener la ejecución para evitar salida adicional
}

// read the details of user to be edited
if($user->read_single()){
    // create array
    $user_arr = array(
        "id" =>  $user->id,
        "nombres" => $user->nombres,
        "apellidos" => $user->apellidos,
        "email" => $user->email,
        "ciudad" => $user->ciudad,
        "pais" => $user->pais,
        "descripcion" => $user->descripcion,
        "intereses" => $user->intereses,
        "programa" => $user->programa,
        "semestre" => $user->semestre,
        "username" => $user->username,
        "rol" => $user->rol,
        "created_at" => $user->created_at // INCLUIDO: Asegura que created_at se envía en el JSON
    );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($user_arr);
} else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user user does not exist
    echo json_encode(array("message" => "Usuario no encontrado."));
}
?>
