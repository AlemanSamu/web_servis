<?php
class EventRegister {
    private $conn;
    private $table_name = "eventos_registro";

    public $id_usuario;
    public $id_evento;
    public $fecha_registro;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar un usuario en un evento
    public function register_event() {
        // Verificar si ya está registrado
        if ($this->isAlreadyRegistered()) {
            return false; // Ya registrado
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                      id_usuario = :id_usuario,
                      id_evento = :id_evento";

        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->id_evento = htmlspecialchars(strip_tags($this->id_evento));

        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":id_evento", $this->id_evento);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si un usuario ya está registrado en un evento
    public function isAlreadyRegistered() {
        $query = "SELECT id_usuario FROM " . $this->table_name . "
                  WHERE id_usuario = ? AND id_evento = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->bindParam(2, $this->id_evento);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Desregistrar un usuario de un evento
    public function unregister_event() {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id_usuario = :id_usuario AND id_evento = :id_evento";

        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->id_evento = htmlspecialchars(strip_tags($this->id_evento));

        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":id_evento", $this->id_evento);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // --- NUEVOS MÉTODOS PARA FUNCIONALIDADES ADICIONALES ---

    // Cantidad de eventos a los que un usuario está inscrito
    public function getUserEventCount($userId) {
        $query = "
            SELECT COUNT(*) AS total_eventos_inscritos
            FROM " . $this->table_name . "
            WHERE id_usuario = ?;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_eventos_inscritos'] ? $row['total_eventos_inscritos'] : 0;
    }

    // Usuarios inscritos en un evento específico
    public function getUsersRegisteredInEvent($eventId) {
        $query = "
            SELECT u.id, u.nombres, u.apellidos, u.username, u.email
            FROM " . $this->table_name . " er
            JOIN users u ON er.id_usuario = u.id
            WHERE er.id_evento = ?;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $eventId);
        $stmt->execute();

        return $stmt;
    }

    // Eventos con más de N inscritos
    public function getPopularEvents($minInscriptions = 10) {
        $query = "
            SELECT e.id, e.nombre, e.fecha, e.tipo_evento, COUNT(er.id_usuario) AS total_inscritos
            FROM eventos e
            JOIN " . $this->table_name . " er ON e.id = er.id_evento
            GROUP BY e.id, e.nombre, e.fecha, e.tipo_evento
            HAVING COUNT(er.id_usuario) > ?
            ORDER BY total_inscritos DESC;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $minInscriptions, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}
?>