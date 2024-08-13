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
  <style>
    .hidden {
    display: none;
}
  </style>
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
          <a href="../../../">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
             <img src="../../img/instalador/home.svg" alt="Perfil">
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
            <img src="../../img/admin/calendar.svg" alt="citas">
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
            <img src="../../img/admin/clipboard.svg" alt="citas">
            <span>Reportes</span>
          </a>
          <ul id="reporte" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../../views/instalador/vista_instalador_reportes.php" class="sidebar-link">Hacer reporte</a>
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
      <div class="text-center">

      </div>

      <!-- contenido general-->
      <div class="col-12 mb-4 card-bienvenida">
          <div class="text-center ">
            <div class="">
              <h5 class=" mensaje-bienvenida">Notificaciones</h5>
              <p class=" mensaje-sub"><mark class="marklued">¡ Esperamos que hoy te encuentres bien !</mark></p>
            </div>
          </div>
        </div>

      <div class="contenidoGeneral mt-4">
      <div class="general-container">
      


      <?php
if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];
    $consulta = "CALL roles_usuario(?)";
    $params = [$user];
    $roles = $db->seleccionar($consulta, $params);

    $es_instalador = false;
    if ($roles) {
        foreach ($roles as $rol) {
            if ($rol->nombre_rol == 'instalador') {
                $es_instalador = true;
                break;
            }
        }
    } else {
        echo '<p>No se obtuvieron roles.</p>';
    }

    if ($es_instalador && isset($_SESSION["id_instalador"])) {
        $id_instalador = $_SESSION["id_instalador"];
        $notificaciones = $db->obtenerNotificacionesInstalador($id_instalador);

        if ($notificaciones) {
            $count = 0;
            foreach ($notificaciones as $notificacion) {
                $hiddenClass = $count >= 4 ? 'hidden' : '';
                echo '<div class="secc-sub-general ' . $hiddenClass . '">';
                echo '<p class="fecha">' . htmlspecialchars($notificacion->fecha) . '</p>';
                echo '<p>' . htmlspecialchars($notificacion->notificacion) . '</p>';
                echo '</div>';
                $count++;
            }
            if (count($notificaciones) > 4) {
                echo '<button id="verMasBtn" class="btn btn-secondary filters">Ver más</button>';
            }
        } else {
            echo '<p>No hay notificaciones para mostrar.</p>';
        }
    } else {
        echo '<p>No estás autorizado para ver las notificaciones.</p>';
    }
} else {
    echo '<p>Debes iniciar sesión para ver las notificaciones.</p>';
}
?>

          
        </div>
      </div>
    
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

    document.addEventListener('DOMContentLoaded', function() {
        const verMasBtn = document.getElementById('verMasBtn');
        if (verMasBtn) {
            verMasBtn.addEventListener('click', function() {
                // Mostrar todas las notificaciones que están ocultas
                document.querySelectorAll('.hidden').forEach(function(notificacion) {
                    notificacion.classList.remove('hidden');
                });
                // Ocultar el botón de "Ver más"
                verMasBtn.style.display = 'none';
            });
        }
    });
  </script>

</body>
</html>
