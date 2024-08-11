<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

include '../../class/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["nombre_cliente_venta"]) && !empty($_POST["cita_id"])) {
    $nombre_cliente = $_POST["nombre_cliente_venta"];
    $cita_id = $_POST["cita_id"];
    $db = new database();
    $db->conectarDB();
    $db->configurarConexionPorRol();

    try {
        $stmt = $db->getPDO()->prepare("CALL crearventaporclienteycita(?, ?)");
        $stmt->execute([$nombre_cliente, $cita_id]);

        echo "<div class='alert alert-success'> VENTA CREADA EXITOSAMENTE</div>";
        header("refresh:2; ../../views/administrador/vista_admin_crear_venta.php");
        exit();
    } catch (Exception $e) {
        error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
        echo "<div class='alert alert-danger'> Error al crear la venta. Esta cita ya esta enlazada a una venta.</div>";
        header("refresh:2; ../../views/administrador/vista_admin_crear_venta.php");
        exit();
    }
}
?>

</body>
</html>
