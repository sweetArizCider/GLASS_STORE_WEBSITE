<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Promoción</title>
</head>
<body>
<div class="container">
    <?php
    include '../../class/database.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_promocion = $_POST['nombre_promocion'];
        $tipo_promocion = $_POST['tipo_promocion'];
        $valor = $_POST['valor'];

        if ($tipo_promocion == 'porcentual') {
            $valor = $valor / 100;
        }

        $database = new Database();
        $database->conectarDB();

        // Definir el parámetro de salida
        $resultado = 0;

        // Llamar al procedimiento almacenado
        $query = "CALL crearpromocion(:nombre_promocion, :tipo_promocion, :valor, @resultado)";
        $params = array(
            ':nombre_promocion' => $nombre_promocion,
            ':tipo_promocion' => $tipo_promocion,
            ':valor' => $valor
        );

        $database->ejecutarcita($query, $params);
        
        $query = "SELECT @resultado AS resultado";
        $stmt = $database->ejecutarcita($query, array()); 

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $resultado = $result['resultado'];

            if ($resultado == 1) {
                echo "<div class='alert alert-success'>PROMOCIÓN AÑADIDA CORRECTAMENTE</div>";
                header("refresh:2;../../views/administrador/vista_admin_promos.php");
                exit();
            } elseif ($resultado == 2) {
                echo "<div class='alert alert-danger'>Error: La promoción con ese nombre ya existe</div>";
                header("refresh:2;../../views/administrador/vista_admin_promos.php");
            }
        } else {
            echo "<div class='alert alert-danger'>Error: No se pudo determinar el resultado de la operación</div>";
            header("refresh:2;../../views/administrador/vista_admin_promos.php");
        }

        $database->desconectarDB();
    }
    ?>
</div>
</body>
</html>
