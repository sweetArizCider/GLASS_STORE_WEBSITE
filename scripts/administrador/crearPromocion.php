<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Promoción</title>
</head>
<body>
<div class="container">
    <?php
    include '../../class/database.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_promocion = $_POST['nombre_promocion'];
        $tipo_promocion = $_POST['tipo_promocion'];
        $valor = $_POST['valor'];

        if ($tipo_promocion == 'porcentual') {
            $valor = $valor / 100;
        }

        $database = new Database();
        $database->conectarDB();

        // Definir el parámetro de salida
        $resultado = 0;

        // Llamar al procedimiento almacenado
        $query = "CALL crearpromocion(:nombre_promocion, :tipo_promocion, :valor, @resultado)";
        $params = array(
            ':nombre_promocion' => $nombre_promocion,
            ':tipo_promocion' => $tipo_promocion,
            ':valor' => $valor
        );

        $database->ejecutarcita($query, $params);
        
        $query = "SELECT @resultado AS resultado";
        $stmt = $database->ejecutarcita($query, array()); 

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $resultado = $result['resultado'];

            if ($resultado == 1) {
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Promoción Exitosa</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                    <meta http-equiv="refresh" content="3;url=../../views/administrador/vista_admin_promos.php">

                    <style>
                        body {
                            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../../img/index/background.jpeg) center/cover no-repeat;
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
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            text-align: center;
                            max-width: 500px;
                            width: 100%;
                            margin: auto;
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
                            font-size: 1em;
                            margin-bottom: 15px;
                            color: black;
                        }
                        .confirmation-container .button-promocion {
                            background: #132644;
  border: 1.5px solid #132644;
  border-radius: 30px;
  font-family: 'Montserrat';
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
                        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                        <h1>¡Promoción Exitosa!</h1>
                        <p>Tu esfuerzo ha dado frutos, ! Ahora es el momento de ver cómo esta promoción trae alegría a tus clientes.</p>
                       
                        <a href="../../views/administrador/vista_admin_promos.php" class="button-promocion">Ver Promociones</a>
                        <br><br>
                    </div>
                </body>
                </html>
                <?php
                exit();
            } elseif ($resultado == 2) {
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error al Crear la Promoción</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                    <meta http-equiv="refresh" content="5;url=../../views/administrador/vista_admin_promos.php">
                    <style>
                        body {
                            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../../img/index/background.jpeg) center/cover no-repeat;
                            background-size: cover;
                            background-position: center;
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
                            font-family: 'Montserrat';
                            color: #F44336;
                            font-size: 2.5em;
                            font-weight: 800;
                            margin-bottom: 15px;
                        }
                        .error-container p {
                            font-family: 'Montserrat';
                            font-size: 1em;
                            margin-bottom: 15px;
                            color: #333;
                        }
                        .error-container .button-retry {
                            background: #F44336;
                            border: 1.5px solid #F44336;
                            border-radius: 30px;
                            font-family: 'Montserrat';
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
                        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                        <h1>¡Lo Sentimos!</h1>
                        <p>Algo salió mal al intentar crear la promoción. Pero no te desanimes,
                             ¡Cada error es una oportunidad para aprender y mejorar!</p>
                        
                        <a href="../../views/administrador/vista_admin_promos.php" class="button-retry">Volver a Intentar</a>
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
                <title>Error al Crear la Promoción</title>
                <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                <meta http-equiv="refresh" content="5;url=../../views/administrador/vista_admin_promos.php">
                <style>
                    body {
                        background: linear-gradient(180deg, rgba(244, 67, 54, 0.45) 100%, rgba(244, 67, 54, 0.45) 100%), url(../../img/index/error_bg.jpg) center/cover no-repeat;
                        background-size: cover;
                        background-position: center;
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
                        font-family: 'Montserrat';
                        color: #F44336;
                        font-size: 2.5em;
                        font-weight: 800;
                        margin-bottom: 15px;
                    }
                    .error-container p {
                        font-family: 'Montserrat';
                        font-size: 1em;
                        margin-bottom: 15px;
                        color: #333;
                    }
                    .error-container .button-retry {
                        background: #F44336;
                        border: 1.5px solid #F44336;
                        border-radius: 30px;
                        font-family: 'Montserrat';
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
                    <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                    <h1>¡Lo Sentimos!</h1>
                    <p>No se pudo determinar el resultado de la operación. Por favor, inténtalo de nuevo.</p>
                    <p style="margin-bottom:2em;">Intenta nuevamente y sigue adelante.</p>
                    <a href="../../views/administrador/vista_admin_promos.php" class="button-retry">Volver a Intentar</a>
                    <br><br>
                </div>
            </body>
            </html>
            <?php
        }

        $database->desconectarDB();
    }
    ?>
</div>
</body>
</html>
