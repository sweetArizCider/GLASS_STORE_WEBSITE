<?php
session_start();
include '../class/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_detalle_producto'])) {
    $id_detalle_producto = $_POST['id_detalle_producto'];
    $nuevo_estado = 'en carrito'; // Estado que deseas cambiar

    // Crear conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();
    $conexion->configurarConexionPorRol();

    // Consulta para actualizar el estado del producto
    $consulta = "UPDATE detalle_producto SET estatus = ? WHERE id_detalle_producto = ?";
    $params = [$nuevo_estado, $id_detalle_producto];

    $resultado = $conexion->actualizar($consulta, $params);

    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del producto']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID del detalle del producto no proporcionado']);
}
?>
