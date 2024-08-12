<?php
// Conectar a la base de datos
include '../../class/database.php';
$db = new Database();
$db->conectarDB();

// Extraer datos del formulario
extract($_POST);

// Llamar al procedimiento almacenado para actualizar el producto
$cadena = "CALL editarproducto('$nombre_actual', '$nuevo_nombre', '$categoria', '$descripcion', '$precio', '$estatus', NULL);";
$db->ejecuta($cadena);

// Manejo de la carga de la portada del producto
if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
    // Cambiar la ruta de destino a la carpeta img/disenos/
    $target_dir = "../../img/disenos/";
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

            // Depuración: Mostrar la consulta que estamos ejecutando
            $sql = "SELECT id_producto FROM productos WHERE nombre = :nombre";
            echo "<div class='alert alert-info'>Consulta SQL: $sql</div>";

            // Obtener el ID del producto actualizado
            $result = $db->ejecutar($sql, [':nombre' => $nuevo_nombre]);

            // Verificar si la consulta fue exitosa
            if ($result && $result->rowCount() > 0) {
                $producto = $result->fetch(PDO::FETCH_ASSOC)['id_producto'];
                echo "<div class='alert alert-info'>ID del producto: $producto</div>";

                // Verificar si se está subiendo una portada o imágenes adicionales
                if ($_POST['upload_type'] == 'portada') {
                    // Llamar al procedimiento almacenado para actualizar o insertar la imagen
                    $sql_imagen = "CALL actualizar_imagen_producto(:producto_id, :imagen_nombre)";
                    $db->ejecutar($sql_imagen, [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                } else if ($_POST['upload_type'] == 'imagenes') {
                    // Insertar nuevas imágenes adicionales
                    $sql_imagen = "INSERT INTO imagen (imagen, producto) VALUES (:imagen_nombre, :producto_id)";
                    $db->ejecutar($sql_imagen, [':imagen_nombre' => $imagen, ':producto_id' => $producto]);
                }

                // Verificar que el cambio se refleje en la base de datos
                $result_check = $db->ejecutar("SELECT * FROM imagen WHERE producto = :producto_id AND imagen = :imagen_nombre", [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                if ($result_check && $result_check->rowCount() > 0) {
                    echo "<div class='alert alert-success'>Imagen verificada correctamente en la base de datos.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: La imagen no se actualizó correctamente en la base de datos.</div>";
                }

            } else {
                echo "<div class='alert alert-danger'>No se pudo obtener el ID del producto. Verifica el nombre del producto.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Lo siento, hubo un error al subir tu archivo.</div>";
        }
    }
}

$db->desconectarDB();

echo "<div class='alert alert-success'>PRODUCTO EDITADO CORRECTAMENTE</div>";
header("refresh:2;../../views/administrador/vista_admin_productos.php");

