<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Asignar Cita</title>
</head>
<body>
<?php
include '../../class/database.php';
$db = new Database();
$db->conectarDB();

if (isset($_POST['id_cita']) && isset($_POST['instaladores'])) {
    $id_cita = $_POST['id_cita'];
    $instaladores = $_POST['instaladores'];

    try {
        $db->beginTransaction();

        // Ejecutar el procedimiento almacenado
        $db->ejecutarcita("CALL AceptarCita(:id_cita)", [':id_cita' => $id_cita]);

        foreach ($instaladores as $id_instalador) {
            // Insertar en la tabla INSTALADOR_CITA
            $db->ejecutarcita("INSERT INTO INSTALADOR_CITA (cita, instalador) VALUES (:cita, :instalador)", [
                ':cita' => $id_cita,
                ':instalador' => $id_instalador
            ]);

            // Obtener detalles de la cita usando un SELECT
            $stmt = $db->ejecutarcita("SELECT
                CONCAT(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m) AS nombre_cliente,
                c.fecha,
                c.hora,
                CONCAT(d.calle, ' ', d.numero, ' ', d.numero_int, ', ', d.colonia, ', ', d.ciudad, '. Referencias: ', d.referencias) AS direccion,
                c.notas,
                IFNULL(
                    REPLACE(
                        (SELECT GROUP_CONCAT(CONCAT(prod.nombre, ': ', dp.monto, ' Alto: ', dp.alto, ', Largo: ', dp.largo) SEPARATOR '\n')
                        FROM DETALLE_CITA dc 
                        JOIN DETALLE_PRODUCTO dp ON dc.detalle_producto = dp.id_detalle_producto 
                        JOIN PRODUCTOS prod ON dp.producto = prod.id_producto 
                        WHERE dc.cita = c.id_cita), 
                        ',', '\n'
                    ),
                    'No hay cotizaciones'
                ) AS cotizaciones
            FROM
                CITAS c
            JOIN
                CLIENTE_DIRECCIONES cd ON c.cliente_direccion = cd.id_cliente_direcciones
            JOIN
                CLIENTE cli ON cd.cliente = cli.id_cliente
            JOIN
                PERSONA p ON cli.persona = p.id_persona
            JOIN
                DIRECCIONES d ON cd.direccion = d.id_direccion
            WHERE
                c.id_cita = :id_cita", [':id_cita' => $id_cita]);

            // Obtener el resultado de la consulta
            $citaDetalles = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($citaDetalles) {
                $notificacion = "Se ha asignado una cita para el cliente {$citaDetalles['nombre_cliente']} el {$citaDetalles['fecha']} a las {$citaDetalles['hora']}. Dirección: {$citaDetalles['direccion']}. Notas: {$citaDetalles['notas']}. Cotizaciones: {$citaDetalles['cotizaciones']}";
            } else {
                $notificacion = "No hay detalles disponibles para la cita.";
            }
            
            // Insertar notificación
            $db->ejecutarcita("INSERT INTO NOTIFICACIONES_INSTALADOR (instalador, notificacion) VALUES (:instalador, :notificacion)", [
                ':instalador' => $id_instalador,
                ':notificacion' => $notificacion
            ]);
        }

        $db->commit();

        echo "<div class='alert alert-success'>INSTALADORES ASIGNADOS Y CITA ACEPTADA EXITOSAMENTE</div>";
        header("refresh:3; url=../../views/administrador/vista_admin_citas.php");
    } catch (Exception $e) {
        $db->rollBack();
        echo "<div class='alert alert-danger'>No se pudo asignar instaladores ni aceptar la cita: {$e->getMessage()}</div>";
        header("refresh:3; url=../../views/administrador/vista_admin_citas.php");
    }
} else {
    header("Location: citas.php?status=error&message=Datos incompletos.");
}
?>
</body>
</html>
