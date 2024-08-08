<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Actualizar Datos Laborales</title>
    <script>
        function redirigir(url) {
            setTimeout(function() {
                window.location.href = url;
            }, 2000); // Redirige después de 2 segundos
        }
    </script>
</head>
<body>
<div class="container">
    <?php
    session_start();

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION["nom_usuario"])) {
        error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
        header("Location: ../iniciarSesion.php");
        exit();
    }

    include '../../class/database.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_completo = $_POST['nombre_completo'];
        $rfc = $_POST['rfc'];
        $nss = $_POST['nss'];
        $curp = $_POST['curp'];

        $db = new database();
        $db->conectarDB();

        try {
            // Llamar al procedimiento para buscar la persona y obtener el ID
            $stmt = $db->getPDO()->prepare("CALL buscarpersonapornombre(?, @id_persona)");
            $stmt->execute([$nombre_completo]);
            
            // Obtener el ID de la persona
            $stmt = $db->getPDO()->prepare("SELECT @id_persona AS id_persona");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_persona = $result['id_persona'];
            
            if ($id_persona) {
                // Llamar al procedimiento para actualizar los datos laborales
                $stmt = $db->getPDO()->prepare("CALL actualizardatoslaborales(?, ?, ?, ?)");
                $stmt->execute([$id_persona, $rfc, $nss, $curp]);
                $message = "DATOS ACTUALIZADOS CORRECTAMENTE";
                $alert_class = "alert-success";
                $redirect_page = "../../views/administrador/vista_admin_gestionainstalador.php";
            } else {
                $message = "Persona no encontrada.";
                $alert_class = "alert-danger";
                $redirect_page = "../../views/administrador/vista_admin_gestionainstalador.php";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "ERROR AL GUARDAR LOS DATOS";
            $alert_class = "alert-danger";
            $redirect_page = "../../views/administrador/vista_admin_gestionainstalador.php";
        }

        $db->desconectarDB();
        ?>

        <!-- Mostrar el mensaje y redirigir -->
        <div class='alert <?php echo $alert_class; ?>'>
            <?php echo $message; ?>
        </div>
        <script>
            redirigir("<?php echo $redirect_page; ?>");
        </script>

        <?php
        exit();
    }
    ?>
</div>
</body>
</html>
