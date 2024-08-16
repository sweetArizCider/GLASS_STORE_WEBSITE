<?php
include '../class/database.php';

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];

    $conexion = new Database();
    $conexion->conectarDB();

    $query = "CALL consultar_horas_disponibles(?)";
    $horarios = $conexion->seleccionar($query, [$fecha]);

    header('Content-Type: application/json');
    echo json_encode($horarios);
}
?>
