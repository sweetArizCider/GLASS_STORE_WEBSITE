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
        $db->ejecutarcita("CALL aceptarcita(:id_cita)", [':id_cita' => $id_cita]);

        foreach ($instaladores as $id_instalador) {
            // insertar en la tabla instalador_cita
            $db->ejecutarcita("insert into instalador_cita (cita, instalador) values (:cita, :instalador)", [
                ':cita' => $id_cita,
                ':instalador' => $id_instalador
            ]);

            // obtener detalles de la cita usando un select
            $stmt = $db->ejecutarcita("select
                concat(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m) as nombre_cliente,
                c.fecha,
                c.hora,
                concat(d.calle, ' ', d.numero, ' ', d.numero_int, ', ', d.colonia, ', ', d.ciudad, '. referencias: ', d.referencias) as direccion,
                c.notas,
                ifnull(
                    replace(
                        (select group_concat(concat(prod.nombre, ': ', dp.monto, ' alto: ', dp.alto, ', largo: ', dp.largo) separator '\n')
                        from detalle_cita dc 
                        join detalle_producto dp on dc.detalle_producto = dp.id_detalle_producto 
                        join productos prod on dp.producto = prod.id_producto 
                        where dc.cita = c.id_cita), 
                        ',', '\n'
                    ),
                    'no hay cotizaciones'
                ) as cotizaciones
            from
                citas c
            join
                cliente_direcciones cd on c.cliente_direccion = cd.id_cliente_direcciones
            join
                cliente cli on cd.cliente = cli.id_cliente
            join
                persona p on cli.persona = p.id_persona
            join
                direcciones d on cd.direccion = d.id_direccion
            where
                c.id_cita = :id_cita", [':id_cita' => $id_cita]);

            // Obtener el resultado de la consulta
            $citaDetalles = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($citaDetalles) {
                $notificacion = "Se ha asignado una cita para el cliente {$citaDetalles['nombre_cliente']} el {$citaDetalles['fecha']} a las {$citaDetalles['hora']}. Dirección: {$citaDetalles['direccion']}. Notas: {$citaDetalles['notas']}. Cotizaciones: {$citaDetalles['cotizaciones']}";
            } else {
                $notificacion = "No hay detalles disponibles para la cita.";
            }
            
            // Insertar notificación
            $db->ejecutarcita("INSERT INTO notificaciones_instalador (instalador, notificacion) VALUES (:instalador, :notificacion)", [
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
