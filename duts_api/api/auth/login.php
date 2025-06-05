<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->password)) {
    $user->username = $data->username;
    $user->password = $data->password;

    if ($user->login()) {
        http_response_code(200); // OK
        echo json_encode(array(
            "message" => "Inicio de sesión exitoso.",
            "user_id" => $user->id,
            "username" => $user->username,
            "nombres" => $user->nombres,
            "apellidos" => $user->apellidos,
            "rol" => $user->rol,
        ));
    } else {
        http_response_code(401); // No autorizado
        echo json_encode(array("message" => "Credenciales inválidas."));
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Por favor, ingresa tu nombre de usuario y contraseña."));
}
?>