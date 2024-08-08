<?php
// Incluir archivo de conexión a la base de datos
include 'db_connect.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $file_path = '';
    $file_name = '';

    // Verificar si se ha subido un archivo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Definir directorio de destino y nombre del archivo
        $uploadDir = 'uploads/';
        $newFileName = uniqid() . '.' . $fileExtension;
        $dest_path = $uploadDir . $newFileName;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $file_path = $dest_path;
            $file_name = $newFileName;
        } else {
            die('Error al mover el archivo subido.');
        }
    }

    // Preparar la consulta SQL para insertar los datos en la tabla
    $sql = "INSERT INTO DISENOS (codigo, file_path, file_name, descripcion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind de los parámetros
        $stmt->bind_param('ssss', $codigo, $file_path, $file_name, $descripcion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Registro agregado exitosamente.";
        } else {
            echo "Error al agregar el registro: " . $stmt->error;
        }

        // Cerrar el statement
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
