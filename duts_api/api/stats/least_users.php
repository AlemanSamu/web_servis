<?php
// Required headers
include_once '../../api_headers.php';

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/User.php';

// Instantiate database and user object
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Query users with least DUTS
$stmt = $user->getLeastUsersByDUTS();
$num = $stmt->rowCount();

// Check if more than 0 records found
if($num > 0){
    // Users array
    $users_arr = array();
    $users_arr["records"] = array();

    // Retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // Extract row
        extract($row);

        $user_item = array(
            "id" => $id,
            "nombres" => $nombres,
            "apellidos" => $apellidos,
            "username" => $username,
            "saldo" => $saldo // Include the calculated balance
        );

        array_push($users_arr["records"], $user_item);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show users data in json format
    echo json_encode($users_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);

    // Tell the user no users found
    echo json_encode(
        array("message" => "No se encontraron usuarios con DUTS.")
    );
}
?>
