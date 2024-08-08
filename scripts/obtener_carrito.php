<?php
session_start();
include '../class/database.php';

$id_usuario = 0;

if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Crear conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();

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
                $id_usuario = $fila->id_cliente;
            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_usuario = $fila->id_instalador;
            } 
        }
    }
}

if ($id_usuario > 0) {
    $consulta_carrito = "CALL carrito(?)";
    $params_carrito = [$id_usuario];
    $resultado_carrito = $conexion->seleccionar($consulta_carrito, $params_carrito);

    echo json_encode($resultado_carrito);
} else {
    echo json_encode([]);
}
?>
