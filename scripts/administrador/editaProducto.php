<?php
// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../class/database.php';
$db = new Database();
$db->conectarDB();

// Verificar si se ha enviado un formulario con datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Variable para almacenar el mensaje de error o éxito
    $message = '';

    // Verificar la presencia de datos necesarios para la edición
    if (isset($_POST['nombre_actual'], $_POST['nuevo_nombre'], $_POST['categoria'], $_POST['descripcion'], $_POST['precio'], $_POST['estatus'])) {
        try {
            // Editar el producto
            $cadena = "CALL editarproducto(:nombre_actual, :nuevo_nombre, :categoria, :descripcion, :precio, :estatus, NULL);";
            $stmt = $db->getPDO()->prepare($cadena);
            $stmt->bindParam(':nombre_actual', $_POST['nombre_actual']);
            $stmt->bindParam(':nuevo_nombre', $_POST['nuevo_nombre']);
            $stmt->bindParam(':categoria', $_POST['categoria']);
            $stmt->bindParam(':descripcion', $_POST['descripcion']);
            $stmt->bindParam(':precio', $_POST['precio']);
            $stmt->bindParam(':estatus', $_POST['estatus']);
            $stmt->execute();
            
            // Procesar la imagen de portada o adicionales
            if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
                $target_dir = "../../img/disenos/";
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Verificar si el archivo es una imagen
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check === false) {
                    $message = "El archivo no es una imagen.";
                    $uploadOk = 0;
                }

                // Verificar el formato de imagen permitido
                $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($imageFileType, $allowedFormats)) {
                    $message = "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
                    $uploadOk = 0;
                }

                // Verificar si el archivo ya existe
                if (file_exists($target_file)) {
                    $message = "Lo siento, el archivo ya existe.";
                    $uploadOk = 0;
                }

                // Verificar el tamaño del archivo
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    $message = "Lo siento, tu archivo es demasiado grande.";
                    $uploadOk = 0;
                }

                // Subir el archivo si todo está bien
                if ($uploadOk == 1) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $message = "El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido subido.";

                        $imagen = basename($_FILES["fileToUpload"]["name"]);
                        $sql = "SELECT id_producto FROM productos WHERE nombre = :nombre";
                        $result = $db->ejecutar1($sql, [':nombre' => $_POST['nuevo_nombre']]);

                        if ($result && $result->rowCount() > 0) {
                            $producto = $result->fetch(PDO::FETCH_ASSOC)['id_producto'];

                            // Dependiendo del tipo de carga
                            if ($_POST['upload_type'] == 'portada') {
                                $sql_imagen = "CALL actualizar_imagen_producto(:producto_id, :imagen_nombre)";
                                $db->ejecutar1($sql_imagen, [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                            } elseif ($_POST['upload_type'] == 'imagenes') {
                                $sql_imagen = "INSERT INTO imagen (imagen, producto) VALUES (:imagen_nombre, :producto_id)";
                                $db->ejecutar1($sql_imagen, [':imagen_nombre' => $imagen, ':producto_id' => $producto]);
                            }

                            $result_check = $db->ejecutar1("SELECT * FROM imagen WHERE producto = :producto_id AND imagen = :imagen_nombre", [':producto_id' => $producto, ':imagen_nombre' => $imagen]);
                            if ($result_check && $result_check->rowCount() > 0) {
                                $message = "Imagen verificada correctamente en la base de datos.";
                            } else {
                                $message = "Error: La imagen no se actualizó correctamente en la base de datos.";
                            }
                        } else {
                            $message = "No se pudo obtener el ID del producto. Verifica el nombre del producto.";
                        }
                    } else {
                        $message = "Lo siento, hubo un error al subir tu archivo.";
                    }
                }
            }


            // Redirigir a la página anterior con el mensaje de éxito o error
            if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                $message = urlencode($message); // Codificar el mensaje para URL
                header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=" . $message);
            } else {
                // Si no hay REFERER, redirigir a una página de error o predeterminada
                header("Location: " . urlencode($message));
            }
            exit();
        } catch (PDOException $e) {
            error_log("Error al editar producto: " . $e->getMessage());
            $message = "Error al editar producto: " . htmlspecialchars($e->getMessage());

            // Redirigir con el mensaje de error
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=" . urlencode($message));
            exit();
        }
    } else {
        $message = "Faltan datos necesarios para la edición del producto.";

        // Redirigir con el mensaje de error
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=" . urlencode($message));
        exit();
    }
} else {
    $message = "Solicitud no válida.";

    // Redirigir con el mensaje de error
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=" . urlencode($message));
    exit();
}

$db->desconectarDB();
?>
