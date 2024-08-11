<?php
session_start();
include '../class/database.php';

if (isset($_POST['id_producto']) && isset($_POST['id_usuario'])) {
    $id_producto = $_POST['id_producto'];
    $id_usuario = $_POST['id_usuario'];

    // Crear conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();
    $conexion->configurarConexionPorRol();

    // Eliminar el favorito de la base de datos
    $consulta = "CALL eliminar_favorito(?, ?)";
    $params = [$id_producto, $id_usuario];
    $resultado = $conexion->ejecutar($consulta, $params);

    if ($resultado) {
        echo 'Producto eliminado de favoritos.';
    } else {
        http_response_code(500);
        echo 'Error al eliminar el producto de favoritos.';
    }
} else {
    http_response_code(400);
    echo 'Datos insuficientes para eliminar de favoritos.';
}
?>
