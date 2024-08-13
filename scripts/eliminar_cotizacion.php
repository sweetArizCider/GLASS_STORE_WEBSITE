<?php
include '../class/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_detalle_producto'])) {
        $id_detalle_producto = $_POST['id_detalle_producto'];

        // Conectar a la base de datos
        $conexion = new database();
        $conexion->conectarDB();

        // Actualizar el estatus a 'rechazado'
        $consulta = "UPDATE detalle_producto SET estatus = 'rechazado' WHERE id_detalle_producto = ?";
        $params = [$id_detalle_producto];

        if ($conexion->ejecutar($consulta, $params)) {
            echo json_encode(['success' => true, 'message' => 'Cotización eliminada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la cotización.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de detalle de producto no proporcionado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
