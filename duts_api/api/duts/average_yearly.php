<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

$table_name = "duts_transactions";

// Query para DUTS total por año
$query = "SELECT
            YEAR(fecha) AS anio,
            SUM(cantidad) AS total_duts_anio
        FROM
            " . $table_name . "
        GROUP BY
            anio
        ORDER BY
            anio DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0){
    $yearly_duts_arr = array();
    $yearly_duts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $yearly_item = array(
            "anio" => $anio,
            "total_duts_anio" => round($total_duts_anio, 2)
        );

        array_push($yearly_duts_arr["records"], $yearly_item);
    }

    http_response_code(200);
    echo json_encode($yearly_duts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No se encontraron datos de DUTS anuales.")
    );
}
?>
