<?php
include '../../class/database.php';
$db = new Database();
$db->conectarDB();

extract($_POST);

// Verificar si la categoría ya existe
$query_check = "SELECT * FROM categorias WHERE nombre = :nombre";
$stmt_check = $db->getPDO()->prepare($query_check);
$stmt_check->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$stmt_check->execute();

if ($stmt_check->rowCount() > 0) {
    // La categoría ya existe
    $_SESSION['error_message'] = "Error: La categoría '$nombre' ya existe.";
} else {
    // La categoría no existe, proceder a agregarla
    $cadena = "CALL añadircategoria(:nombre)";
    $stmt_insert = $db->getPDO()->prepare($cadena);
    $stmt_insert->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt_insert->execute();
    $_SESSION['message'] = "¡Categoría agregada exitosamente!";
}

$db->desconectarDB();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categoria Agregada</title>
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
        <?php if (isset($_SESSION['error_message'])): ?>
            <h1>¡Error!</h1>
            <p><?php echo $_SESSION['error_message']; ?></p>
        <?php else: ?>
            <h1>¡Categoría Agregada!</h1>
            <p><?php echo $_SESSION['message']; ?></p>
        <?php endif; ?>
        <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
        <br><br>
    </div>
</body>
</html>

<?php
unset($_SESSION['message']);
unset($_SESSION['error_message']);
header("refresh:3;url=../../views/administrador/vista_admin_productos.php");
?>
