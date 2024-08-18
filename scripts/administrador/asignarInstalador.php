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

function mostrarMensaje($titulo, $mensaje, $claseBoton, $textoBoton, $urlRedirect) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$titulo</title>
    <meta http-equiv="refresh" content="5;url=$urlRedirect">
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
        .container-message {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .container-message h1 {
            color: #132644;
            font-size: 2.5em;
            font-weight: 800;
            margin-bottom: 15px;
        }
        .container-message p {
            font-size: .9em;
            margin-bottom: 15px;
        }
        .button-action {
            background: #132644;
            border: 1.5px solid #132644;
            border-radius: 30px;
            font-size: .9em;
            color: #fff;
            cursor: pointer;
            padding: 8px 18px;
            text-decoration: none;
        }
        .button-retry {
            background: #c82333;
            border: 1.5px solid #c82333;
        }
    </style>
</head>
<body>
    <div class="container-message">
        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px;">
        <h1>$titulo</h1>
        <p>$mensaje</p>
        <a href="$urlRedirect" class="button-action $claseBoton">$textoBoton</a>
    </div>
</body>
</html>
HTML;
}

if (isset($_POST['id_cita'], $_POST['instaladores'], $_POST['fecha'], $_POST['hora'])) {
    $id_cita = $_POST['id_cita'];
    $instaladores = $_POST['instaladores'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    try {
        $db->beginTransaction();

        $db->ejecutarcita("CALL aceptarcita(:id_cita)", [':id_cita' => $id_cita]);

        foreach ($instaladores as $id_instalador) {
            $db->ejecutarcita("INSERT INTO instalador_cita (cita, instalador) VALUES (:cita, :instalador)", [
                ':cita' => $id_cita,
                ':instalador' => $id_instalador
            ]);

            $stmt = $db->ejecutarcita("SELECT
                CONCAT(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m) AS nombre_cliente,
                CONCAT(d.calle, ' ', d.numero, ' ', d.numero_int, ', ', d.colonia, ', ', d.ciudad, '. referencias: ', d.referencias) AS direccion,
                c.notas,
                IFNULL(
                    REPLACE(
                        (SELECT GROUP_CONCAT(CONCAT(prod.nombre, ': ', dp.monto, ' alto: ', dp.alto, ', largo: ', dp.largo) SEPARATOR '\n')
                        FROM detalle_cita dc 
                        JOIN detalle_producto dp ON dc.detalle_producto = dp.id_detalle_producto 
                        JOIN productos prod ON dp.producto = prod.id_producto 
                        WHERE dc.cita = c.id_cita), 
                        ',', '\n'
                    ),
                    'no hay cotizaciones'
                ) AS cotizaciones
            FROM
                citas c
            JOIN cliente_direcciones cd ON c.cliente_direccion = cd.id_cliente_direcciones
            JOIN cliente cli ON cd.cliente = cli.id_cliente
            JOIN persona p ON cli.persona = p.id_persona
            JOIN direcciones d ON cd.direccion = d.id_direccion
            WHERE c.id_cita = :id_cita", [':id_cita' => $id_cita]);

            $citaDetalles = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($citaDetalles) {
                $notificacion = "Se ha asignado una cita para el cliente {$citaDetalles['nombre_cliente']} el {$fecha} a las {$hora}. Dirección: {$citaDetalles['direccion']}. Notas: {$citaDetalles['notas']}. Cotizaciones: {$citaDetalles['cotizaciones']}";
            } else {
                $notificacion = "No hay detalles disponibles para la cita.";
            }

            $db->ejecutarcita("INSERT INTO notificaciones_instalador (instalador, notificacion) VALUES (:instalador, :notificacion)", [
                ':instalador' => $id_instalador,
                ':notificacion' => $notificacion
            ]);
        }

        $db->commit();
        mostrarMensaje('¡Asignación Exitosa!', 'Los instaladores han sido asignados con éxito a la cita. Se ha notificado a los instaladores sobre los detalles de la cita. Por favor, mantente pendiente de tus notificaciones para cualquier actualización.', '', 'Volver a Citas', '../../views/administrador/vista_admin_citas.php');
    } catch (Exception $e) {
        $db->rollBack();
        mostrarMensaje('¡Lo Lamentamos!', 'Ha ocurrido un error al intentar asignar los instaladores a la cita. Por favor, inténtalo de nuevo.', 'button-retry', 'Volver a Intentar', '../../views/administrador/vista_admin_citas.php');
    }
} else {
    mostrarMensaje('¡Lo Lamentamos!', 'Ha ocurrido un error al intentar asignar los instaladores a la cita. Por favor, inténtalo de nuevo.', 'button-retry', 'Volver a Intentar', '../../views/administrador/vista_admin_citas.php');
}
?>
</body>
</html>
