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
include '../../class/database.php';
$db = new Database();
$db->conectarDB();

if (isset($_POST['id_cita']) && isset($_POST['motivo'])) {
    $id_cita = $_POST['id_cita'];
    $motivo = $_POST['motivo'];

    try {
        $db->ejecutar("CALL RechazarCita(:id_cita, :motivo)", [
            ':id_cita' => $id_cita,
            ':motivo' => $motivo
        ]);
        
        echo"<div class='alert alert-success'>CITA RECHAZADA CON EXITO</div>";
        header ("refresh:3 ; ../../views/administrador/vista_admin_citas.php");
    } catch (Exception $e) {
        echo"<div class='alert alert-success'>No se puede</div>";
        header ("refresh:3 ; ../../views/administrador/vista_admin_citas.php");
    }
} else {
    header("Location: citas.php?status=error&message=Datos incompletos.");
}
?>
</body>
</html>

