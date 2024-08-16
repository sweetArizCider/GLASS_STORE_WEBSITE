<?php
session_start();
include '../class/database.php';

echo $id_cliente;
echo $tipo;
echo $fecha;
echo $hora;
echo $direccion;
echo $notas;

// Verificación de inicio de sesión--------------------------------------------
if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];
    $conexion = new database();
    $conexion->conectarDB();

    $consulta_rol = "CALL roles_usuario(?)";
    $resultado_rol = $conexion->seleccionar($consulta_rol, [$user]);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;
        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $resultado_ids = $conexion->seleccionar($consulta_ids, [$user]);

        if ($resultado_ids && !empty($resultado_ids)) {
            $fila = $resultado_ids[0];
            if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
                $id_cliente = $fila->id_cliente;
                $_SESSION['id_cliente'] = $id_cliente;
            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_usuario = $fila->id_instalador;
            } 
        }
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inicia Sesión</title>
        <meta http-equiv="refresh" content="5;url=../views/iniciarSesion.php">
        <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/styles.css">
        <style>
            body {
                background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: Arial, sans-serif;
            }
            .auth-container {
                background-color: rgba(255, 255, 255);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 500px;
                width: 100%;
            }
            .auth-container h1 {
                font-family: 'Montserrat';
                color: #132644;
                font-size: 2.5em;
                font-weight: 800;
                margin-bottom: 15px;
            }
            .auth-container p {
                font-family: 'Montserrat';
                font-size: .9em;
                margin-bottom: 15px;
            }
            .button-cita-ex {
                background: #132644;
                border: 1.5px solid #132644;
                border-radius: 30px;
                font-family: Inter;
                font-size: .9em;
                font-weight: 400;
                color: #fff;
                cursor: pointer;
                padding: 8px 18px;
                text-decoration: none;
            }
            .auth-container .btn:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
            <h1>Inicia Sesión</h1>
            <p style="margin-bottom:2em;">Para agendar una cita y disfrutar de todos nuestros servicios, es necesario que inicies sesión.</p>
            <a href="../views/iniciarSesion.php" class="button-cita-ex">Iniciar Sesión</a>
            <br><br>
        </div>
    </body>
    </html>
    <?php
    exit;

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id_cliente'])) {
        echo "Error: No estás autenticado.";
        exit;
    }



// Procesar el post--------------------------------------------



$tipo = $_POST['tipo'];
$fecha = $_POST['selected_date'];
$hora = $_POST['hora'];
$direccion = $_POST['direccion'];
$notas = $_POST['motivo'];

// Verificar que las variables requeridas no estén vacías
if (empty($tipo) || empty($fecha) || empty($hora) || empty($direccion) || empty($notas)) {
    echo "Todos los campos son obligatorios.";
    exit();
}

try {
    // Obtener id_cliente de la sesión usando las tablas usuarios y persona
    $consulta_cliente = "SELECT c.id_cliente 
                         FROM cliente c
                         INNER JOIN persona p ON c.persona = p.id_persona
                         INNER JOIN usuarios u ON p.usuario = u.id_usuario
                         WHERE u.nom_usuario = :usuario";
    $stmt_cliente = $conexion->getPDO()->prepare($consulta_cliente);
    $stmt_cliente->bindParam(':usuario', $_SESSION['nom_usuario']);
    $stmt_cliente->execute();
    $id_cliente = $stmt_cliente->fetchColumn();

    if ($id_cliente === false) {
        echo "No se encontró el cliente para el usuario.";
        exit();
    }

    // Obtener el id_cliente_direcciones correspondiente
    $consulta_direccion = "SELECT id_cliente_direcciones FROM cliente_direcciones WHERE cliente = :cliente AND direccion = :direccion";
    $stmt_direccion = $conexion->getPDO()->prepare($consulta_direccion);
    $stmt_direccion->bindParam(':cliente', $id_cliente);
    $stmt_direccion->bindParam(':direccion', $direccion);
    $stmt_direccion->execute();
    $id_cliente_direcciones = $stmt_direccion->fetchColumn();

    if ($id_cliente_direcciones === false) {
        echo "No se encontró una dirección válida para el cliente.";
        exit();
    }

    // Preparar la consulta INSERT
    $consulta = "INSERT INTO CITAS (tipo, fecha, hora, cliente_direccion, notas, estatus)
    VALUES (:tipo, :fecha, :hora, :direccion, :notas, 'en espera')";
    
    $stmt = $conexion->getPDO()->prepare($consulta);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':direccion', $id_cliente_direcciones); // Usar el id_cliente_direcciones
    $stmt->bindParam(':notas', $notas);
    $stmt->execute();









// Mensaje de procesamiento--------------------------------------------
        if ($id_cita) {
            $username = $_SESSION["nom_usuario"];
            ?>
           <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Cita Confirmada</title>
                <meta http-equiv="refresh" content="3;url=../index.php">
                <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
                <link rel="stylesheet" href="../css/styles.css">
                <style>
                    body {
                        background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
                        background-size: cover;
                        background-position: center;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        font-family: Arial, sans-serif;
                    }
                    .confirmation-container {
                        background-color: rgba(255, 255, 255);
                        padding: 20px;
                        padding-bottom: 20px !important;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        text-align: center;
                        max-width: 500px;
                        width: 100%;
                    }
                    .confirmation-container h1 {
                        font-family: 'Montserrat';
                        color: #132644;
                        font-size: 2.5em;
                        font-weight: 800;
                        margin-bottom: 15px;
                    }
                    .confirmation-container p {
                        font-family: 'Montserrat';
                        font-size: .9em;
                        margin-bottom: 15px;
                    }
                    .confirmation-container .btn:hover {
                        background-color: #0056b3;
                    }
                    .button-cita-ex{

  background: #132644;
  border: 1.5px solid #132644;
  border-radius: 30px;
  font-family: Inter;
  font-size: .9em;
  font-weight: 400;
  color: #fff;
  cursor: pointer;
  padding: 8px 18px;
  text-decoration: none;
                    }

                </style>
            </head>
            <body>
                <div class="confirmation-container">
                    <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                    <h1>¡Cita Exitosa!</h1>
                    <p>Gracias <strong><?php echo htmlspecialchars($username); ?></strong> por agendar tu cita con nosotros.</p>
                    <p style="margin-bottom:2em;">Por favor, mantente pendiente de tus notificaciones para cualquier actualización.</p>
                    <a href="../views/citas.php" class="button-cita-ex">Volver al Inicio</a>
                    <br><br>
                </div>
            </body>
            </html>
            <?php
            
// Obtener los productos del carrito--------------------------------------------
            $query_productos_carrito = "SELECT id_detalle_producto FROM detalle_producto WHERE estatus = 'en carrito' AND cliente = ?";
            $stmt_productos_carrito = $conexion->getPDO()->prepare($query_productos_carrito);
            $stmt_productos_carrito->execute([$id_cliente]);

            $productos_carrito = $stmt_productos_carrito->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($productos_carrito)) {
                foreach ($productos_carrito as $detalle_producto_id) {
                    $query_detalle_cita = "INSERT INTO detalle_cita (cita, detalle_producto) VALUES (?, ?)";
                    $stmt_detalle_cita = $conexion->getPDO()->prepare($query_detalle_cita);
                    $stmt_detalle_cita->execute([$id_cita, $detalle_producto_id]);

                    if ($stmt_detalle_cita->rowCount() > 0) {
                       
                        $estatus_valido = 'aceptada'; 
                        $query_actualizar_estatus = "UPDATE detalle_producto SET estatus = ? WHERE id_detalle_producto = ?";
                        $stmt_actualizar_estatus = $conexion->getPDO()->prepare($query_actualizar_estatus);
                        $stmt_actualizar_estatus->execute([$estatus_valido, $detalle_producto_id]);
                    } 
                }
            } 
        } 
        else{
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Error al Crear la Cita</title>
                <meta http-equiv="refresh" content="5;url=../index.php">
                <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
                <link rel="stylesheet" href="../css/styles.css">
                <style>
                    body {
                        background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
                        background-size: cover;
                        background-position: center;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        font-family: Arial, sans-serif;
                    }
                    .error-container {
                        background-color: rgba(255, 255, 255); /* Fondo semitransparente */
                        padding: 20px;
                        padding-bottom: 20px !important;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        text-align: center;
                        max-width: 500px;
                        width: 100%;
                    }
                    .error-container h1 {
                        font-family: 'Montserrat';
                        color: #c82333;
                        font-size: 2.5em;
                        font-weight: 800;
                        margin-bottom: 15px;
                    }
                    .error-container p {
                        font-family: 'Montserrat';
                        font-size: .9em;
                        margin-bottom: 15px;
                    }
                    .error-container .btn {
                        background-color: #c82333;
                        color: #ffffff;
                        padding: 10px 20px;
                        border-radius: 5px;
                        text-decoration: none;
                        font-weight: bold;
                        transition: background-color 0.3s;
                    }
                    .error-container .btn:hover {
                        background-color: #a71d2a;
                    }
                    .button-retry{
                        background: #c82333;
                        border: 1.5px solid #c82333;
                        border-radius: 30px;
                        font-family: Inter;
                        font-size: .9em;
                        font-weight: 400;
                        color: #fff;
                        cursor: pointer;
                        padding: 8px 18px;
                        text-decoration: none;
                    }
    
                </style>
            </head>
            <body>
                <div class="error-container">

                    <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                    <h1>¡Lo Lamentamos!</h1>
                    <p>Ha ocurrido un error al intentar crear tu cita. Por favor, inténtalo de nuevo.</p>
                    <a href="../views/citas.php" class="button-retry" style="border-radius: 30px;">Volver a Intentar</a>
                    <br><br>

                </div>
            </body>
            </html>
            <?php
        }
    } catch (Exception $e) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error al Crear la Cita</title>
            <meta http-equiv="refresh" content="5;url=../index.php">
            <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="../css/styles.css">
            <style>
                body {
                    background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
                    background-size: cover;
                    background-position: center;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    font-family: Arial, sans-serif;
                }
                .error-container {
                    background-color: rgba(255, 255, 255); /* Fondo semitransparente */
                    padding: 20px;
                    padding-bottom: 20px !important;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 500px;
                    width: 100%;
                }
                .error-container h1 {
                    font-family: 'Montserrat';
                    color: #c82333;
                    font-size: 2.5em;
                    font-weight: 800;
                    margin-bottom: 15px;
                }
                .error-container p {
                    font-family: 'Montserrat';
                    font-size: .9em;
                    margin-bottom: 15px;
                }
                .error-container .btn {
                    background-color: #c82333;
                    color: #ffffff;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                    transition: background-color 0.3s;
                }
                .error-container .btn:hover {
                    background-color: #a71d2a;
                }
                .button-retry{
                    background: #c82333;
                    border: 1.5px solid #c82333;
                    border-radius: 30px;
                    font-family: Inter;
                    font-size: .9em;
                    font-weight: 400;
                    color: #fff;
                    cursor: pointer;
                    padding: 8px 18px;
                    text-decoration: none;
                }

            </style>
        </head>
        <body>
            <div class="error-container">
                <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                <h1>¡Lo Lamentamos!</h1>
                <p>Ha ocurrido un error al intentar crear tu cita. Por favor, inténtalo de nuevo.</p>
                
                <a href="../views/citas.php" class="button-retry">Volver a Intentar</a>
                <br><br>
            </div>
        </body>
        </html>
        <?php
    }
}
