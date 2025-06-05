<?php
// required headers
include_once '../../api_headers.php';
include_once '../../config/database.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

$table_name = "duts_transactions";

// Query para DUTS total por mes
$query = "SELECT
            YEAR(fecha) AS anio,
            MONTH(fecha) AS mes,
            SUM(cantidad) AS total_duts_mes
        FROM
            " . $table_name . "
        GROUP BY
            anio, mes
        ORDER BY
            anio DESC, mes DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0){
    $monthly_duts_arr = array();
    $monthly_duts_arr["records"] = array();

    // Configurar la configuración regional para nombres de meses en español
    // Esto es importante para que strftime() funcione correctamente.
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        // Obtener el nombre del mes en español
        $month_name_timestamp = mktime(0, 0, 0, $mes, 10);
        $month_name = strftime('%B', $month_name_timestamp);
        $month_name = ucfirst($month_name); // Poner la primera letra en mayúscula

        $monthly_item = array(
            "anio" => $anio,
            "mes" => $mes,
            "nombre_mes" => $month_name,
            "total_duts_mes" => round($total_duts_mes, 2)
        );

        array_push($monthly_duts_arr["records"], $monthly_item);
    }

    http_response_code(200);
    echo json_encode($monthly_duts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No se encontraron datos de DUTS mensuales.")
    );
}
?>
