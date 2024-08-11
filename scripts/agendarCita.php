<?php
session_start();
include '../class/database.php';

// Función para obtener el ID de la relación cliente-dirección
function obtenerIdClienteDireccion($cliente_id, $calle, $numero, $numero_interior, $colonia, $ciudad, $referencias, $conexion) {
    // Verificar si la dirección ya existe
    $query_verificar_direccion = "SELECT id_direccion FROM direcciones 
                                  WHERE calle = ? AND numero = ? AND numero_int = ? 
                                  AND colonia = ? AND ciudad = ? AND referencias = ?";
    $stmt_verificar_direccion = $conexion->getPDO()->prepare($query_verificar_direccion);
    $stmt_verificar_direccion->execute([$calle, $numero, $numero_interior, $colonia, $ciudad, $referencias]);
    $direccion_existente = $stmt_verificar_direccion->fetch(PDO::FETCH_ASSOC);

    if ($direccion_existente) {
        // Si la dirección existe, obtener su ID
        $direccion_id = $direccion_existente['id_direccion'];
    } else {
        // Si la dirección no existe, insertarla y obtener el ID generado
        $query_insertar_direccion = "INSERT INTO direcciones (calle, numero, numero_int, colonia, ciudad, referencias) 
                                     VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insertar_direccion = $conexion->getPDO()->prepare($query_insertar_direccion);
        $stmt_insertar_direccion->execute([$calle, $numero, $numero_interior, $colonia, $ciudad, $referencias]);

        // Obtener el ID de la nueva dirección insertada
        $direccion_id = $conexion->getPDO()->lastInsertId();
    }

    // Verificar si la relación cliente-dirección ya existe
    $query_verificar_cliente_direccion = "SELECT id_cliente_direcciones FROM cliente_direcciones 
                                          WHERE cliente = ? AND direccion = ?";
    $stmt_verificar_cliente_direccion = $conexion->getPDO()->prepare($query_verificar_cliente_direccion);
    $stmt_verificar_cliente_direccion->execute([$cliente_id, $direccion_id]);
    $cliente_direccion_existente = $stmt_verificar_cliente_direccion->fetch(PDO::FETCH_ASSOC);

    if ($cliente_direccion_existente) {
        // Si la relación cliente-dirección existe, devolver su ID
        return $cliente_direccion_existente['id_cliente_direcciones'];
    } else {
        // Si la relación cliente-dirección no existe, insertarla y obtener el ID generado
        $query_insertar_cliente_direccion = "INSERT INTO cliente_direcciones (cliente, direccion) 
                                             VALUES (?, ?)";
        $stmt_insertar_cliente_direccion = $conexion->getPDO()->prepare($query_insertar_cliente_direccion);
        $stmt_insertar_cliente_direccion->execute([$cliente_id, $direccion_id]);

        // Obtener el ID de la nueva relación cliente-dirección insertada
        return $conexion->getPDO()->lastInsertId();
    }
}

// Verificar si el usuario está autenticado
if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Crear una instancia única de la conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();
    $conexion->configurarConexionPorRol();

    // Consulta para obtener el rol del usuario basado en el nombre de usuario
    $consulta_rol = "CALL roles_usuario(?)";
    $params_rol = [$user];
    $resultado_rol = $conexion->seleccionar($consulta_rol, $params_rol);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;

        // Consulta para obtener los IDs del cliente e instalador basado en el nombre de usuario
        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $params_ids = [$user];
        $resultado_ids = $conexion->seleccionar($consulta_ids, $params_ids);

        if ($resultado_ids && !empty($resultado_ids)) {
            $fila = $resultado_ids[0];

            if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
                $id_cliente = $fila->id_cliente;
                $_SESSION['id_cliente'] = $id_cliente;  // Establecer la sesión para cliente
            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_usuario = $fila->id_instalador;
                // Establecer una sesión para instalador si es necesario
            } 
        }
    }
} else {
    echo "Error: No estás autenticado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si el cliente está autenticado
    if (!isset($_SESSION['id_cliente'])) {
        echo "Error: No estás autenticado.";
        exit;
    }

    // Obtener los datos del formulario
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $numero_interior = $_POST['numero_interior'] ?? null;
    $colonia = $_POST['colonia'];
    $ciudad = $_POST['ciudad'];
    $referencias = $_POST['referencias'] ?? null;
    $motivo = $_POST['motivo'];
    $hora = $_POST['hora'];
    $fecha = $_POST['selected_date'];
    $id_cliente = $_SESSION['id_cliente'];  // Asegúrate de que el id_cliente está en la sesión

    // Conectar a la base de datos
    $conexion = new database();
    $conexion->conectarDB();

    try {
        // Obtener el ID de la relación cliente-dirección
        $id_cliente_direccion = obtenerIdClienteDireccion($id_cliente, $calle, $numero, $numero_interior, $colonia, $ciudad, $referencias, $conexion);

        // Llamar al procedimiento almacenado para crear la cita
        $query = "CALL crear_cita(?, ?, ?, ?, ?, @id_cita)";
        $stmt = $conexion->getPDO()->prepare($query);
        $stmt->execute(['instalacion', $fecha, $hora, $id_cliente_direccion, $motivo]);

        // Obtener el ID de la cita creada
        $result = $conexion->getPDO()->query("SELECT @id_cita as id_cita");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $id_cita = $row['id_cita'];

        if ($id_cita) {
            echo "Cita creada con éxito. ID de la cita: " . $id_cita;

            // Obtener detalles_producto con estatus "en carrito" para el cliente actual
            $query_detalles_producto = "SELECT id_detalle_producto FROM detalle_producto WHERE estatus = 'en carrito' AND cliente = ?";
            $stmt_detalles_producto = $conexion->getPDO()->prepare($query_detalles_producto);
            $stmt_detalles_producto->execute([$id_cliente]);

            // Transferir los detalles del producto a detalle_cita
            while ($detalle = $stmt_detalles_producto->fetch(PDO::FETCH_ASSOC)) {
                $detalle_producto_id = $detalle['id_detalle_producto'];

                $query_detalle_cita = "INSERT INTO detalle_cita (cita, detalle_producto) VALUES (?, ?)";
                $stmt_detalle_cita = $conexion->getPDO()->prepare($query_detalle_cita);
                $stmt_detalle_cita->execute([$id_cita, $detalle_producto_id]);

                // Comprobar si la inserción fue exitosa
                if ($stmt_detalle_cita->rowCount() > 0) {
                    echo "Detalle producto ID " . $detalle_producto_id . " añadido a detalle_cita.";

                    // Opcional: Actualizar estatus del detalle_producto a 'procesado'
                    $query_actualizar_estatus = "UPDATE detalle_producto SET estatus = 'procesado' WHERE id_detalle_producto = ?";
                    $stmt_actualizar_estatus = $conexion->getPDO()->prepare($query_actualizar_estatus);
                    $stmt_actualizar_estatus->execute([$detalle_producto_id]);
                } else {
                    echo "Error al añadir detalle producto ID " . $detalle_producto_id . " a detalle_cita.";
                }
            }
        } else {
            echo "Error: No se pudo obtener el ID de la cita creada.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
