<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Quitar Rol</title>
</head>
<body>
<div class="container">
    <?php
    include '../../class/database.php';
    $db = new Database();
    $db->conectarDB();
    extract($_POST);

    try {
        $cadena = "CALL quitarrolusuariopornombre(?, ?)";
        $stmt = $db->getPDO()->prepare($cadena);
        $stmt->execute([$nom_usuario_quitar, $nombre_rol_quitar]);
        
        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-success'>ROL QUITADO EXITOSAMENTE</div>";
        } else {
            echo "<div class='alert alert-warning'>No se pudo quitar el rol. Verifica el nombre de usuario y el rol.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al quitar rol: " . htmlspecialchars($e->getMessage()) . "</div>";
    } finally {
        $db->desconectarDB();
    }
    header("refresh:3;../../views/administrador/vista_admin_darRol.php");
    ?>
</div>
</body>
</html>
