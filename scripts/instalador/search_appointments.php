<?php
session_start();
require 'db_connection.php'; // Asegúrate de que este archivo maneja la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_appointment'])) {
    $searchTerm = trim($_POST['search_appointment']);
    $appointments = searchAppointmentsByName($searchTerm);
    echo json_encode($appointments);
    exit();
}

function searchAppointmentsByName($searchTerm) {
    global $db;
    $id_instalador = $_SESSION["id_instalador"]; // Asegúrate de que el instalador esté en sesión

    // Prepara la consulta SQL
    $stmt = $db->prepare("
        SELECT c.id_cita, c.tipo, c.fecha, c.hora, cl.nombres, cl.apellido_p, cl.apellido_m, d.calle, d.numero, d.colonia
        FROM citas AS c
        JOIN cliente_direcciones AS cd ON c.cliente_direccion = cd.id_cliente_direcciones
        JOIN persona AS cl ON cd.cliente = cl.id_persona
        JOIN direcciones AS d ON cd.direccion = d.id_direccion
        WHERE c.estatus = 'activa'
        AND c.instalador = ?
        AND (cl.nombres LIKE ? OR cl.apellido_p LIKE ? OR d.calle LIKE ?)
    ");

    // Modifica el término de búsqueda para la consulta SQL
    $searchTerm = '%' . $searchTerm . '%';

    // Ejecuta la consulta con los parámetros
    $stmt->execute([$id_instalador, $searchTerm, $searchTerm, $searchTerm]);

    // Devuelve los resultados como un array asociativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
