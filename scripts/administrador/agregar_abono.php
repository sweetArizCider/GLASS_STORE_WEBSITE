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

    // Obtener la fecha actual para p_fecha_pago
    $fecha_pago = date('Y-m-d H:i:s');

    try {
        // Llamada al procedimiento almacenado con los 3 parámetros requeridos
        $cadena = "call registrar_abono('$id_venta', '$abono', '$fecha_pago');";
        $db->ejecuta($cadena);
        $db->desconectarDB();
        echo "<div class='alert alert-success'> ABONO AÑADIDO CORRECTAMENTE</div>";
        header("refresh:2;url=../../views/administrador/vista_admin_ventas.php");
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>No se pudo añadir el abono: {$e->getMessage()}</div>";
    }
    ?>
</div>
</body>
</html>

