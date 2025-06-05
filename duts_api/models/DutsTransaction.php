<?php
class DutsTransaction {
    private $conn;
    private $table_name = "duts_transactions";
    private $users_table = "users"; // Necesario si se hacen JOINs o verificaciones con la tabla users

    public $id;
    public $id_origen;
    public $id_destino;
    public $cantidad;
    public $fecha;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear una nueva transacción de DUTS (anteriormente 'transfer')
    public function create() { // <<-- MÉTODO RENOMBRADO A 'create'
        // Verificar saldo de origen
        // Es crucial que esta lógica de saldo se realice antes de la inserción.
        // Si getCurrentBalance() es un método de este mismo modelo, está bien.
        // Si necesitas el saldo del usuario, asegúrate de que el usuario tenga suficiente.
        $current_balance_origin = $this->getCurrentBalance($this->id_origen);
        if ($current_balance_origin < $this->cantidad) {
            // Puedes lanzar una excepción aquí o simplemente retornar false
            // para que el controlador (transfer.php) maneje el mensaje de error.
            return false; // Saldo insuficiente
        }

        // Consulta para insertar la transacción
        $query = "INSERT INTO " . $this->table_name . "
                    SET
                        id_origen = :id_origen,
                        id_destino = :id_destino,
                        cantidad = :cantidad";
                        // 'fecha' tiene DEFAULT CURRENT_TIMESTAMP en la DB, no es necesario insertarla explícitamente

        $stmt = $this->conn->prepare($query);

        // Limpiar y castear los datos a sus tipos esperados (entero o flotante)
        // Esto es más apropiado para datos numéricos que htmlspecialchars/strip_tags.
        $this->id_origen = (int) $this->id_origen;
        $this->id_destino = (int) $this->id_destino;
        $this->cantidad = (float) $this->cantidad;

        // Vincular los valores
        $stmt->bindParam(":id_origen", $this->id_origen);
        $stmt->bindParam(":id_destino", $this->id_destino);
        $stmt->bindParam(":cantidad", $this->cantidad);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }

        // Para depuración si falla:
        // error_log("Error al crear transacción: " . print_r($stmt->errorInfo(), true));
        return false;
    }

    // Obtener el saldo actual de un usuario
    public function getCurrentBalance($user_id) {
        $query = "SELECT
                    (COALESCE(SUM(CASE WHEN id_destino = :user_id THEN cantidad ELSE 0 END), 0) -
                     COALESCE(SUM(CASE WHEN id_origen = :user_id THEN cantidad ELSE 0 END), 0)) AS balance
                  FROM " . $this->table_name . "
                  WHERE id_origen = :user_id OR id_destino = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Asegúrate de devolver 0.0 si el balance es NULL (ej. si no hay transacciones)
        return (float)($row['balance'] ?? 0.0);
    }

    // Obtener el historial de transacciones de un usuario
    public function getTransactionHistory($user_id) {
        $query = "SELECT dt.id, dt.cantidad, dt.fecha,
                            u_origen.username as origen_username,
                            u_destino.username as destino_username
                    FROM " . $this->table_name . " dt
                    LEFT JOIN " . $this->users_table . " u_origen ON dt.id_origen = u_origen.id
                    LEFT JOIN " . $this->users_table . " u_destino ON dt.id_destino = u_destino.id
                    WHERE dt.id_origen = :user_id OR dt.id_destino = :user_id
                    ORDER BY dt.fecha DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs
        $stmt->execute();

        return $stmt;
    }

    // Cantidad total de DUTS transferidos por un usuario
    public function getTotalDutsSent($userId) {
        $query = "
            SELECT COALESCE(SUM(cantidad), 0) AS total_enviados
            FROM " . $this->table_name . "
            WHERE id_origen = :user_id;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['total_enviados'] ?? 0.0);
    }

    // Cantidad total de DUTS recibidos por un usuario
    public function getTotalDutsReceived($userId) {
        $query = "
            SELECT COALESCE(SUM(cantidad), 0) AS total_recibidos
            FROM " . $this->table_name . "
            WHERE id_destino = :user_id;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['total_recibidos'] ?? 0.0);
    }

    // Puedes añadir otros métodos aquí como read(), read_single(), etc.
}
?>
