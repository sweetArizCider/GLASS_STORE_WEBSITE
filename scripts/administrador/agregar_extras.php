<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<div class="container">
    <?php
    include '../../class/database.php';
    $db = new Database();
    $db->conectarDB();
    extract($_POST);

    try {
        $cadena = "call agregarextras('$id_venta', '$extras');";
        $db->ejecuta($cadena);
        $db->desconectarDB();
        echo "<div class='alert alert-success'> EXTRA AÑADIDO CORRECTAMENTE</div>";
        header("refresh:2;url=../../views/administrador/vista_admin_ventas.php");
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>No se pudo añadir el extra: {$e->getMessage()}</div>";
    }
    ?>
</div>
</body>
</html>
