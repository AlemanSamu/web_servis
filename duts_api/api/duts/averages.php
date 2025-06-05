<?php
include_once '../../api_headers.php';
include_once '../../config/database.php';
include_once '../../models/DutsTransaction.php';

$database = new Database();
$db = $database->getConnection();
$transaction = new DutsTransaction($db);

$interval = isset($_GET['interval']) ? htmlspecialchars(strip_tags($_GET['interval'])) : 'day';

$stmt = $transaction->getAverageDUTS($interval);
$num = $stmt->rowCount();

if($num > 0){
    $averages_arr = array();
    $averages_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $average_item = array(
            "periodo" => $periodo,
            "promedio_duts" => $promedio_duts
        );
        array_push($averages_arr["records"], $average_item);
    }
    http_response_code(200);
    echo json_encode($averages_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron promedios de DUTS para el intervalo especificado."));
}
?>