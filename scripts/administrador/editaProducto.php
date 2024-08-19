<?php
// Mostrar errores en pantalla, pero no mostrar advertencias o avisos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

include '../../class/database.php';
$db = new Database();
$db->conectarDB();

extract($_POST);

// Función para mostrar mensajes con redirección
function mostrarMensaje($titulo, $mensaje, $redireccion = "../../views/administrador/vista_admin_productos.php") {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $titulo; ?></title>
        <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../css/styless.css">
    </head>
    <body>
        <div class="confirmation-container">
            <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
            <h1><?php echo $titulo; ?></h1>
            <p><?php echo $mensaje; ?></p>
            <a href="<?php echo $redireccion; ?>" class="button-cita-ex">Continuar</a>
        </div>
    </body>
    </html>
    <?php
    header("refresh:2; $redireccion");
    exit();
}

// Comprobar qué acción se está ejecutando
if (isset($_POST['action']) && ($_POST['action'] == 'add' || $_POST['action'] == 'remove')) {
    // Manejar la adición de un diseño
    if ($_POST['action'] == 'add' && !empty($codigo_diseno) && !empty($id_producto)) {
        try {
            error_log("Iniciando procedimiento para agregar diseño...");
            $stmt = $db->getPDO()->prepare("CALL agregar_diseno_a_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            if ($stmt->execute()) {
                mostrarMensaje("Diseño Agregado", "Tu diseño ya está disponible para nuestros clientes.");
            } else {
                error_log("Error en la ejecución del procedimiento.");
                mostrarMensaje("Error al Agregar", "Error al ejecutar el procedimiento para agregar el diseño.");
            }
        } catch (PDOException $e) {
            error_log("Error al agregar diseño: " . $e->getMessage());
            mostrarMensaje("Error al Agregar", "Error al agregar diseño: " . htmlspecialchars($e->getMessage()));
        }
    } elseif ($_POST['action'] == 'add') {
        mostrarMensaje("Error al Agregar", "Recuerde seleccionar el diseño.");
    }

    // Manejar la eliminación de un diseño
    if ($_POST['action'] == 'remove' && !empty($codigo_diseno) && !empty($id_producto)) {
        try {
            $stmt = $db->getPDO()->prepare("CALL quitar_diseno_de_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            if ($stmt->execute()) {
                mostrarMensaje("Diseño Eliminado", "Tu diseño ya no está disponible.");
            } else {
                mostrarMensaje("Error al Eliminar", "Error al ejecutar el procedimiento para eliminar el diseño.");
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar diseño: " . $e->getMessage());
            mostrarMensaje("Error al Eliminar", "Error al eliminar diseño: " . htmlspecialchars($e->getMessage()));
        }
    } elseif ($_POST['action'] == 'remove') {
        mostrarMensaje("Error al Eliminar", "Recuerde seleccionar el diseño.");
    }
} else {
    // Manejar cambio de estatus e imágenes
    if (isset($estatus, $id_producto)) {
        try {
            // Editar el estatus del producto
            error_log("Iniciando procedimiento para editar estatus del producto...");
            $stmt = $db->getPDO()->prepare("CALL editarproductoestatus(:id_producto, :estatus)");
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->bindParam(':estatus', $estatus, PDO::PARAM_STR);
            if ($stmt->execute()) {
                error_log("Estatus del producto actualizado correctamente.");

                // Manejar la imagen de portada o imágenes adicionales solo si se ha subido un archivo
                if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
                    $target_dir = "../../img/disenos/";
                    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    // Comprobar si el archivo es una imagen real
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if ($check !== false) {
                        $uploadOk = 1;
                    } else {
                        $uploadOk = 0;
                        mostrarMensaje("Error al Subir Imagen", "El archivo no es una imagen.");
                    }

                    // Verificar formatos permitidos
                    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($imageFileType, $allowedFormats)) {
                        $uploadOk = 0;
                        mostrarMensaje("Error al Subir Imagen", "Solo se permiten archivos JPG, JPEG, PNG y GIF.");
                    }

                    // Verificar tamaño de archivo
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        $uploadOk = 0;
                        mostrarMensaje("Error al Subir Imagen", "El archivo es demasiado grande.");
                    }

                    // Subir archivo si todo está bien
                    if ($uploadOk == 1) {
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            $imagen = basename($_FILES["fileToUpload"]["name"]);

                            // Insertar o actualizar la imagen del producto
                            if ($_POST['upload_type'] == 'imagenes') {
                                $stmt_imagen = $db->getPDO()->prepare("INSERT INTO imagen (imagen, producto) VALUES (:imagen_nombre, :producto_id)");
                                $stmt_imagen->bindParam(':imagen_nombre', $imagen, PDO::PARAM_STR);
                                $stmt_imagen->bindParam(':producto_id', $id_producto, PDO::PARAM_INT);
                                if ($stmt_imagen->execute()) {
                                    mostrarMensaje("Imagen Agregada", "La imagen ha sido agregada correctamente.");
                                } else {
                                    mostrarMensaje("Error al Agregar Imagen", "No se pudo agregar la imagen.");
                                }
                            }
                        } else {
                            mostrarMensaje("Error al Subir Imagen", "Hubo un error al subir la imagen.");
                        }
                    }
                } else {
                    mostrarMensaje("Producto Editado", "El estatus del producto ha sido actualizado correctamente.");
                }
            } else {
                mostrarMensaje("Error al Editar", "Error al actualizar el estatus del producto.");
            }
        } catch (PDOException $e) {
            error_log("Error al editar producto: " . $e->getMessage());
            mostrarMensaje("Error al Editar", "Error al editar el producto: " . htmlspecialchars($e->getMessage()));
        }
    } else {
        mostrarMensaje("Error al Editar", "Recuerde llenar todos los campos necesarios.");
    }
}

$db->desconectarDB();
?>
