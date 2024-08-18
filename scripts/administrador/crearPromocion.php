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
                    <title>Promocion Agregada</title>
                    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
                    <link rel="stylesheet" href="../../css/styles.css">
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
                            padding-bottom: 20px !important;
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
                            font-size: .9em;
                            margin-bottom: 15px;
                        }
                        .confirmation-container .btn:hover {
                            background-color: #0056b3;
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
                    </style>
                </head>
                <body>
                    <div class="confirmation-container">
                        <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                       
                            <h1>¡Promoción Agregada!</h1>
                            <p>Tu oferta especial ya está disponible para nuestros clientes. </p>
                      
                        <a href="../../views/administrador/vista_admin_promos.php" class="button-cita-ex">Continuar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_promos.php");
                exit();
            } elseif ($resultado == 2) {
                echo "<div class='alert alert-danger'>Error: La promoción con ese nombre ya existe</div>";
                header("refresh:2;../../views/administrador/vista_admin_promos.php");
            }
        } else {
            echo "<div class='alert alert-danger'>Error: No se pudo determinar el resultado de la operación</div>";
            header("refresh:2;../../views/administrador/vista_admin_promos.php");
        }

        $database->desconectarDB();
    }
    ?>
</div>
</body>
</html>
