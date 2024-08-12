<?php
session_start();
include '../class/database.php';

$user = $_SESSION["nom_usuario"] ?? null;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$productos_por_pagina = isset($_GET['productos_por_pagina']) ? intval($_GET['productos_por_pagina']) : 8;
$offset = ($page - 1) * $productos_por_pagina;

$conexion = new database();
$conexion->conectarDB();

// Consulta para obtener los productos
$consulta_productos = "
    SELECT p.id_producto, p.nombre, p.precio, MIN(i.imagen) as imagen
    FROM productos p
    LEFT JOIN imagen i ON p.id_producto = i.producto
    WHERE p.estatus = 'activo' 
    AND (p.nombre LIKE :search)
    GROUP BY p.id_producto
    LIMIT :limit OFFSET :offset
";

$stmt = $conexion->getPDO()->prepare($consulta_productos);
$search_param = "%$search%";
$stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt->bindParam(':limit', $productos_por_pagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_OBJ);

// Si el usuario está autenticado, obtenemos sus favoritos
if ($user) {
    $consulta_favoritos = "
        SELECT f.producto 
        FROM favoritos f
        INNER JOIN cliente c ON f.cliente = c.id_cliente
        INNER JOIN persona p ON c.persona = p.id_persona
        INNER JOIN usuarios u ON p.usuario = u.id_usuario
        WHERE u.nom_usuario = :user
    ";

    $stmt_favoritos = $conexion->getPDO()->prepare($consulta_favoritos);
    $stmt_favoritos->bindParam(':user', $user, PDO::PARAM_STR);
    $stmt_favoritos->execute();

    $favoritos = $stmt_favoritos->fetchAll(PDO::FETCH_COLUMN, 0);

    // Marcamos los productos que son favoritos
    foreach ($productos as $producto) {
        $producto->es_favorito = in_array($producto->id_producto, $favoritos);
    }
}

// Enviamos la respuesta JSON con la información de los productos
header('Content-Type: application/json');
echo json_encode(['productos' => $productos]);
?>
