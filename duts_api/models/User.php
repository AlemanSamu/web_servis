<?php
class User {
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "users"; // Nombre de tu tabla de usuarios
    private $events_register_table = "eventos_registro"; // Nombre de la tabla de registro de eventos
    private $duts_transactions_table = "duts_transactions"; // Nombre de tu tabla de transacciones DUTS

    // Propiedades del objeto (asegúrate de que estas coincidan con tus columnas de la tabla 'users')
    public $id;
    public $nombres;
    public $apellidos;
    public $email;
    public $ciudad;
    public $pais;
    public $descripcion;
    public $intereses;
    public $programa;
    public $semestre;
    public $username;
    public $password; // Almacenará el password sin hashear temporalmente, luego se hashea
    public $rol;
    public $created_at; // Esta columna SÍ existe en tu tabla 'users'

    // Constructor con $db como conexión a la base de datos
    public function __construct($db){
        $this->conn = $db;
    }

    // Método para crear un nuevo usuario
    public function create(){
        // Consulta para insertar un registro
        $query = "INSERT INTO " . $this->table_name . "
                    SET
                        nombres = :nombres,
                        apellidos = :apellidos,
                        email = :email,
                        ciudad = :ciudad,
                        pais = :pais,
                        descripcion = :descripcion,
                        intereses = :intereses,
                        programa = :programa,
                        semestre = :semestre,
                        username = :username,
                        password = :password,
                        rol = :rol";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Limpiar los datos
        $this->nombres=htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos=htmlspecialchars(strip_tags($this->apellidos));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->ciudad=htmlspecialchars(strip_tags($this->ciudad));
        $this->pais=htmlspecialchars(strip_tags($this->pais));
        $this->descripcion=htmlspecialchars(strip_tags($this->descripcion));
        $this->intereses=htmlspecialchars(strip_tags($this->intereses));
        $this->programa=htmlspecialchars(strip_tags($this->programa));
        $this->semestre = isset($this->semestre) && $this->semestre !== '' ? (int) $this->semestre : null;
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->rol=strtolower(htmlspecialchars(strip_tags($this->rol)));

        // Vincular los valores
        $stmt->bindParam(":nombres", $this->nombres);
        $stmt->bindParam(":apellidos", $this->apellidos);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":ciudad", $this->ciudad);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":intereses", $this->intereses);
        $stmt->bindParam(":programa", $this->programa);
        $stmt->bindParam(":semestre", $this->semestre);
        $stmt->bindParam(":username", $this->username);

        // Hashear la contraseña antes de guardar en la base de datos
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":rol", $this->rol);

        // Ejecutar la consulta
        if($stmt->execute()){
            return true;
        }

        // Para depuración, puedes activar esto:
        // error_log("Error al ejecutar create en User: " . json_encode($stmt->errorInfo()));
        return false;
    }

    // Método para verificar si el email o username ya existe para REGISTRO (sin ID a excluir)
    public function exists(){
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ? OR email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare( $query );

        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->email);

        $stmt->execute();

        $num = $stmt->rowCount();

        return $num > 0;
    }

    // Método para verificar si email o username ya existen, EXCLUYENDO al usuario actual (para UPDATE)
    public function usernameOrEmailExistsExceptCurrent(){
        // Consulta para verificar si el email o username ya existen, excluyendo al usuario con el ID actual
        $query = "SELECT id
                  FROM " . $this->table_name . "
                  WHERE (email = :email OR username = :username) AND id != :id
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->id=htmlspecialchars(strip_tags($this->id)); // Asegúrate de vincular el ID actual

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $num = $stmt->rowCount();

        return $num > 0;
    }


    // Método para iniciar sesión de usuario
    public function login(){
        $query = "SELECT
                            id, nombres, apellidos, email, rol, password
                        FROM
                            " . $this->table_name . "
                        WHERE
                            username = ?
                        LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->username=htmlspecialchars(strip_tags($this->username));

        $stmt->bindParam(1, $this->username);

        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->nombres = $row['nombres'];
            $this->apellidos = $row['apellidos'];
            $this->email = $row['email'];
            $this->rol = $row['rol'];
            $hashed_password = $row['password'];

            if(password_verify($this->password, $hashed_password)){
                return true;
            }
        }
        return false;
    }

    // Método para leer todos los usuarios
    public function read(){
        $query = "SELECT
                            id, nombres, apellidos, email, ciudad, pais,
                            descripcion, intereses, programa, semestre,
                            rol, username, created_at
                        FROM
                            " . $this->table_name . "
                        ORDER BY
                            created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para leer un solo usuario
    public function read_single(){
        $query = "SELECT
                            id, nombres, apellidos, email, ciudad, pais, descripcion, intereses, programa, semestre, username, rol, created_at
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
            $this->nombres = $row['nombres'];
            $this->apellidos = $row['apellidos'];
            $this->email = $row['email'];
            $this->ciudad = $row['ciudad'];
            $this->pais = $row['pais'];
            $this->descripcion = $row['descripcion'];
            $this->intereses = $row['intereses'];
            $this->programa = $row['programa'];
            $this->semestre = $row['semestre'];
            $this->username = $row['username'];
            $this->rol = $row['rol'];
            $this->created_at = $row['created_at']; // Corrected: Assign created_at
            return true;
        }
        return false;
    }

    // *** MÉTODO CRÍTICO PARA ACTUALIZAR USUARIO! ***
    public function update(){
        $query = "UPDATE " . $this->table_name . "
                  SET
                      nombres = :nombres,
                      apellidos = :apellidos,
                      email = :email,
                      ciudad = :ciudad,
                      pais = :pais,
                      descripcion = :descripcion,
                      intereses = :intereses,
                      programa = :programa,
                      semestre = :semestre,
                      username = :username,
                      rol = :rol";

        // Si se proporciona una nueva contraseña (no es nula o vacía), la incluimos en la actualización
        if(!empty($this->password)){
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar los datos
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->nombres=htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos=htmlspecialchars(strip_tags($this->apellidos));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->ciudad=htmlspecialchars(strip_tags($this->ciudad));
        $this->pais=htmlspecialchars(strip_tags($this->pais));
        $this->descripcion=htmlspecialchars(strip_tags($this->descripcion));
        $this->intereses=htmlspecialchars(strip_tags($this->intereses));
        $this->programa=htmlspecialchars(strip_tags($this->programa));
        $this->semestre = isset($this->semestre) && $this->semestre !== '' ? (int) $this->semestre : null; // Asegura tipo int o null
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->rol=strtolower(htmlspecialchars(strip_tags($this->rol)));

        // Vincular los valores
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ciudad', $this->ciudad);
        $stmt->bindParam(':pais', $this->pais);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':intereses', $this->intereses);
        $stmt->bindParam(':programa', $this->programa);
        $stmt->bindParam(':semestre', $this->semestre);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':rol', $this->rol);

        // Hashing de la contraseña si se proporciona
        if(!empty($this->password)){
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        if($stmt->execute()){
            return true;
        }

        // Para depuración
        // error_log("Error al ejecutar update en User: " . json_encode($stmt->errorInfo()));
        return false;
    }


    // Método para obtener los top 5 usuarios por saldo de DUTS
    // NOTA: Esta función asume que 'duts_transactions' existe para calcular el saldo.
    // Si esta tabla no existe o no tiene las columnas correctas, generará errores.
    public function getTopUsersByDUTS(){
        $query = "SELECT
                            u.id,
                            u.nombres,
                            u.apellidos,
                            u.username,
                            COALESCE(SUM(CASE WHEN t.id_destino = u.id THEN t.cantidad ELSE 0 END) -
                                     SUM(CASE WHEN t.id_origen = u.id THEN t.cantidad ELSE 0 END), 0) AS saldo
                          FROM
                            " . $this->table_name . " u
                          LEFT JOIN
                            " . $this->duts_transactions_table . " t ON u.id = t.id_origen OR u.id = t.id_destino
                          GROUP BY
                            u.id, u.nombres, u.apellidos, u.username
                          ORDER BY
                            saldo DESC
                          LIMIT 0, 5"; // Limita a los 5 mejores

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // ¡NUEVO MÉTODO! Para obtener los 5 usuarios con menos DUTS
    // NOTA: Esta función asume que 'duts_transactions' existe para calcular el saldo.
    public function getLeastUsersByDUTS(){
        $query = "SELECT
                            u.id,
                            u.nombres,
                            u.apellidos,
                            u.username,
                            COALESCE(SUM(CASE WHEN t.id_destino = u.id THEN t.cantidad ELSE 0 END) -
                                     SUM(CASE WHEN t.id_origen = u.id THEN t.cantidad ELSE 0 END), 0) AS saldo
                          FROM
                            " . $this->table_name . " u
                          LEFT JOIN
                            " . $this->duts_transactions_table . " t ON u.id = t.id_origen OR u.id = t.id_destino
                          GROUP BY
                            u.id, u.nombres, u.apellidos, u.username
                          ORDER BY
                            saldo ASC
                          LIMIT 0, 5"; // Ordena ascendente para obtener los que tienen menos

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Método para eliminar un usuario (ya lo revisamos para la cascada)
    public function delete() {
        // Eliminar registros de duts_transactions donde el usuario sea origen o destino
        $query_duts_transactions = "DELETE FROM " . $this->duts_transactions_table . " WHERE id_origen = ? OR id_destino = ?";
        $stmt_duts_transactions = $this->conn->prepare($query_duts_transactions);
        $stmt_duts_transactions->bindParam(1, $this->id);
        $stmt_duts_transactions->bindParam(2, $this->id);
        $stmt_duts_transactions->execute();

        // Eliminar registros de eventos_registro donde el usuario esté inscrito
        $query_event_registers = "DELETE FROM " . $this->events_register_table . " WHERE id_usuario = ?";
        $stmt_event_registers = $this->conn->prepare($query_event_registers);
        $stmt_event_registers->bindParam(1, $this->id);
        $stmt_event_registers->execute();

        // Ahora, eliminar el usuario de la tabla principal
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // ¡MÉTODO AÑADIDO! Para obtener usuarios que no están registrados en ningún evento
    public function getUsersWithoutEvents(){
        $query = "SELECT
                            u.id, u.nombres, u.apellidos, u.email, u.username, u.rol
                        FROM
                            " . $this->table_name . " u
                        LEFT JOIN
                            " . $this->events_register_table . " er ON u.id = er.id_usuario
                        WHERE
                            er.id_usuario IS NULL
                        GROUP BY
                            u.id, u.nombres, u.apellidos, u.email, u.username, u.rol
                        ORDER BY
                            u.nombres ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
