<?php

include '../../class/database.php';

$db = new Database();
$db->conectarDB(); 
session_start();
$db->configurarConexionPorRol()

$pdo = $db->getPDO();

$idVenta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;

header('Content-Type: application/json');

if ($idVenta) {
    try {
        $query = "CALL verpromocionesporventa(:idVenta)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':idVenta', $idVenta, PDO::PARAM_INT);
        $stmt->execute();

        $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($promociones);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al recuperar las promociones: ' . htmlspecialchars($e->getMessage())]);
    }
} else {
    echo json_encode(['error' => 'ID de venta no válido.']);
}
?>
