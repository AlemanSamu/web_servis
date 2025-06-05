<?php
// Required headers
include_once '../../api_headers.php';

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/User.php'; // Asegúrate de que el modelo User esté disponible

// Instantiate database and user object
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Query users with no events
// ¡CORRECCIÓN! Llamando al método getUsersWithoutEvents() que está definido en models/User.php
$stmt = $user->getUsersWithoutEvents();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    // Users array
    $users_arr = array();
    $users_arr["records"] = array();

    // Retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // Extract row
        extract($row);

        $user_item = array(
            "id" => $id,
            "nombres" => $nombres,
            "apellidos" => $apellidos,
            "email" => $email,
            "username" => $username,
            "rol" => $rol
        );

        array_push($users_arr["records"], $user_item);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show users data in json format
    echo json_encode($users_arr);
} else {
    // Set response code - 200 OK (más apropiado si no se encuentran resultados, no es un error del servidor)
    http_response_code(200);
    echo json_encode(
        array("message" => "No se encontraron usuarios sin asistencia a eventos.")
    );
}
?>
