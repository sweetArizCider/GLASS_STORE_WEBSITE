<?php
session_start();
header('Content-Type: application/json');

// Obtener la fecha desde la solicitud GET
$fecha = $_GET['fecha'];

// Crear una instancia de la clase database
require_once 'path/to/database.php'; // Asegúrate de incluir la ruta correcta a tu archivo database.php
$db = new database();
$db->conectarDB(); // Conectar a la base de datos
$db->configurarConexionPorRol();

// Preparar la consulta SQL
$sql = "SELECT hora FROM citas WHERE fecha = :fecha AND estatus IN ('aceptada', 'en espera')";

// Ejecutar la consulta con parámetros
$params = ['fecha' => $fecha];
$resultado = $db->executeQueryWithParams($sql, $params);

// Procesar los resultados
$horariosOcupados = [];
foreach ($resultado as $row) {
    $horariosOcupados[] = $row->hora;
}

// Enviar los resultados en formato JSON
echo json_encode($horariosOcupados);

// Desconectar de la base de datos
$db->desconectarDB();
?>
