<?php
session_start();
include '../class/database.php';
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nom_usuario'])) {
    echo json_encode(['error' => 'Debe iniciar sesión para agregar productos a favoritos.']);
    exit();
}

// Verificar que id_producto esté definido en POST
if (!isset($_POST['id_producto']) || empty($_POST['id_producto'])) {
    echo json_encode(['error' => 'Datos de POST faltantes.']);
    exit();
}

// Obtener id_producto desde POST
$id_producto = intval($_POST['id_producto']);

// Obtener id_usuario desde la sesión
$id_usuario = 0;
if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    $conexion = new database();
    $conexion->conectarDB();

    $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
    $params_ids = [$user];
    $resultado_ids = $conexion->seleccionar($consulta_ids, $params_ids);

    if ($resultado_ids && !empty($resultado_ids)) {
        $fila = $resultado_ids[0];
        $id_usuario = isset($fila->id_cliente) ? $fila->id_cliente : (isset($fila->id_instalador) ? $fila->id_instalador : 0);
    }
}

// Verificar que id_usuario fue encontrado
if ($id_usuario === 0) {
    echo json_encode(['error' => 'ID de usuario no encontrado.']);
    exit();
}

// Verificar si el producto ya está en favoritos
$esFavorito = $conexion->esFavorito($id_producto, $id_usuario);

try {
    if ($esFavorito) {
        // Eliminar de favoritos
        $resultado = $conexion->eliminarFavorito($id_producto, $id_usuario);
        if ($resultado) {
            echo json_encode(['mensaje' => 'Producto eliminado de favoritos.']);
        } else {
            echo json_encode(['error' => 'No se pudo eliminar el producto de favoritos.']);
        }
    } else {
        // Insertar en favoritos
        $consulta = "CALL insertarfavorito(?, ?)";
        $params = [$id_usuario, $id_producto];
        $resultado = $conexion->ejecutar($consulta, $params);

        if ($resultado) {
            echo json_encode(['success' => true, 'mensaje' => 'Producto agregado a favoritos.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo insertar el favorito.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al ejecutar el procedimiento: ' . $e->getMessage()]);
}
