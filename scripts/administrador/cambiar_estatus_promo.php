<?php
include '../../class/database.php'; 

$debugMessages = []; // Array para almacenar los mensajes de depuración

if (isset($_POST['cambiar_estatus'])) {
    $debugMessages[] = "Formulario enviado. ID Promoción: " . $_POST['id_promocion'];
    $id_promocion = $_POST['id_promocion'];
    $estatus_actual = $_POST['estatus_actual'];

    // Determinar el nuevo estatus
    $nuevo_estatus = ($estatus_actual === 'activo') ? 'inactivo' : 'activo';

    // Preparar la consulta
    $consulta = "CALL actualizarestatuspromocion(?, ?)";
    $params = [$id_promocion, $nuevo_estatus];

    try {
        $db = new Database(); 
        $debugMessages[] = "Conexión a la base de datos establecida.";

        // Ejecutar el procedimiento almacenado
        $debugMessages[] = "Ejecutando consulta: " . $consulta;
        $filas_afectadas = $db->ejecutarProcedimiento2($consulta, $params);

        // Verificar el resultado
        $debugMessages[] = "Filas afectadas: " . $filas_afectadas;

        if ($filas_afectadas > 0) {
            // Redirigir a la misma página para mostrar los cambios
            header("Location: ../../views/administrador/vista_admin_promos.php");
            exit();
        } else {
            $debugMessages[] = "No se realizaron cambios en la base de datos.";
        }
    } catch (Exception $e) {
        $debugMessages[] = "Error: " . $e->getMessage();
    }
} else {
    $debugMessages[] = "Formulario no enviado o parámetros incorrectos.";
}

// Mostrar mensajes de depuración en el HTML
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuración</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .debug { color: red; }
    </style>
</head>
<body>
    <h1>Mensajes de Depuración</h1>
    <div class="debug">
        <?php foreach ($debugMessages as $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
</body>
</html>
