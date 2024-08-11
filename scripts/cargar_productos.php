<?php
session_start();
include '../class/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$productos_por_pagina = isset($_GET['productos_por_pagina']) ? intval($_GET['productos_por_pagina']) : 8;
$offset = ($page - 1) * $productos_por_pagina;

$conexion = new database();
$conexion->conectarDB();
$conexion->configurarConexionPorRol();

$consulta_productos = "
    SELECT p.id_producto, p.nombre, p.precio, i.imagen
    FROM productos p
    LEFT JOIN imagen i ON p.id_producto = i.producto
    WHERE p.estatus = 'activo' 
    AND (p.nombre LIKE :search)
    LIMIT :limit OFFSET :offset
";

$stmt = $conexion->getPDO()->prepare($consulta_productos);
$search_param = "%$search%";
$stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt->bindParam(':limit', $productos_por_pagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_OBJ);

header('Content-Type: application/json');
echo json_encode(['productos' => $productos]);

