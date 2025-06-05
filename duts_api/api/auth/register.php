<?php
// api_headers.php to handle CORS
include_once '../../api_headers.php';

// Database connection
include_once '../../config/database.php';

// User object
include_once '../../models/User.php';

// Instantiate database and user object
$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// *** Importante: Asegúrate de que todos los campos requeridos en el HTML estén aquí ***
if (
    !empty($data->nombres) &&
    !empty($data->apellidos) &&
    !empty($data->email) &&
    !empty($data->ciudad) &&       // Este campo es requerido en el HTML
    !empty($data->pais) &&         // Este campo es requerido en el HTML
    !empty($data->programa) &&     // Este campo es requerido en el HTML
    !empty($data->semestre) &&     // Este campo es requerido en el HTML
    !empty($data->username) &&
    !empty($data->password) &&
    !empty($data->rol)
) {
    // Set user property values
    $user->nombres = $data->nombres;
    $user->apellidos = $data->apellidos;
    $user->email = $data->email;
    // Usar el operador de fusión de null (??) para establecer valores predeterminados
    // si las propiedades no existen o son null.
    // Aunque ya verificamos con !empty(), es buena práctica para campos opcionales si los tuvieras.
    $user->ciudad = $data->ciudad;
    $user->pais = $data->pais;
    $user->descripcion = isset($data->descripcion) ? $data->descripcion : '';
    $user->intereses = isset($data->intereses) ? $data->intereses : '';
    $user->programa = $data->programa;
    // Manejar el semestre que puede ser 0 o nulo si no se envía o está vacío
    $user->semestre = isset($data->semestre) && $data->semestre !== '' ? (int)$data->semestre : null;
    $user->username = $data->username;
    $user->password = $data->password; // Esta es la contraseña en texto plano, User.php la hasheará
    $user->rol = $data->rol;

    try {
        // Verificar si el usuario/email ya existe
        if($user->exists()){
            http_response_code(409); // Conflict
            echo json_encode(array("message" => "El email o nombre de usuario ya existe."));
        } else {
            // Intentar crear el usuario
            if($user->create()){ // Esta es la llamada al método 'create' que estaba faltando/mal definido
                http_response_code(201); // Creado
                echo json_encode(array("message" => "Usuario registrado exitosamente."));
            } else {
                http_response_code(503); // Servicio no disponible
                echo json_encode(array("message" => "No se pudo registrar el usuario."));
            }
        }
    } catch (PDOException $e) {
        // Captura errores de la base de datos (ej: problemas de conexión, campos inválidos)
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Error de base de datos al registrar usuario: " . $e->getMessage()));
        // Para depuración avanzada: error_log("PDO Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    } catch (Exception $e) {
        // Captura cualquier otra excepción general
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Error inesperado al registrar usuario: " . $e->getMessage()));
        // Para depuración avanzada: error_log("General Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    }

} else {
    // Si faltan datos en la solicitud
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(array("message" => "No se pudo registrar el usuario. Datos incompletos."));
}
?>