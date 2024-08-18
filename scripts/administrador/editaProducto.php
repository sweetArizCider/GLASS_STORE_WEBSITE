<?php
include '../../class/database.php';
$db = new Database();
$db->conectarDB();

extract($_POST);

// Comprobar qué acción se está ejecutando
if ($_POST['action'] == 'add' || $_POST['action'] == 'remove') {
    // Manejar la adición de un diseño
    if ($_POST['action'] == 'add' && !empty($codigo_diseno) && !empty($id_producto)) {
        try {
            error_log("Iniciando procedimiento para agregar diseño...");
            $stmt = $db->getPDO()->prepare("CALL agregar_diseno_a_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            if ($stmt->execute()) {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Diseño Agregado</title>
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
                       
                            <h1>¡Diseño Agregado!</h1>
                            <p>Tu diseño ya está disponible para nuestros clientes. </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
            } else {
                error_log("Error en la ejecución del procedimiento.");
                echo "<div class='alert alert-danger'>Error al agregar diseño.</div>";
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            error_log("Error al agregar diseño: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error al agregar diseño: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } elseif ($_POST['action'] == 'add') {
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
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
               
                    <h1>¡Error al Agregar!</h1>
                    <p>Recuerde seleccionar el diseño. </p>
              
                <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                <br><br>
            </div>
        </body>
        </html>
        
        <?php
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
    }

    // Manejar la eliminación de un diseño
    if ($_POST['action'] == 'remove' && !empty($codigo_diseno) && !empty($id_producto)) {
        try {
            $stmt = $db->getPDO()->prepare("CALL quitar_diseno_de_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            if ($stmt->execute()) {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Diseño Eliminado</title>
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
                       
                            <h1>¡Diseño Eliminado!</h1>
                            <p>Tu diseño ya no está disponible</p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
            } else {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Eliminar!</h1>
                            <p></p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Eliminar!</h1>
                            <p></p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
        }
    } elseif ($_POST['action'] == 'remove') {
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
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
               
                    <h1>¡Error al Agregar!</h1>
                    <p>Recuerde seleccionar el diseño. </p>
              
                <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                <br><br>
            </div>
        </body>
        </html>
        
        <?php
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
    }
} else {
    // Esta parte solo debe ejecutarse si se está editando el producto.
    // Verifica que las variables necesarias existen antes de ejecutar.
    if (isset($nombre_actual, $nuevo_nombre, $categoria, $descripcion, $precio, $estatus)) {
        // Editar el producto
        $cadena = "CALL editarproducto('$nombre_actual', '$nuevo_nombre', '$categoria', '$descripcion', '$precio', '$estatus', NULL);";
        $db->ejecuta($cadena);

        // Manejar la imagen de portada o imágenes adicionales solo si se ha subido un archivo
        if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../../img/disenos/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
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
               
                    <h1>¡Error al Agregar!</h1>
                    <p>Recuerde usar imagenes </p>
              
                <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                <br><br>
            </div>
        </body>
        </html>
        
        <?php
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
                $uploadOk = 0;
            }

            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedFormats)) {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>Recuerde usar imagenes </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>El archivo ya no existe </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
                $uploadOk = 0;
            }

            if ($_FILES["fileToUpload"]["size"] > 500000) {
                ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>El archivo es demasiado grande </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "<div class='alert alert-success'>El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido subido.</div>";

                    $imagen = basename($_FILES["fileToUpload"]["name"]);

                    $sql = "SELECT id_producto FROM productos WHERE nombre = :nombre";
                    $result = $db->ejecutar1($sql, [':nombre' => $nuevo_nombre]);

                    if ($result && $result->rowCount() > 0) {
                        $producto = $result->fetch(PDO::FETCH_ASSOC)['id_producto'];

                        if ($_POST['upload_type'] == 'portada') {
                            $sql_imagen = "CALL actualizar_imagen_producto(:producto_id, :imagen_nombre)";
                            $db->ejecutar1($sql_imagen, [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                        } else if ($_POST['upload_type'] == 'imagenes') {
                            $sql_imagen = "INSERT INTO imagen (imagen, producto) VALUES (:imagen_nombre, :producto_id)";
                            $db->ejecutar1($sql_imagen, [':imagen_nombre' => $imagen, ':producto_id' => $producto]);
                        }

                        $result_check = $db->ejecutar1("SELECT * FROM imagen WHERE producto = :producto_id AND imagen = :imagen_nombre", [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                        if ($result_check && $result_check->rowCount() > 0) {
                            ?>

                            <!DOCTYPE html>
                            <html lang="es">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Imagen Agregada</title>
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
                                   
                                        <h1>¡Imagen Agregada!</h1>
                                        <p>La imagen ha sido agregada correctamente</p>
                                  
                                    <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                                    <br><br>
                                </div>
                            </body>
                            </html>
                            
                            <?php
                            header("refresh:2;../../views/administrador/vista_admin_productos.php");
                        } else {
                            ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>La imagen no se pudo actualizar </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
                        }
                    } else {
                        ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>Por favor, verifique en nombre del producto</p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
                    }
                } else {
                    ?>

                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>Hubo un problema al agregar el archivo </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
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
                    <title>Error</title>
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
                       
                            <h1>¡Error al Agregar!</h1>
                            <p>Recuerde llenar todos los campos necesarios para la edición </p>
                      
                        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Volver a Intentar</a>
                        <br><br>
                    </div>
                </body>
                </html>
                
                <?php
                header("refresh:2;../../views/administrador/vista_admin_productos.php");
    }
}

$db->desconectarDB();

// Comentar la redirección temporalmente
// header("refresh:2;../../views/administrador/vista_admin_productos.php");
?>
