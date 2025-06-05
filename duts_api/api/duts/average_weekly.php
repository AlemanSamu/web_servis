<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

$table_name = "duts_transactions";

// Query para DUTS total por semana
// WEEK(fecha, 3) define que la semana empieza el lunes y la primera semana del año
// es aquella con 4 o más días. Ajusta si tu definición de semana es diferente.
$query = "SELECT
            YEAR(fecha) AS anio,
            WEEK(fecha, 3) AS semana,
            SUM(cantidad) AS total_duts_semana
        FROM
            " . $table_name . "
        GROUP BY
            anio, semana
        ORDER BY
            anio DESC, semana DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    $weekly_duts_arr = array();
    $weekly_duts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $weekly_item = array(
            "anio" => $anio,
            "semana" => $semana,
            "total_duts_semana" => round($total_duts_semana, 2)
        );

        array_push($weekly_duts_arr["records"], $weekly_item);
    }

    http_response_code(200);
    echo json_encode($weekly_duts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No se encontraron datos de DUTS semanales.")
    );
}
?>
