<?php
class Event {
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "eventos"; // Nombre de tu tabla de eventos
    private $events_register_table = "eventos_registro"; // Nombre de tu tabla de registro de eventos

    // Propiedades del objeto
    public $id;
    public $nombre;
    public $descripcion;
    public $fecha;
    public $tipo_evento;
    // public $created_at; // Comentamos o eliminamos esta propiedad si la columna no existe

    // Constructor con $db como conexión a la base de datos
    public function __construct($db){
        $this->conn = $db;
    }

    // Método para crear un nuevo evento
    public function create(){
        // Consulta para insertar un registro
        $query = "INSERT INTO " . $this->table_name . "
                    SET
                        nombre = :nombre,
                        descripcion = :descripcion,
                        fecha = :fecha,
                        tipo_evento = :tipo_evento";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Limpiar los datos
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion=htmlspecialchars(strip_tags($this->descripcion));
        $this->fecha=htmlspecialchars(strip_tags($this->fecha));
        $this->tipo_evento=htmlspecialchars(strip_tags($this->tipo_evento));

        // Vincular los valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);

        // Ejecutar la consulta
        if($stmt->execute()){
            return true;
        }

        // Para depuración
        // error_log("Error al ejecutar create en Event: " . json_encode($stmt->errorInfo()));
        return false;
    }

    // Método para leer todos los eventos (o filtrar por tipo)
    public function read(){
        $query = "SELECT
                            id, nombre, descripcion, fecha, tipo_evento
                        FROM
                            " . $this->table_name;

        // Si se proporciona un tipo de evento, añadir la cláusula WHERE
        if(isset($this->tipo_evento) && !empty($this->tipo_evento)){
            $query .= " WHERE tipo_evento = :tipo_evento";
        }

        $query .= " ORDER BY fecha DESC";

        $stmt = $this->conn->prepare($query);

        // Si se proporciona un tipo de evento, vincular el parámetro
        if(isset($this->tipo_evento) && !empty($this->tipo_evento)){
            $this->tipo_evento = htmlspecialchars(strip_tags($this->tipo_evento));
            $stmt->bindParam(':tipo_evento', $this->tipo_evento);
        }

        $stmt->execute();
        return $stmt;
    }

    // Método para leer un solo evento
    public function read_single(){
        $query = "SELECT
                            id, nombre, descripcion, fecha, tipo_evento
                        FROM
                            " . $this->table_name . "
                        WHERE
                            id = ?
                        LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties if row exists
        if($row){
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->fecha = $row['fecha'];
            $this->tipo_evento = $row['tipo_evento'];
            // $this->created_at = $row['created_at']; // Comentamos o eliminamos esta asignación
            return true;
        }
        return false;
    }

    // Método para actualizar un evento
    public function update(){
        $query = "UPDATE " . $this->table_name . "
                  SET
                      nombre = :nombre,
                      descripcion = :descripcion,
                      fecha = :fecha,
                      tipo_evento = :tipo_evento
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar y vincular los datos
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion=htmlspecialchars(strip_tags($this->descripcion));
        $this->fecha=htmlspecialchars(strip_tags($this->fecha));
        $this->tipo_evento=htmlspecialchars(strip_tags($this->tipo_evento));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':tipo_evento', $this->tipo_evento);

        if($stmt->execute()){
            return true;
        }

        // Para depuración
        // error_log("Error al ejecutar update en Event: " . json_encode($stmt->errorInfo()));
        return false;
    }

    // Método para eliminar un evento
    public function delete(){
        // Primero, eliminar registros de eventos_registro asociados a este evento
        $query_event_registers = "DELETE FROM " . $this->events_register_table . " WHERE id_evento = ?";
        $stmt_event_registers = $this->conn->prepare($query_event_registers);
        $stmt_event_registers->bindParam(1, $this->id);
        $stmt_event_registers->execute();

        // Ahora, eliminar el evento de la tabla principal
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }

        // Para depuración
        // error_log("Error al ejecutar delete en Event: " . json_encode($stmt->errorInfo()));
        return false;
    }


    // Método para obtener eventos futuros
    public function readFutureEvents(){
        $query = "SELECT
                            id, nombre, descripcion, fecha, tipo_evento
                        FROM
                            " . $this->table_name . "
                        WHERE
                            fecha >= CURDATE()
                        ORDER BY
                            fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para registrar un usuario en un evento (Eventos_Registro)
    public function registerUserForEvent($user_id, $event_id){
        // Verifica si el usuario ya está registrado en este evento
        $query_check = "SELECT id FROM " . $this->events_register_table . " WHERE id_usuario = ? AND id_evento = ? LIMIT 0,1";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(1, $user_id);
        $stmt_check->bindParam(2, $event_id);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            return false; // Ya registrado
        }

        // Si no está registrado, procede con la inserción
        $query = "INSERT INTO " . $this->events_register_table . "
                    SET
                        id_usuario = :id_usuario,
                        id_evento = :id_evento,
                        fecha_registro = NOW()"; // Añade la fecha de registro

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_usuario", $user_id);
        $stmt->bindParam(":id_evento", $event_id);

        if($stmt->execute()){
            return true;
        }

        // error_log("Error al registrar usuario en Event: " . json_encode($stmt->errorInfo()));
        return false;
    }

    // Método para dar de baja a un usuario de un evento
    public function unregisterUserFromEvent($user_id, $event_id){
        $query = "DELETE FROM " . $this->events_register_table . " WHERE id_usuario = ? AND id_evento = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $event_id);

        if($stmt->execute()){
            // Comprobar si se eliminó alguna fila
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    // Método para obtener eventos populares (con más de N inscritos)
    public function getPopularEvents($min_inscriptions){
        $query = "SELECT
                            e.id, e.nombre, e.descripcion, e.fecha, e.tipo_evento, COUNT(er.id_usuario) as total_inscritos
                        FROM
                            " . $this->table_name . " e
                        JOIN
                            " . $this->events_register_table . " er ON e.id = er.id_evento
                        GROUP BY
                            e.id, e.nombre, e.descripcion, e.fecha, e.tipo_evento
                        HAVING
                            COUNT(er.id_usuario) >= :min_inscriptions
                        ORDER BY
                            total_inscritos DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_inscriptions', $min_inscriptions, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Método para contar los eventos en los que un usuario está inscrito
    public function countEventsForUser($user_id){
        $query = "SELECT
                            COUNT(id_evento) as total_eventos_inscritos
                        FROM
                            " . $this->events_register_table . "
                        WHERE
                            id_usuario = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_eventos_inscritos'];
    }

    // Método para obtener los usuarios inscritos en un evento específico
    public function getRegisteredUsersForEvent($event_id){
        $query = "SELECT
                            u.id, u.nombres, u.apellidos, u.email, u.username
                        FROM
                            users u
                        JOIN
                            " . $this->events_register_table . " er ON u.id = er.id_usuario
                        WHERE
                            er.id_evento = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $event_id);
        $stmt->execute();
        return $stmt;
    }
}
