<?php
// Incluir cabeceras CORS (si es necesario para ejecución directa en navegador, aunque no es una API endpoint)
include_once '../../api_headers.php';
// Incluir la configuración de la base de datos
include_once '../../config/database.php';
// Incluir la clase del modelo User
include_once '../../models/User.php';

// Instanciar la base de datos y obtener la conexión
$database = new Database();
$db = $database->getConnection();

// Instanciar el objeto User
$user = new User($db);

echo "<h1>Insertando 10 Usuarios Estudiantes...</h1>";
echo "<ul style='list-style-type: none; padding: 0;'>";

for ($i = 1; $i <= 10; $i++) {
    // Generar datos aleatorios para cada estudiante
    // Usamos un número aleatorio para intentar asegurar la unicidad de username y email
    $random_num = rand(10000, 99999); // Rango más amplio para mayor unicidad
    $first_name_options = ['Maria', 'Juan', 'Ana', 'Carlos', 'Laura', 'Pedro', 'Sofía', 'Diego', 'Valeria', 'Andres'];
    $last_name_options = ['Gomez', 'Rodriguez', 'Perez', 'Lopez', 'Martinez', 'Diaz', 'Sanchez', 'Fernandez', 'Torres', 'Ramirez'];
    $city_options = ['Bogota', 'Medellin', 'Cali', 'Barranquilla', 'Cartagena', 'Bucaramanga', 'Manizales', 'Pereira', 'Armenia', 'Popayan'];
    $country_options = ['Colombia', 'Mexico', 'Argentina', 'Chile', 'Peru', 'Ecuador', 'España', 'Venezuela', 'Brasil', 'Canada'];
    $program_options = ['Ingenieria de Sistemas', 'Administracion de Empresas', 'Contaduria Publica', 'Derecho', 'Medicina', 'Arquitectura', 'Diseño Grafico', 'Psicologia', 'Comunicacion Social', 'Marketing'];

    $user->nombres = $first_name_options[array_rand($first_name_options)] . ' ' . $random_num; // Añadir num para unicidad
    $user->apellidos = $last_name_options[array_rand($last_name_options)];
    $user->email = "estudiante{$random_num}@example.com";
    $user->ciudad = $city_options[array_rand($city_options)];
    $user->pais = $country_options[array_rand($country_options)];
    $user->descripcion = "Estudiante de DUTS App, interesado en aprender.";
    $user->intereses = "Tecnologia, Lectura, Viajes";
    $user->programa = $program_options[array_rand($program_options)];
    $user->semestre = rand(1, 10);
    $user->username = "estudiante{$random_num}";
    $user->password = "123456"; // Contraseña simple para usuarios de prueba
    $user->rol = "estudiante"; // Rol fijo para estos usuarios

    echo "<li>Intentando registrar a: " . $user->username . " (" . $user->email . ") - Rol: " . $user->rol . "... ";

    // Verificar si el usuario ya existe (por username o email) antes de intentar crear
    if ($user->exists()) {
        echo "<span style='color: orange;'>Ya existe. Saltando.</span></li>";
    } else {
        if ($user->create()) {
            echo "<span style='color: green;'>¡Éxito!</span></li>";
        } else {
            // En caso de error, intenta obtener más detalles si es posible (descomentar para depuración)
            // $errorInfo = $db->errorInfo();
            // echo "<span style='color: red;'>¡Error al registrar! Código: " . $errorInfo[0] . ", Mensaje: " . $errorInfo[2] . "</span></li>";
            echo "<span style='color: red;'>¡Error al registrar!</span></li>";
        }
    }
}
echo "</ul>";
echo "<p>Proceso de inserción de estudiantes finalizado.</p>";
?>