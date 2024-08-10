<?php
session_start();

// Verifica que el usuario haya iniciado sesión
if (!isset($_SESSION["nom_usuario"])) {
    header("Location: iniciarSesion.php");
    exit();
}

// Conectar a la base de datos
include '../class/database.php'; 
$conexion = new Database();
$conexion->conectarDB();

$tipo = $_POST['tipo'];
$fecha = $_POST['selected_date'];
$hora = $_POST['hora'];
$direccion = $_POST['direccion'];
$notas = $_POST['motivo'];

// Verificar que las variables requeridas no estén vacías
if (empty($tipo) || empty($fecha) || empty($hora) || empty($direccion) || empty($notas)) {
    echo "Todos los campos son obligatorios.";
    exit();
}

try {
    // Obtener id_cliente de la sesión usando las tablas usuarios y persona
    $consulta_cliente = "SELECT c.id_cliente 
                         FROM cliente c
                         INNER JOIN persona p ON c.persona = p.id_persona
                         INNER JOIN usuarios u ON p.usuario = u.id_usuario
                         WHERE u.nom_usuario = :usuario";
    $stmt_cliente = $conexion->getPDO()->prepare($consulta_cliente);
    $stmt_cliente->bindParam(':usuario', $_SESSION['nom_usuario']);
    $stmt_cliente->execute();
    $id_cliente = $stmt_cliente->fetchColumn();

    if ($id_cliente === false) {
        echo "No se encontró el cliente para el usuario.";
        exit();
    }

    // Obtener el id_cliente_direcciones correspondiente
    $consulta_direccion = "SELECT id_cliente_direcciones FROM cliente_direcciones WHERE cliente = :cliente AND direccion = :direccion";
    $stmt_direccion = $conexion->getPDO()->prepare($consulta_direccion);
    $stmt_direccion->bindParam(':cliente', $id_cliente);
    $stmt_direccion->bindParam(':direccion', $direccion);
    $stmt_direccion->execute();
    $id_cliente_direcciones = $stmt_direccion->fetchColumn();

    if ($id_cliente_direcciones === false) {
        echo "No se encontró una dirección válida para el cliente.";
        exit();
    }

    // Preparar la consulta INSERT
    $consulta = "INSERT INTO CITAS (tipo, fecha, hora, cliente_direccion, notas, estatus)
    VALUES (:tipo, :fecha, :hora, :direccion, :notas, 'en espera')";
    
    $stmt = $conexion->getPDO()->prepare($consulta);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':direccion', $id_cliente_direcciones); // Usar el id_cliente_direcciones
    $stmt->bindParam(':notas', $notas);
    $stmt->execute();

    echo "Cita agendada con éxito.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión a la base de datos
$conexion->desconectarDB();
header("refresh:3; ../index.php");  // Redirige
?>
