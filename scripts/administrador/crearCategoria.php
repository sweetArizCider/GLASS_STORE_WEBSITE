<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Agregar producto</title>
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <?php
        include '../../class/database.php';
        $db = new Database();
        $db-> conectarDB();
        extract($_POST);

        $cadena = "call añadircategoria('$nombre');";
        $db->ejecuta($cadena);
        $db->desconectarDB();

        echo "<div class='alert alert-success'> CATEGORIA CREADA CORRECTAMENTE</div>";
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
        ?>
    </div>

</body>
</html>