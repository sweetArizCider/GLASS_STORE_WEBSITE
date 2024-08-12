<?php
// Incluir archivo de conexión a la base de datos
include '../../class/database.php';

$db = new database();
$db->conectarDB();
$conn = $db->getPDO(); // Obtener la conexión PDO desde la clase `database`

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $file_name = '';
    $file_path = ''; // Inicializar file_path

    // Verificar si se ha subido un archivo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Generar un nombre único para el archivo usando un hash
        $newFileName = uniqid() . '.' . $fileExtension; // Genera un nombre único usando uniqid()
        $uploadDir = __DIR__ . '/../../img/disenos/'; // Directorio de destino
        $dest_path = $uploadDir . $newFileName;

        // Asegúrate de que el directorio de destino existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crea el directorio si no existe
        }

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $file_name = $newFileName; // Almacenar solo el nombre del archivo en la base de datos
            $file_path = $file_name;   // Asignar el mismo valor a file_path
        } else {
            die('Error al mover el archivo subido.');
        }
    }

    // Preparar la consulta SQL para insertar los datos en la tabla
    $sql = "INSERT INTO disenos (codigo, file_path, file_name, descripcion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Ejecutar la consulta con los parámetros
        $stmt->execute([$codigo, $file_path, $file_name, $descripcion]);

        // Confirmar la ejecución
        if ($stmt->rowCount() > 0) {
            echo "Registro agregado exitosamente.";
        } else {
            echo "Error al agregar el registro.";
        }

        // Cerrar el statement
        $stmt->closeCursor();
    } else {
        echo "Error al preparar la consulta: " . $conn->errorInfo()[2];
    }

    $db->desconectarDB(); // Desconectar la base de datos
}
