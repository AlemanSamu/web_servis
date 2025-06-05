<?php
class Database {
    private $host = "localhost";
    private $db_name = "duts_platform"; // Asegúrate de que este sea el nombre de tu BD de MySQL
    private $username = "root";         // El usuario por defecto de MySQL en XAMPP
    private $password = "";             // La contraseña por defecto de MySQL en XAMPP (vacía)
    public $conn;

    // Método para obtener la conexión a la base de datos
    public function getConnection() {
        $this->conn = null; // Reinicia la conexión a null cada vez para asegurar una nueva conexión

        try {
            // Cadena de conexión para MySQL con PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Configura PDO para lanzar excepciones en caso de errores SQL
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Establecer el juego de caracteres a utf8mb4 si tu BD lo usa (recomendado)
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            // En lugar de hacer echo, registra el error y lanza una excepción personalizada
            // para que los scripts que usan esta clase puedan manejarla de forma más elegante.
            error_log("Error de conexión a la base de datos: " . $exception->getMessage());
            throw new Exception("Error de conexión a la base de datos.");
            // También podrías lanzar directamente la PDOException si prefieres.
        }
        return $this->conn; // Devuelve el objeto de conexión
    }
}
// Opcional: Asegúrate de que no haya espacios o saltos de línea después de la etiqueta de cierre si la usas
// ?>