<?php
session_start();
include '../../class/database.php';

$db = new database();
$db->conectarDB();
$db->configurarConexionPorRol();

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
// Código PHP para manejar la búsqueda del diseño
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $detalle_cita = $_POST['detalle_cita'];
  $categoria = $_POST['categoria'];
  $alto = $_POST['alto'];
  $largo = $_POST['largo'];
  $diseno_codigo = $_POST['diseno'];
  $extras = $_POST['extras'];
  $notas = $_POST['notas'];
  $grosor = $_POST['grosor'] ?? null;
  $tipo_tela = $_POST['tipo_tela'] ?? null;
  $marco = $_POST['marco'] ?? null;
  $tipo_cadena = $_POST['tipo_cadena'] ?? null;
  $color = $_POST['color'] ?? null;

  // Buscar el ID del diseño en la base de datos
  $disenoQuery = $db->getPDO()->prepare("SELECT id_diseno FROM DISENOS WHERE codigo = ? AND estatus = 'activo'");
  $disenoQuery->execute([$diseno_codigo]);
  $diseno = $disenoQuery->fetch(PDO::FETCH_OBJ);

  if ($diseno) {
      $id_diseno = $diseno->id_diseno;

      // Insertar el reporte en la base de datos con el id_diseno obtenido
      $insertQuery = $db->getPDO()->prepare("
          INSERT INTO reporte (detalle_cita, instalador, alto, largo, diseno, extras, notas, grosor, tipo_tela, marco, tipo_cadena, color)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
      $insertQuery->execute([
          $detalle_cita,
          $result->id_instalador,
          $alto,
          $largo,
          $id_diseno,
          $extras,
          $notas,
          $grosor,
          $tipo_tela,
          $marco,
          $tipo_cadena,
          $color
      ]);

      // Actualizar el estatus del detalle de la cita a "activo"
      $updateQuery = $db->getPDO()->prepare("
          UPDATE detalle_cita
          SET estatus = 'activo'
          WHERE id_detalle_cita = ?
      ");
      $updateQuery->execute([$detalle_cita]);

      // Redireccionar o mostrar mensaje de éxito
      header('Location: vista_instalador_reportes.php?status=success');
      exit();
  } else {
      // Manejar el caso en que el diseño no se encuentre
      echo "<script>alert('Diseño no encontrado.');</script>";
  }
}
?>
