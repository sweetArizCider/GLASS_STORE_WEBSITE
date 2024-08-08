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
} else {
    echo 'No se encontró el ID del instalador para el usuario.';
}
?>
  <!--Logo flotante del negocio-->
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
          <a href="#">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
             <img src="../../img/instalador/home.svg" alt="Perfil">
            <span>Inicio</span>
          </a>
          <ul id="home" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
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
            <img src="../../img/admin/calendar.svg" alt="citas">
            <span>Citas</span>
          </a>
          <ul id="citas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../../views/instalador/vista_instalador_citas.php" class="sidebar-link">Ver Citas</a>
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
      <div class="text-center">
        <div class="busqueda mx-auto">
          <input type="text" placeholder="Buscar" class="buscar-input" id="search-input">
          <img src="../../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer;">
        </div>
      </div>

      <!-- contenido general-->
      <div class="contenidoGeneral mt-4">
        <div class="general-container">
          <div class="d-flex justify-content-end mt-4">
            <div class="dropdown">
              <button class="btn btn-secondary filters" type="button" id="dropdownOrdenar" data-bs-toggle="dropdown" aria-expanded="false"> Ordenar <img src="../../img/instalador/filter.svg" alt="Filtrar" class="icono-filtro">
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownOrdenar">
                <li><a class="dropdown-item" href="#">Recientes</a></li>
                <li><a class="dropdown-item" href="#">Antiguas</a></li>
              </ul>
            </div>
          </div>

          <!-- Sección para mostrar citas -->
          <div class="citas-container">
          <?php

if (isset($_SESSION["id_instalador"])) {
    $id_instalador = $_SESSION["id_instalador"];
    $citas = $db->obtenerCitasInstalador($id_instalador);

    if ($citas) {
        foreach ($citas as $cita) {
            $fecha = date('d \d\e F \d\e Y', strtotime($cita->fecha));
            $hora = date('h:i A', strtotime($cita->hora));

            echo '<div class="secc-sub-general">';
            echo '<p class="fecha">' . $fecha . '</p>';
            echo '<p><mark class="marklued">' . htmlspecialchars($cita->cliente) . '</mark><br> Requiere <span class="bueld">una Instalación</span> en el domicilio: <span class="bueld">' . htmlspecialchars($cita->calle) . ' #' . htmlspecialchars($cita->numero) . ' ' . htmlspecialchars($cita->numero_int) . ', ' . htmlspecialchars($cita->colonia) . ', ' . htmlspecialchars($cita->ciudad) . ' referencias: ' . htmlspecialchars($cita->referencias) . '</span> <br> el día <span class="bueld">' . $fecha . '</span> a las <span class="bueld">' . $hora . '</span></p>';
            echo '</div> <br>';

        }
    } else {
        echo '<p>No hay citas para mostrar.</p>';
    }
} else {
    echo '<p>No estás autorizado para ver las citas.</p>';
}
?>

          </div>
        </div>
      </div>
    </div>
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
