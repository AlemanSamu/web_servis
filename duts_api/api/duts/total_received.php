<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/DutsTransaction.php';
include_once '../../models/User.php'; // Incluir User para validar si existe

$database = new Database();
$db = $database->getConnection();

$duts_transaction = new DutsTransaction($db);
$user_model = new User($db); // Instancia del modelo User

$userId = isset($_GET['user_id']) ? htmlspecialchars(strip_tags($_GET['user_id'])) : null;

if (!empty($userId)) {
    // Verificar si el usuario existe
    $user_model->id = $userId;
    if (!$user_model->read_single()) {
        http_response_code(404);
        echo json_encode(array("message" => "El usuario especificado no existe."));
        exit();
    }

    $total_received = $duts_transaction->getTotalDutsReceived($userId);

    http_response_code(200);
    echo json_encode(array(
        "user_id" => (int)$userId,
        "total_duts_recibidos" => (float)$total_received
    ));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Se requiere el ID del usuario."));
}
?>