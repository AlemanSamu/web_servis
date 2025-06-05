<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/DutsTransaction.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$transaction = new DutsTransaction($db);
$user_model = new User($db);

$user_id = isset($_GET['user_id']) ? htmlspecialchars(strip_tags($_GET['user_id'])) : die(json_encode(array("message" => "Se requiere el ID del usuario.")));

$user_model->id = $user_id;
if (!$user_model->read_single()) {
    http_response_code(404);
    echo json_encode(array("message" => "Usuario no encontrado."));
    exit();
}

$balance = $transaction->getCurrentBalance($user_id);

http_response_code(200);
echo json_encode(array(
    "user_id" => (int)$user_id, // Asegurarse de que el ID sea numérico
    "balance" => (float)$balance, // Asegurarse de que el balance sea numérico
    "required_for_graduation" => 100000,
    "progress_percentage" => ($balance / 100000) * 100
));
?>