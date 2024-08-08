<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Eliminar Promoción</title>
</head>
<body>
<div class="container">
    <?php
    include '../../class/database.php';
    $db = new database();
    $db->conectarDB();

    // Verificar si los datos POST están presentes
    if (isset($_POST['nombre_promocion']) && isset($_POST['id_venta'])) {
        $nombre_promocion = $_POST['nombre_promocion'];
        $id_venta = $_POST['id_venta'];

        try {
            // Consultar el id_promocion
            $query = "SELECT id_promocion FROM promociones WHERE nombre_promocion = :nombre_promocion";
            $stmt = $db->getPDO()->prepare($query);
            $stmt->execute([':nombre_promocion' => $nombre_promocion]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if ($result) {
                $id_promocion = $result->id_promocion;

                // Llamar al procedimiento almacenado
                $query = "CALL quitarpromocionporventa(:id_venta, :nombre_promocion)";
                $stmt = $db->getPDO()->prepare($query);
                $stmt->execute([':id_venta' => $id_venta, ':nombre_promocion' => $nombre_promocion]);

                if ($stmt->rowCount() > 0) {
                    echo "<div class='alert alert-success'>Promoción eliminada correctamente.</div>";
                } else {
                    echo "<div class='alert alert-warning'>No se pudo eliminar la promoción. Verifica los datos.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Promoción no encontrada.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error al eliminar la promoción: " . htmlspecialchars($e->getMessage()) . "</div>";
        } finally {
            $db->desconectarDB();
        }
    } else {
        echo "<div class='alert alert-danger'>Faltan datos para procesar la solicitud.</div>";
    }

    // Redirigir después de un breve retraso
    header("refresh:3;../../views/administrador/vista_admin_ventas.php");
    ?>
</div>
</body>
</html>
