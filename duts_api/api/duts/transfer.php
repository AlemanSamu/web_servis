<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/DutsTransaction.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$transaction = new DutsTransaction($db);
$user_model = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->id_origen) &&
    !empty($data->id_destino) &&
    !empty($data->cantidad) &&
    $data->cantidad > 0
) {
    $transaction->id_origen = $data->id_origen;
    $transaction->id_destino = $data->id_destino;
    $transaction->cantidad = $data->cantidad;

    if ($transaction->id_origen == $transaction->id_destino) {
        http_response_code(400);
        echo json_encode(array("message" => "No se puede transferir DUTS al mismo usuario."));
        exit();
    }

    $user_model->id = $transaction->id_origen;
    $user_origin_exists = $user_model->read_single();

    $user_model->id = $transaction->id_destino;
    $user_dest_exists = $user_model->read_single();

    if (!$user_origin_exists) {
        http_response_code(404);
        echo json_encode(array("message" => "El usuario de origen no existe."));
        exit();
    }
    if (!$user_dest_exists) {
        http_response_code(404);
        echo json_encode(array("message" => "El usuario de destino no existe."));
        exit();
    }

    $current_balance = $transaction->getCurrentBalance($transaction->id_origen);
    if ($current_balance < $transaction->cantidad) {
        http_response_code(400);
        echo json_encode(array("message" => "Saldo DUTS insuficiente para la transferencia. Saldo actual: " . $current_balance));
        exit();
    }

    if($transaction->create()){
        http_response_code(201);
        echo json_encode(array("message" => "Transferencia DUTS realizada exitosamente."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "No se pudo realizar la transferencia DUTS."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Datos incompletos o cantidad inválida para la transferencia."));
}
?>