<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Glass Store</title>
  <link rel="shortcut icon" href="../../img/index/logoVarianteSmall.png" type="image/x-icon">
  <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../css/normalized.css">
  <link rel="stylesheet" href="../../css/style_admin.css">
</head>
<body>
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

if ($result) {
    $_SESSION["id_instalador"] = $result->id_instalador;
    $id_instalador = $result->id_instalador;

    $stmt_citas = $db->getPDO()->prepare("CALL obtener_citas_instalador(?)");
    $stmt_citas->execute([$id_instalador]);
    $citas = $stmt_citas->fetchAll(PDO::FETCH_OBJ);
    
    $stmt_citas->closeCursor();

    foreach ($citas as $cita) {
      if (isset($cita->id_cita)) {
          $stmt_detalles = $db->getPDO()->prepare("CALL obtener_detalles_producto_cita(?)");
          $stmt_detalles->execute([$cita->id_cita]);
          $detalles_producto = $stmt_detalles->fetchAll(PDO::FETCH_OBJ);
  
          $cita->productos = $detalles_producto;
  
          $stmt_detalles->closeCursor();
      } else {
          $cita->productos = [];
      }
  }
} else {
    echo 'No se encontró el ID del instalador para el usuario.';
    $citas = []; 
}
?>
  <!-- Logo flotante del negocio -->
  <div id="logotipo-flotante">
    <img src="../../img/index/GLASS.png" alt="Glass store">
  </div>

  <!-- Barra lateral -->
  <div class="wrapper">
    <aside id="sidebar">
    <div class="d-flex">
        <button class="toggle-btn" type="button">
          <img src="../../img/index/menu.svg" alt="Menu">
        </button>
        <div class="sidebar-logo">
          <a href="#">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
            <img src="../../img/instalador/home.svg" alt="Inicio">
            <span>Inicio</span>
          </a>
          <ul id="inicio" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="#" class="sidebar-link">Volver al Inicio</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#notificaciones" aria-expanded="false" aria-controls="notificaciones">
            <img src="../../img/instalador/notificacion.svg" alt="Notificaciones">
            <span>Notificaciones</span>
          </a>
          <ul id="notificaciones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_instalador_notificaciones.php" class="sidebar-link">Ver Notificaciones</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#citas" aria-expanded="false" aria-controls="citas">
            <img src="../../img/admin/calendar.svg" alt="Citas">
            <span>Citas</span>
          </a>
          <ul id="citas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../../views/instalador/vista_instalador_citas.php" class="sidebar-link">Ver Citas</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#reporte" aria-expanded="false" aria-controls="reporte">
            <img src="../../img/admin/clipboard.svg" alt="Reportes">
            <span>Reportes</span>
          </a>
          <ul id="reporte" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../../views/instalador/vista_instalador_reportes.php" class="sidebar-link">Hacer Reporte</a>
            </li>
          </ul>
        </li>
      </ul>
      <div class="sidebar-footer">
        <a href="./index_Instalador.php" class="sidebar-link">
          <img src="../../img/admin/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-footer">
        <a href="#" class="sidebar-link">
          <img src="../../img/admin/logout.svg" alt="Cerrar Sesión">
          <span>Cerrar Sesión</span>
        </a>
      </div>
    </aside>
    <div class="main p-3">
      <!-- Contenido general -->
      <div class="contenidoGeneral mt-4">
        <div class="general-container">
          <div class="d-flex justify-content-end mt-4">
            <div class="dropdown">
              <button class="btn btn-secondary filters" type="button" id="dropdownOrdenar" data-bs-toggle="dropdown" aria-expanded="false">
                Ordenar <img src="../../img/instalador/filter.svg" alt="Filtrar" class="icono-filtro">
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownOrdenar">
                <li><a class="dropdown-item" href="#">Recientes</a></li>
                <li><a class="dropdown-item" href="#">Antiguos</a></li>
              </ul>
            </div>
          </div>
          <!-- Acordeón de Citas -->
          <div class="accordion" id="accordionCitas">
    <?php foreach ($citas as $index => $cita): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= htmlspecialchars($cita->id_cita) ?>">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= htmlspecialchars($cita->id_cita) ?>" aria-expanded="true" aria-controls="collapse<?= htmlspecialchars($cita->id_cita) ?>">
                    <?= htmlspecialchars($cita->cliente) ?> - <?= htmlspecialchars($cita->fecha) ?> <?= htmlspecialchars($cita->hora) ?>
                </button>
            </h2>
            <div id="collapse<?= htmlspecialchars($cita->id_cita) ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= htmlspecialchars($cita->id_cita) ?>" data-bs-parent="#accordionCitas">
                <div class="accordion-body">
                    <p><strong>Dirección:</strong> <?= htmlspecialchars($cita->direccion) ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($cita->tipo) ?></p>
                    <p><strong>Notas:</strong> <?= htmlspecialchars($cita->notas) ?></p>

                    <!-- Lista de Detalles del Producto -->
                    <h5>Detalles del Producto</h5>
                    <ul class="list-group">
                        <?php if (!empty($cita->productos)): ?>
                            <?php foreach ($cita->productos as $producto): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (!empty($producto->producto_nombre)): ?>
                                            <p><strong>Nombre:</strong> <?= htmlspecialchars($producto->producto_nombre) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_monto)): ?>
                                            <p><strong>Monto:</strong> <?= htmlspecialchars($producto->producto_monto) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_alto)): ?>
                                            <p><strong>Alto:</strong> <?= htmlspecialchars($producto->producto_alto) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_largo)): ?>
                                            <p><strong>Largo:</strong> <?= htmlspecialchars($producto->producto_largo) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_cantidad)): ?>
                                            <p><strong>Cantidad:</strong> <?= htmlspecialchars($producto->producto_cantidad) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_grosor)): ?>
                                            <p><strong>Grosor:</strong> <?= htmlspecialchars($producto->producto_grosor) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_tipo_tela)): ?>
                                            <p><strong>Tipo de Tela:</strong> <?= htmlspecialchars($producto->producto_tipo_tela) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_marco)): ?>
                                            <p><strong>Marco:</strong> <?= htmlspecialchars($producto->producto_marco) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_tipo_cadena)): ?>
                                            <p><strong>Tipo de Cadena:</strong> <?= htmlspecialchars($producto->producto_tipo_cadena) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_color)): ?>
                                            <p><strong>Color:</strong> <?= htmlspecialchars($producto->producto_color) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($producto->producto_diseno)): ?>
                                            <p><strong>Diseño:</strong> <?= htmlspecialchars($producto->producto_diseno) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalReporte<?= htmlspecialchars($producto->id_detalle) ?>">Generar Reporte</button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No hay detalles de productos para esta cita.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const hamBurger = document.querySelector(".toggle-btn");
    hamBurger.addEventListener("click", function () {
      document.querySelector("#sidebar").classList.toggle("expand");
    });

    

  </script>
</body>
</html>