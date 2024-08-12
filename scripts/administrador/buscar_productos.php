<?php
include '../../class/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$productosPorPagina = 10;
$offset = ($page - 1) * $productosPorPagina;

$db = new Database();
$db->conectarDB();

$query = "
    SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.estatus, c.nombre AS categoria 
    FROM productos p
    JOIN categorias c ON p.categoria = c.id_categoria
    WHERE p.nombre LIKE :search OR c.nombre LIKE :search
    LIMIT $productosPorPagina OFFSET $offset
";

$params = [
    ':search' => '%' . $search . '%'
];

$productos = $db->seleccionar($query, $params);

header('Content-Type: application/json');
echo json_encode(['productos' => $productos]);
?>
