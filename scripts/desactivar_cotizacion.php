<?php
include '../class/database.php';

if (isset($_POST['id_detalle_producto'])) {
    $id_detalle_producto = $_POST['id_detalle_producto'];

    // Crear conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();

    // Actualizar el estado del producto a "desactivada"
    $consulta = "UPDATE detalle_producto SET estatus = 'desactivada' WHERE id_detalle_producto = ?";
    $params = [$id_detalle_producto];
    $resultado = $conexion->ejecutar($consulta, $params);

    if ($resultado) {
        echo "Producto desactivado correctamente.";
    } else {
        echo "Error al desactivar el producto.";
    }
}
?>
