<?php
// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
                error_log("Diseño agregado correctamente.");
                echo "<div class='alert alert-success'>Diseño agregado correctamente.</div>";
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
        echo "<div class='alert alert-danger'>El código de diseño no puede estar vacío.</div>";
    }

    // Manejar la eliminación de un diseño
    if ($_POST['action'] == 'remove' && !empty($codigo_diseno) && !empty($id_producto)) {
        try {
            $stmt = $db->getPDO()->prepare("CALL quitar_diseno_de_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Diseño quitado correctamente.</div>";
            } else {
                error_log("Error en la ejecución del procedimiento.");
                echo "<div class='alert alert-danger'>Error al quitar diseño.</div>";
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            error_log("Error al quitar diseño: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error al quitar diseño: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } elseif ($_POST['action'] == 'remove') {
        echo "<div class='alert alert-danger'>El código de diseño no puede estar vacío.</div>";
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
                echo "<div class='alert alert-danger'>El archivo no es una imagen.</div>";
                $uploadOk = 0;
            }

            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedFormats)) {
                echo "<div class='alert alert-danger'>Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.</div>";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                echo "<div class='alert alert-danger'>Lo siento, el archivo ya existe.</div>";
                $uploadOk = 0;
            }

            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "<div class='alert alert-danger'>Lo siento, tu archivo es demasiado grande.</div>";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "<div class='alert alert-success'>El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido subido.</div>";

                    $imagen = basename($_FILES["fileToUpload"]["name"]);

                    $sql = "SELECT id_producto FROM productos WHERE nombre = :nombre";
                    $result = $db->ejecutar($sql, [':nombre' => $nuevo_nombre]);

                    if ($result && $result->rowCount() > 0) {
                        $producto = $result->fetch(PDO::FETCH_ASSOC)['id_producto'];

                        if ($_POST['upload_type'] == 'portada') {
                            $sql_imagen = "CALL actualizar_imagen_producto(:producto_id, :imagen_nombre)";
                            $db->ejecutar($sql_imagen, [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                        } else if ($_POST['upload_type'] == 'imagenes') {
                            $sql_imagen = "INSERT INTO imagen (imagen, producto) VALUES (:imagen_nombre, :producto_id)";
                            $db->ejecutar($sql_imagen, [':imagen_nombre' => $imagen, ':producto_id' => $producto]);
                        }

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
    } else {
        echo "<div class='alert alert-danger'>Faltan datos necesarios para la edición del producto.</div>";
    }
}

$db->desconectarDB();

// Comentar la redirección temporalmente
// header("refresh:2;../../views/administrador/vista_admin_productos.php");
?>
