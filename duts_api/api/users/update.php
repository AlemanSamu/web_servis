<?php
// Incluye los archivos de configuración de la base de datos y el objeto de usuario
include_once '../../api_headers.php'; // Asegúrate de que este archivo contiene las cabeceras CORS
include_once '../../config/database.php';
include_once '../../models/User.php';

// Obtiene la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Prepara el objeto de usuario
$user = new User($db);

// Obtiene los datos enviados (PUT request)
$data = json_decode(file_get_contents("php://input"));

// Asegúrate de que los datos no estén vacíos y que se ha proporcionado un ID
if(empty($data) || !isset($data->id) || empty($data->id)){
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Datos incompletos o ID de usuario no proporcionado para actualizar."));
    exit();
}

// Asigna el ID al objeto user
$user->id = $data->id;

// Lee los detalles del usuario existente
// Esto es importante para que si solo actualizas un campo, los otros no se sobrescriban a null.
// Si read_single() devuelve false, significa que el usuario no existe.
if(!$user->read_single()){
    http_response_code(404); // Not Found
    echo json_encode(array("message" => "Usuario no encontrado."));
    exit();
}

// Asigna los valores de las propiedades del objeto user con los datos recibidos.
// Si un campo no se envía en la solicitud PUT, se mantiene el valor existente (cargado por read_single).
$user->nombres = isset($data->nombres) ? $data->nombres : $user->nombres;
$user->apellidos = isset($data->apellidos) ? $data->apellidos : $user->apellidos;
$user->email = isset($data->email) ? $data->email : $user->email;
$user->ciudad = isset($data->ciudad) ? $data->ciudad : $user->ciudad;
$user->pais = isset($data->pais) ? $data->pais : $user->pais;
$user->descripcion = isset($data->descripcion) ? $data->descripcion : $user->descripcion;
$user->intereses = isset($data->intereses) ? $data->intereses : $user->intereses;
$user->programa = isset($data->programa) ? $data->programa : $user->programa;
// Para semestre, asegúrate de que sea un número o null si está vacío
$user->semestre = isset($data->semestre) && $data->semestre !== '' ? (int)$data->semestre : null;
$user->username = isset($data->username) ? $data->username : $user->username;
$user->rol = isset($data->rol) && !empty($data->rol) ? strtolower($data->rol) : $user->rol; // Asegura minúsculas y no sobrescribe si está vacío

// Solo si se proporciona una nueva contraseña, la asignamos al objeto para que se hashee y actualice
if(isset($data->password) && !empty($data->password)){
    $user->password = $data->password;
} else {
    // Si no se proporciona contraseña o está vacía, nos aseguramos de que el método update no la cambie.
    // En el método update(), si $this->password es null/empty, la consulta SQL no incluirá el campo password.
    $user->password = null;
}

// VERIFICACIÓN: Comprueba si el email o username ya existen para OTRO usuario
// Usa el nuevo método usernameOrEmailExistsExceptCurrent()
if($user->usernameOrEmailExistsExceptCurrent()){
    http_response_code(409); // Conflict
    echo json_encode(array("message" => "El nuevo email o nombre de usuario ya está en uso por otro usuario."));
    exit();
}

// Intenta actualizar el usuario
if($user->update()){
    http_response_code(200); // OK
    echo json_encode(array("message" => "Usuario actualizado exitosamente."));
} else {
    http_response_code(503); // Service Unavailable (error al ejecutar la consulta de actualización)
    echo json_encode(array("message" => "No se pudo actualizar el usuario. Ocurrió un error en el servidor."));
}
?>
