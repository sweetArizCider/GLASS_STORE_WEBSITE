<?php

session_start();
include '../class/database.php';

$id_usuario = 0;

if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];
    $conexion = new database();
    $conexion->conectarDB();

    $consulta_rol = "CALL roles_usuario(?)";
    $params_rol = [$user];
    $resultado_rol = $conexion->seleccionar($consulta_rol, $params_rol);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;

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

if ($id_usuario) {
    $consultaFavoritos = "CALL obtener_favoritos_id_cliente(?)";
    $paramsFavoritos = [$id_usuario];
    $favoritos = $conexion->seleccionar($consultaFavoritos, $paramsFavoritos);

    echo json_encode($favoritos);
} else {
    echo json_encode([]);
}



/*
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['nom_usuario'])) {
    echo json_encode(['error' => 'Debe iniciar sesión para obtener los favoritos.']);
    exit();
}

include '../class/database.php';
$conexion = new database();
$conexion->conectarDB();

$user = $_SESSION["nom_usuario"];
$consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
$params_ids = [$user];
$resultado_ids = $conexion->seleccionar($consulta_ids, $params_ids);

if ($resultado_ids && !empty($resultado_ids)) {
    $fila = $resultado_ids[0];
    $id_usuario = isset($fila->id_cliente) ? $fila->id_cliente : (isset($fila->id_instalador) ? $fila->id_instalador : 0);
}

if ($id_usuario === 0) {
    echo json_encode(['error' => 'ID de usuario no encontrado.']);
    exit();
}

$consulta = "CALL obtener_favoritos_id_cliente(?)";
$params = [$id_usuario];
$favoritos = $conexion->seleccionar($consulta, $params);

echo json_encode($favoritos);
*/
?>
