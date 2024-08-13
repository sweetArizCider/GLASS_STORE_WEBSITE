<?php
session_start();
include '../../class/database.php';

$db = new database();
$db->conectarDB();

$user = $_SESSION["nom_usuario"];

// Consulta para obtener el id_instalador
$stmt = $db->getPDO()->prepare("
    SELECT i.id_instalador
    FROM instalador i
    JOIN persona p ON i.persona = p.id_persona
    JOIN usuarios u ON p.usuario = u.id_usuario
    WHERE u.nom_usuario = ?
");
$stmt->execute([$user]);
$result = $stmt->fetch(PDO::FETCH_OBJ);

// Verificar si result no es nulo y tiene la propiedad id_instalador
if ($result && isset($result->id_instalador)) {
    $id_instalador = $result->id_instalador;
} else {
    // Manejo del caso en que no se encuentra el instalador
    echo "<script>alert('Instalador no encontrado.');</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $detalle_cita = $_POST['detalle_cita'];
    $categoria = $_POST['categoria'];
    $alto = $_POST['alto'] ?? null;
    $largo = $_POST['largo'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $monto = $_POST['monto'] ?? null;
    $extras = $_POST['extras'] ?? null;
    $notas = $_POST['notas'] ?? null;
    $grosor = $_POST['grosor'] ?? null;
    $tipo_tela = $_POST['tipo_tela'] ?? null;
    $marco = $_POST['marco'] ?? null;
    $tipo_cadena = $_POST['tipo_cadena'] ?? null;
    $color = $_POST['color'] ?? null;

    // Corregir la búsqueda del diseño
    $diseno_codigo = $_POST['diseno'] ?? null;

    // Consulta para obtener el id_diseno basado en el codigo
    $query = "SELECT id_diseno FROM disenos WHERE codigo = :codigo";
    $params = [':codigo' => $diseno_codigo];

    $designResult = $db->executeQueryWithParams($query, $params);

    if ($designResult && isset($designResult[0]->id_diseno)) {
        $id_diseno = $designResult[0]->id_diseno;
    } else {
        // Manejo del caso en que no se encuentra el diseño
        $id_diseno = null;
    }

    // Insertar el reporte en la base de datos
    $insertQuery = $db->getPDO()->prepare("
        INSERT INTO reporte (detalle_cita, instalador, alto, largo, cantidad, monto, extras, notas, grosor, tipo_tela, marco, tipo_cadena, color, diseno)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertSuccess = $insertQuery->execute([
        $detalle_cita,
        $id_instalador, // Asegúrate de que este valor no sea null
        $alto,
        $largo,
        $cantidad,
        $monto,
        $extras,
        $notas,
        $grosor,
        $tipo_tela,
        $marco,
        $tipo_cadena,
        $color,
        $id_diseno
    ]);

    if ($insertSuccess) {
        // Actualizar el estatus del detalle de la cita a "activo"
        $updateQuery = $db->getPDO()->prepare("
            UPDATE detalle_cita
            SET estatus = 'activo'
            WHERE id_detalle_cita = ?
        ");
        $updateQuery->execute([$detalle_cita]);

        // Redireccionar o mostrar mensaje de éxito
        $_SESSION['message'] = 'Reporte generado exitosamente.';
        echo "<script>
    setTimeout(function() {
        window.location.href = '../../views/instalador/vista_instalador_reportes.php';
    }, 1000);
</script>";
        exit();
    } else {
        // Manejar el caso en que la inserción falle
        echo "<script>alert('Error al generar el reporte.');
        setTimeout(function() {
        window.location.href = '../../views/instalador/vista_instalador_reportes.php';
    }, 1000);</script>";
    }


}




?>
