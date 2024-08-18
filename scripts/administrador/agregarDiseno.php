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
            ?>

            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Diseno Agregado</title>
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
                        <p>Tu nuevo diseño ya está disponible para nuestros clientes. </p>
                  
                    <a href="../../views/administrador/vista_admin_disenos.php" class="button-cita-ex">Continuar</a>
                    <br><br>
                </div>
            </body>
            </html>
            
            <?php
            header("refresh:2;../../views/administrador/vista_admin_disenos.php");
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
