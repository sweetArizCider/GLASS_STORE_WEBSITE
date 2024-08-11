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

    // Insertar el favorito en la base de datos
    $consulta = "CALL agregar_favorito(?, ?)";
    $params = [$id_producto, $id_usuario];
    $resultado = $conexion->ejecutar($consulta, $params);

    if ($resultado) {
        echo 'Producto agregado a favoritos.';
    } else {
        http_response_code(500);
        echo 'Error al agregar el producto a favoritos.';
    }
} else {
    http_response_code(400);
    echo 'Datos insuficientes para agregar a favoritos.';
}
?>
