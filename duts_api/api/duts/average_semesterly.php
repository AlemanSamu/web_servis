<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

$table_name = "duts_transactions";

// Query para DUTS total por semestre
$query = "SELECT
            YEAR(fecha) AS anio,
            CASE
                WHEN MONTH(fecha) BETWEEN 1 AND 6 THEN 1
                WHEN MONTH(fecha) BETWEEN 7 AND 12 THEN 2
            END AS semestre,
            SUM(cantidad) AS total_duts_semestre
        FROM
            " . $table_name . "
        GROUP BY
            anio, semestre
        ORDER BY
            anio DESC, semestre DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0){
    $semesterly_duts_arr = array();
    $semesterly_duts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $semesterly_item = array(
            "anio" => $anio,
            "semestre" => $semestre,
            "total_duts_semestre" => round($total_duts_semestre, 2)
        );

        array_push($semesterly_duts_arr["records"], $semesterly_item);
    }

    http_response_code(200);
    echo json_encode($semesterly_duts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No se encontraron datos de DUTS semestrales.")
    );
}
?>
