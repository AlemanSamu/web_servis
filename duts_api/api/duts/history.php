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

$stmt = $transaction->getTransactionHistory($user_id);
$num = $stmt->rowCount();

if($num > 0){
    $transactions_arr = array();
    $transactions_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $transaction_item = array(
            "id" => $id,
            "id_origen" => $id_origen,
            "origen_username" => $origen_username,
            "id_destino" => $id_destino,
            "destino_username" => $destino_username,
            "cantidad" => $cantidad,
            "fecha" => $fecha
        );
        array_push($transactions_arr["records"], $transaction_item);
    }
    http_response_code(200);
    echo json_encode($transactions_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron transacciones para este usuario."));
}
?>