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

if ($result) {
    $_SESSION["id_instalador"] = $result->id_instalador;
    $id_instalador = $result->id_instalador;

    $stmt_citas = $db->getPDO()->prepare("CALL obtener_citas_instalador(?)");
    $stmt_citas->execute([$id_instalador]);
    $citas = $stmt_citas->fetchAll(PDO::FETCH_OBJ);
    
    $stmt_citas->closeCursor();

    foreach ($citas as $cita) {
        $cita->nombre_cliente = isset($cita->cliente) ? $cita->cliente : 'Cliente Desconocido';
        $cita->direccion = isset($cita->direccion) ? $cita->direccion : 'Dirección no disponible';
        $cita->tipo = isset($cita->tipo) ? $cita->tipo : 'Tipo no disponible';

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


  <!--Barra lateral-->
  <div class="wrapper">
    <aside id="sidebar">
    <div class="d-flex">
        <button class="toggle-btn" type="button">
          <img src="../../img/index/menu.svg" alt="Menu">
        </button>
        <div class="sidebar-logo">
          <a href="../../../../">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="../" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
            <img src="../../img/instalador/home.svg" alt="Inicio">
            <span>Inicio</span>
          </a>
          <ul id="inicio" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./index_Instalador.php" class="sidebar-link">Volver al Inicio</a>
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
        <a href="../../scripts/cerrarSesion.php" class="sidebar-link">
            <img src="../../img/admin/logout.svg" alt="Cerrar Sesión">
            <span>Cerrar Sesión</span>
        </a>
    </div>
     
      
    </aside>


    <div class="main p-3">
    <div class="main p-3">
      <div class="text-center">
        <div class="busqueda mx-auto">
          <input type="text" placeholder="Buscar" class="buscar-input" id="search-input">
          <img src="../../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer;">
        </div>
      </div>
      <!-- Contenido general -->
      <div class="contenidoGeneral mt-4">
        <div class="general-container">
          <div class="d-flex justify-content-end mt-4">
            <div class="dropdown">
              <button class="btn btn-secondary filters" type="button" id="dropdownOrdenar" data-bs-toggle="dropdown" aria-expanded="false">
                Ordenar <img src="../../img/instalador/filter.svg" alt="Filtrar" class="icono-filtro">
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownOrdenar">
                <li><a class="dropdown-item" href="#" onclick="sortCitas('recientes')">Recientes</a></li>
                <li><a class="dropdown-item" href="#" onclick="sortCitas('antiguos')">Antiguos</a></li>
              </ul>
            </div>
          </div>
          <!-- Acordeón de Citas -->
          <div class="accordion" id="accordionCitas">
    <?php foreach ($citas as $index => $cita): ?>
        <div class="accordion-item" data-cliente="<?= htmlspecialchars($cita->nombre_cliente) ?>" data-fecha="<?= htmlspecialchars($cita->fecha) ?>">
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
                                        <!-- Other product properties -->
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

  // Búsqueda por nombre de cliente
  document.getElementById('search-input').addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const citas = document.querySelectorAll('.accordion-item');

      citas.forEach(cita => {
          const cliente = cita.querySelector('.accordion-button').textContent.toLowerCase();
          if (cliente.includes(searchValue)) {
              cita.style.display = '';  // Muestra la cita
          } else {
              cita.style.display = 'none';  // Oculta la cita
          }
      });
  });

  // Ordenar citas por fecha
  function sortCitas(order) {
      const accordion = document.getElementById('accordionCitas');
      const citas = Array.from(accordion.querySelectorAll('.accordion-item'));

      citas.sort((a, b) => {
          const dateA = new Date(a.getAttribute('data-fecha'));
          const dateB = new Date(b.getAttribute('data-fecha'));

          return order === 'recientes' ? dateB - dateA : dateA - dateB;
      });

      citas.forEach(cita => accordion.appendChild(cita));
  }
</script>


</body>
</html>