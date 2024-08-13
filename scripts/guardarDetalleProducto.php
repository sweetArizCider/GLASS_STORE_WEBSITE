<?php
session_start();
include '../class/database.php';

try {
$db = new database();
$db->conectarDB();
$pdo = $db->getPDO();

if (isset($_POST['producto'], $_POST['alto'], $_POST['ancho'], $_POST['cantidad'], $_POST['total'])) {
    $producto = $_POST['producto'];
    $alto = $_POST['alto'];
    $ancho = $_POST['ancho'];
    $cantidad = $_POST['cantidad'];
    $total = $_POST['total'];
    $diseno = isset($_POST['diseno']) ? $_POST['diseno'] : null;
    $color_accesorios = isset($_POST['color_accesorios']) ? $_POST['color_accesorios'] : null;

    if (isset($_SESSION["nom_usuario"])) {
        $user = $_SESSION["nom_usuario"];

        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $params_ids = [$user];
        $resultado_ids = $db->seleccionar($consulta_ids, $params_ids);

        if ($resultado_ids && !empty($resultado_ids)) {
            $id_cliente = $resultado_ids[0]->id_cliente;

            $stmt = $pdo->prepare("
                INSERT INTO detalle_producto (producto, cliente, alto, largo, cantidad, marco, tipo_cadena, diseno)
                VALUES (:producto, :cliente, :alto, :ancho, :cantidad, :marco, :tipo_cadena, :diseno)
            ");
            $stmt->bindParam(':producto', $producto);
            $stmt->bindParam(':cliente', $id_cliente);
            $stmt->bindParam(':alto', $alto);
            $stmt->bindParam(':ancho', $ancho);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':marco', $color_accesorios);
            $stmt->bindParam(':tipo_cadena', $color_accesorios);
            $stmt->bindParam(':diseno', $diseno);

            if ($stmt->execute()) {
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>¡Cotización Guardada!</title>
                    <meta http-equiv="refresh" content="5;url=../views/productos.php">
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
                        .success-container {
                            background-color: rgba(255, 255, 255);
                        padding: 20px;
                        padding-bottom: 20px !important;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        text-align: center;
                        max-width: 500px;
                        width: 100%;
                        }
                        .success-container h1 {
                            font-family: 'Montserrat';
                        color: #132644;
                        font-size: 2.5em;
                        font-weight: 800;
                        margin-bottom: 15px;
                        }
                        .success-container p {
                            font-family: 'Montserrat';
                        font-size: .9em;
                        margin-bottom: 15px;
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
                        .success-container .btn:hover {
                            background-color: #218838;
                        }
                    </style>
                </head>
                <body>
                    <div class="success-container">
                        <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                        <h1>¡Cotización Guardada!</h1>
                        <p style="margin-bottom:2em;">Tu cotización ha sido guardada exitosamente. Puedes revisarla en la sección <strong>Mis Cotizaciones</strong> para concluir tu compra.</p>
                        <a href="../views/productos.php" class="button-cita-ex">Seguir Cotizando</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                
            } else {
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error al Guardar Cotización</title>
                    <meta http-equiv="refresh" content="3;url=../views/productos.php">
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
                        .error-container {
                            background-color: rgba(255, 255, 255);
                            padding: 20px;
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
                            font-size: 1em;
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
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                        <h1>¡Lo Lamentamos!</h1>
                        <p style="margin-bottom:2em;">Ha ocurrido un error al intentar guardar tu cotización. Por favor, inténtalo de nuevo.</p>
                        <a href="../views/productos.php" class="btn" style="border-radius:30px; font-weight:400;">Volver a Intentar</a>
                        <br><br>
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
                <title>Inicia Sesión</title>
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
                    .auth-container .btn:hover {
                        background-color: #0056b3;
                    }
                </style>
            </head>
            <body>
                <div class="auth-container">
                    <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                    <h1>Inicia Sesión</h1>
                    <p style="margin-bottom:2em;">Para guardar tus cotizaciones y disfrutar de todos los beneficios, es necesario que inicies sesión.</p>
                    <a href="../views/iniciarSesion.php" class="button-cita-ex">Iniciar Sesión</a>
                    <br><br>
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
            <title>Inicia Sesión</title>
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
                
                .auth-container .btn:hover {
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
            <div class="auth-container">
                <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                <h1>Inicia Sesión</h1>
                <p style="margin-bottom:2em;">Para guardar tus cotizaciones y disfrutar de todos los beneficios, es necesario que inicies sesión.</p>
                <a href="../views/iniciarSesion.php" class="button-cita-ex">Iniciar Sesión</a>
                <br><br>
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
                    <title>Error al Guardar Cotización</title>
                    <meta http-equiv="refresh" content="3;url=../views/productos.php">
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
                        .error-container {
                            background-color: rgba(255, 255, 255);
                            padding: 20px;
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
                            font-size: 1em;
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
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                        <h1>¡Lo Lamentamos!</h1>
                        <p style="margin-bottom:2em;">Ha ocurrido un error al intentar guardar tu cotización, hemos recibido datos incompletos. Por favor, inténtalo de nuevo.</p>
                        <a href="../views/productos.php" class="btn" style="border-radius:30px; font-weight:400;">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                <?php
}
}
catch (PDOException $e) {
    // Captura y manejo de errores
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error al Guardar Cotización</title>
        <meta http-equiv="refresh" content="3;url=../views/productos.php">
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
            .error-container {
                background-color: rgba(255, 255, 255);
                padding: 20px;
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
                font-size: 1em;
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
        </style>
    </head>
    <body>
        <div class="error-container">
            <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4"style="width: 100px; margin-top:1em;">
            <h1>¡Lo Lamentamos!</h1>
            <p style="margin-bottom:2em;">Ha ocurrido un error al intentar guardar tu cotización. Por favor, inténtalo de nuevo.</p>
            <a href="../views/productos.php" class="btn" style="border-radius:30px; font-weight:400;">Volver a Intentar</a>
            <br><br>
        </div>
    </body>
    </html>
    <?php
}
?>
