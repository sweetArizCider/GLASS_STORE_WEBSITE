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


        ?>
        <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación Exitosa</title>
    <meta http-equiv="refresh" content="5;url=../../views/administrador/vista_admin_citas.php">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url('../../img/index/background.jpeg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .confirmation-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .confirmation-container h1 {
            color: #132644;
            font-size: 2.5em;
            font-weight: 800;
            margin-bottom: 15px;
        }
        .confirmation-container p {
            font-size: .9em;
            margin-bottom: 15px;
        }
        .button-cita-ex {
            background: #132644;
            border: 1.5px solid #132644;
            border-radius: 30px;
            font-size: .9em;
            color: #fff;
            cursor: pointer;
            padding: 8px 18px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px;">
        <h1>¡Asignación Exitosa!</h1>
        <p>Los instaladores han sido asignados con éxito a la cita.</p>
        <p>Se ha notificado a los instaladores sobre los detalles de la cita. Por favor, mantente pendiente de tus notificaciones para cualquier actualización.</p>
        <a href="../../views/administrador/vista_admin_citas.php" class="button-cita-ex">Volver a Citas</a>
    </div>
</body>
</html>
         <?php

       
    } catch (Exception $e) {
        ?>
        <!DOCTYPE html>
     <html lang="es">
     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Error en la Asignación</title>
         <meta http-equiv="refresh" content="5;url=../../views/administrador/vista_admin_citas.php">
         <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
         <style>
             body {
                 background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url('../../img/index/background.jpeg') center/cover no-repeat;
                 display: flex;
                 justify-content: center;
                 align-items: center;
                 height: 100vh;
                 font-family: Arial, sans-serif;
             }
             .error-container {
                 background-color: rgba(255, 255, 255, 0.9);
                 padding: 20px;
                 border-radius: 10px;
                 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                 text-align: center;
                 max-width: 500px;
                 width: 100%;
             }
             .error-container h1 {
                 color: #c82333;
                 font-size: 2.5em;
                 font-weight: 800;
                 margin-bottom: 15px;
             }
             .error-container p {
                 font-size: .9em;
                 margin-bottom: 15px;
             }
             .button-retry {
                 background: #c82333;
                 border: 1.5px solid #c82333;
                 border-radius: 30px;
                 font-size: .9em;
                 color: #fff;
                 cursor: pointer;
                 padding: 8px 18px;
                 text-decoration: none;
             }
         </style>
     </head>
     <body>
         <div class="error-container">
             <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px;">
             <h1>¡Lo Lamentamos!</h1>
             <p>Ha ocurrido un error al intentar asignar los instaladores a la cita. Por favor, inténtalo de nuevo.</p>
             <a href="../../views/administrador/vista_admin_citas.php" class="button-retry">Volver a Intentar</a>
         </div>
     </body>
     </html>
          <?php
    }
} else {

    ?>
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error en la Asignación</title>
    <meta http-equiv="refresh" content="5;url=../../views/administrador/vista_admin_citas.php">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url('../../img/index/background.jpeg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .error-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-container h1 {
            color: #c82333;
            font-size: 2.5em;
            font-weight: 800;
            margin-bottom: 15px;
        }
        .error-container p {
            font-size: .9em;
            margin-bottom: 15px;
        }
        .button-retry {
            background: #c82333;
            border: 1.5px solid #c82333;
            border-radius: 30px;
            font-size: .9em;
            color: #fff;
            cursor: pointer;
            padding: 8px 18px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px;">
        <h1>¡Lo Lamentamos!</h1>
        <p>Ha ocurrido un error al intentar asignar los instaladores a la cita. Por favor, inténtalo de nuevo.</p>
        <a href="../../views/administrador/vista_admin_citas.php" class="button-retry">Volver a Intentar</a>
    </div>
</body>
</html>
     <?php
}
?>
</body>
</html>
