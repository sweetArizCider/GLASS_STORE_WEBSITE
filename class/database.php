<?php
  


class database{
    // paramentros que le voy a enviar al objeto pdo
    private $PDOlocal;
    private $user = 'arizpe1';
    private $password = "arizpe1";
    private $server = "mysql:host=18.246.191.85;dbname=glass_store_ana";

    // le ponemos la sig cadena: host, base de datos
    function conectarDB()
    // vamos a instanciar PDO
    {
        try{
        $this->PDOlocal = new PDO ($this->server, $this->user, $this->password);
        }
        catch (PDOException $e)
        // excepciones del PDO
        {
            echo $e->getMessage();
        }
    }

    function desconectarDB()
    {
        try{
            $this->PDOlocal = null;
            }
            catch (PDOException $e)
            // excepciones del PDO
            {
                echo $e->getMessage();
            }
    }
    
    // Iniciar una transacción
    public function beginTransaction() {
        $this->PDOlocal->beginTransaction();
    }

    // Confirmar una transacción
    public function commit() {
        $this->PDOlocal->commit();
    }

    // Revertir una transacción
    public function rollBack() {
        $this->PDOlocal->rollBack();
    }

    function seleccionar($consulta, $params = [])
{
    try
    {
        // Preparar la consulta
        $stmt = $this->PDOlocal->prepare($consulta);

        // Ejecutar la consulta con parámetros si existen
        $stmt->execute($params);

        // Obtener todos los resultados
        $fila = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $fila;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage();
        return null; // Opcional: devolver null en caso de error
    }
}


    function ejecuta($consulta)
    {
        try
        {
            $this->PDOlocal->query($consulta);
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    function ejecutar($query, $params = []) {
        $stmt = $this->PDOlocal->prepare($query);
        try {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key +1, $value); // Aquí usamos directamente $key
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false; // Asegúrate de devolver un valor en caso de error
        }
    }

    function ejecutar1($query, $params = []) {
        $stmt = $this->PDOlocal->prepare($query);
        try {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key , $value); // Aquí usamos directamente $key
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false; // Asegúrate de devolver un valor en caso de error
        }
    }

    
    function verificar($usuario, $contra)
    {
        try {
            $stmt = $this->PDOlocal->prepare("CALL autenticacion(:p_nom_usuario, :p_password)");
            $stmt->bindParam(':p_nom_usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':p_password', $contra, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
    
            if ($resultado) {
                $mensaje = $resultado->mensaje;
    
                if ($mensaje == 'Autenticación exitosa') {
                    session_start();
                    $_SESSION["nom_usuario"] = $usuario;
                    $_SESSION["rol"] = $resultado->rol; // Asumiendo que el procedimiento almacenado devuelve el rol
                    echo "<div class='alert alert-success'>";
                    echo "<h2 align='center'> BIENVENIDO ". $_SESSION["nom_usuario"]. "</h2>";
                    echo "</div>";
                    header("refresh:2;../index.php");
                } else {
                    echo "<div class='alert alert-danger'>";
                    echo "<h2 align='center'> ". $mensaje ."</h2>";
                    echo "</div>";
                    header("refresh:2;../views/iniciarSesion.php");
                }
            }
        } catch (PDOException $e) {
            $mensajeError = $e->getMessage();
            $mensaje = substr($mensajeError, strpos($mensajeError, ': ') + 2);
            echo "<div class='alert alert-danger'>";
            echo "<h2 align='center'> ". $mensaje ."</h2>";
            echo "</div>";
            header("refresh:2;../views/iniciarSesion.php");
        }
    }

    // Método para preparar una consulta
public function prepareQuery($query) {
    try {
        // Usar el objeto PDO directamente para preparar la consulta
        return $this->PDOlocal->prepare($query);
    } catch (PDOException $e) {
        // Manejo de errores
        echo "Error en la preparación de la consulta: " . $e->getMessage();
        return false; // Retornar false en caso de error
    }
}


    // Método para ejecutar una consulta sin parámetros
    public function executeQuery($query) {
        try {
            $stmt = $this->prepareQuery($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Método para ejecutar una consulta con parámetros
    public function executeQueryWithParams($query, $params) {
        try {
            $stmt = $this->prepareQuery($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function cerrarSesion()
    {
        session_start();
        session_destroy();
        header ("Location: ../index.php");
    }

    // Método para buscar productos por nombre
    function BuscarProductoPorNombre($nombreBuscado) {
        try {
            $stmt = $this->PDOlocal->prepare("CALL buscarproductopornombre(:nombreBuscado)");
            $stmt->bindParam(':nombreBuscado', $nombreBuscado, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ); // Asegúrate de que los resultados se obtienen como objetos
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    

    function ejecutarProcedimiento($query, $params = []) {
        $this->conectarDB();
        try {
            $stmt = $this->PDOlocal->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $resultados;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error en la ejecución del procedimiento: " . $e->getMessage() . "</div>";
            return [];
        } finally {
            $this->desconectarDB();
        }
    }

     // Obtener el objeto PDO
    function getPDO() {
        return $this->PDOlocal;
    }

    function obtenerNotificacionesInstalador($id_instalador) {
        try {
            $stmt = $this->PDOlocal->prepare("CALL obtener_notificaciones_instalador(:id_instalador)");
            $stmt->bindParam(':id_instalador', $id_instalador, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function obtenerCitasInstalador($id_instalador) {
        $sql = "CALL obtener_citas_instalador(?)";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->execute([$id_instalador]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function agregarFavorito($id_usuario, $id_producto) {
        $consulta = "CALL insertarfavorito(?, ?)";
        $params = [$id_usuario, $id_producto];
        return $this->ejecutar($consulta, $params);
    }

    function eliminarFavorito($id_producto, $id_usuario) {
        try {
            $stmt = $this->PDOlocal->prepare("DELETE FROM favoritos WHERE cliente = :id_usuario AND producto = :id_producto");
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    function esFavorito($id_producto, $id_usuario) {
        try {
            $stmt = $this->PDOlocal->prepare("SELECT COUNT(*) as count FROM favoritos WHERE cliente = :id_usuario AND producto = :id_producto");
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado->count > 0;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


/* como la cambiaste esto ya no se usa
    function obtenerFavoritos($id_usuario) {
        try {
            $stmt = $this->PDOlocal->prepare("
                SELECT P.nombre, P.descripcion, P.precio, I.imagen 
                FROM FAVORITOS F 
                INNER JOIN PRODUCTOS P ON F.producto = P.id_producto 
                INNER JOIN IMAGEN I ON P.id_producto = I.producto 
                WHERE F.cliente = :id_usuario AND P.estatus = 'activo'
            ");
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }*/
    public function actualizar($consulta, $params) {
        try {
            $stmt = $this->PDOlocal->prepare($consulta);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ultimoID() {
        return $this->PDOlocal->lastInsertId();
    }

    function ejecutarcita($query, $params = []) {
        try {
            $stmt = $this->PDOlocal->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false; // Asegúrate de devolver un valor en caso de error
        }
    }
    
    public function ObtenerProductosPredeterminados($excluidos = []) {
        try {
            $query = "
                SELECT DISTINCT P.id_producto, P.nombre, P.precio, 
                    (SELECT I.imagen 
                     FROM imagen I 
                     WHERE I.producto = P.id_producto 
                     LIMIT 1) AS imagen
                FROM productos P
                WHERE P.estatus = 'activo'
                " . (!empty($excluidos) ? "AND P.id_producto NOT IN (" . implode(',', $excluidos) . ")" : "") . "
                ORDER BY RAND()
                LIMIT 16
            ";
            $stmt = $this->PDOlocal->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ); // Devuelve los resultados como objetos
        } catch (PDOException $e) {
            echo $e->getMessage();
            return []; // Devuelve un array vacío en caso de error
        } 
    }
    
    public function buscarProductos($termino) {
    
        try {
            $query = "SELECT id_producto, nombre FROM PRODUCTOS WHERE nombre LIKE :termino AND estatus = 'activo' LIMIT 10";
            $stmt = $this->PDOlocal->prepare($query);
            $termino = "%$termino%";
            $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ); // Devuelve los resultados como objetos
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    function ejecutarProcedimiento2($query, $params = []) {
        $this->conectarDB();
        try {
            $stmt = $this->PDOlocal->prepare($query);
            // Ajustar los índices para que comiencen en 1
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
            $stmt->execute();
            return $stmt->rowCount(); // Devolver el número de filas afectadas
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error en la ejecución del procedimiento: " . $e->getMessage() . "</div>";
            return 0;
        } finally {
            $this->desconectarDB();
        }
    }
    
    
    
    
}
?>