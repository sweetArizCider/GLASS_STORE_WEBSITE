<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Editar producto</title>
</head>
<body>
    <div class="container">
        <?php
        include '../../class/database.php';
        $db = new Database();
        $db->conectarDB();

        // Extraer datos del formulario
        extract($_POST);

        // Llamar al procedimiento almacenado para actualizar el producto
        $cadena = "CALL editarproducto('$nombre_actual', '$nuevo_nombre', '$categoria', '$descripcion', '$precio', '$estatus', NULL);";
        $db->ejecuta($cadena);

        // Manejo de la carga de la imagen
        if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../../img/productos/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Verificar si es una imagen real
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "<div class='alert alert-danger'>El archivo no es una imagen.</div>";
                $uploadOk = 0;
            }

            // Validar formatos permitidos
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedFormats)) {
                echo "<div class='alert alert-danger'>Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.</div>";
                $uploadOk = 0;
            }

            // Verificar si el archivo ya existe
            if (file_exists($target_file)) {
                echo "<div class='alert alert-danger'>Lo siento, el archivo ya existe.</div>";
                $uploadOk = 0;
            }

            // Validar el tamaño del archivo
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "<div class='alert alert-danger'>Lo siento, tu archivo es demasiado grande.</div>";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "<div class='alert alert-success'>El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido subido.</div>";

                    // Obtener el nombre del archivo subido
                    $imagen = basename($_FILES["fileToUpload"]["name"]);

                    // Actualizar la imagen del producto en la base de datos
                    $cadena = "UPDATE productos SET imagen = '$imagen' WHERE nombre = '$nuevo_nombre';";
                    $db->ejecuta($cadena);
                } else {
                    echo "<div class='alert alert-danger'>Lo siento, hubo un error al subir tu archivo.</div>";
                }
            }
        }

        $db->desconectarDB();

        echo "<div class='alert alert-success'>PRODUCTO EDITADO CORRECTAMENTE</div>";
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
        ?>
    </div>
</body>
</html>
