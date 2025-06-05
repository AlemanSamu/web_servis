<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

$table_name = "duts_transactions";

// Query para DUTS total por día
// Calcula la suma de transacciones por cada día
$query = "SELECT
            DATE(fecha) AS dia,
            SUM(cantidad) AS total_duts_dia
        FROM
            " . $table_name . "
        GROUP BY
            dia
        ORDER BY
            dia DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    $daily_duts_arr = array();
    $daily_duts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $daily_item = array(
            "dia" => $dia,
            "total_duts_dia" => round($total_duts_dia, 2)
        );

        array_push($daily_duts_arr["records"], $daily_item);
    }

    http_response_code(200);
    echo json_encode($daily_duts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No se encontraron datos de DUTS diarios.")
    );
}
?>
