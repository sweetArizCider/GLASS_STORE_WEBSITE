<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
session_start();
include '../../class/database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $db->conectarDB();
    
    // Obtener la lista de reportes seleccionados
    $reportes_seleccionados = isset($_POST['reportes_seleccionados']) ? $_POST['reportes_seleccionados'] : [];
    $id_cita = isset($_POST['id_cita']) ? intval($_POST['id_cita']) : 0;

    // Procesar cada reporte seleccionado
    foreach ($reportes_seleccionados as $id_reporte) {
        $stmt = $db->getPDO()->prepare("CALL actualizarestatusreporte(?, 'aceptada')");
        $stmt->execute([$id_reporte]);
    }

    // Cambiar el estatus de los reportes no seleccionados a 'rechazada' solo para los reportes de la cita actual
    if (!empty($reportes_seleccionados)) {
        $placeholders = implode(',', array_fill(0, count($reportes_seleccionados), '?'));
        $stmt = $db->getPDO()->prepare("
            UPDATE reporte 
            SET estatus = 'aceptada' 
            WHERE estatus = 'en espera'
            AND detalle_cita IN (
                SELECT id_detalle_cita 
                FROM detalle_cita 
                WHERE cita = ?
            )
            AND id_reporte NOT IN ($placeholders)
        ");
        $stmt->execute(array_merge([$id_cita], $reportes_seleccionados));
    } else {
        // Si no hay reportes seleccionados, podemos decidir si actualizar todos los reportes 'en espera' a 'rechazada' para la cita actual
        $stmt = $db->getPDO()->prepare("
            UPDATE reporte 
            SET estatus = 'rechazada'
            WHERE estatus = 'en espera'
            AND detalle_cita IN (
                SELECT id_detalle_cita 
                FROM detalle_cita 
                WHERE cita = ?
            )
        ");
        $stmt->execute([$id_cita]);
    }
    
    $db->desconectarDB();
    
    // Redirigir de vuelta a la página de citas
    echo "<div class='alert alert-success'>REPORTES GUARDADOS CORRECTAMENTE</div>";
    header("Location: ../../views/administrador/vista_admin_reporte.php");
    exit();
}
?>


</body>
</html>
