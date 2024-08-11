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
            <li><a class="dropdown-item" href="#" id="ordenar-recientes">Recientes</a></li>
            <li><a class="dropdown-item" href="#" id="ordenar-antiguas">Antiguas</a></li>
          </ul>
        </div>

          </div>

<!-- Sección para mostrar citas -->
<div class="citas-container" id="resultados">
    <?php
    if (isset($_SESSION["id_instalador"])) {
        $id_instalador = $_SESSION["id_instalador"];
        $citas = $db->obtenerCitasInstalador($id_instalador);

        if ($citas) {
            foreach ($citas as $cita) {
                $fecha = date('d \d\e F \d\e Y', strtotime($cita->fecha));
                $hora = date('h:i A', strtotime($cita->hora));

                $cliente = $cita->cliente ?? 'Desconocido';
                $calle = $cita->calle ?? 'No disponible';
                $numero = $cita->numero ?? 'No disponible';
                $numero_int = $cita->numero_int ?? ''; // Puede estar vacío
                $colonia = $cita->colonia ?? 'No disponible';
                $ciudad = $cita->ciudad ?? 'No disponible';
                $referencias = $cita->referencias ?? 'No disponible';

                echo '<div class="secc-sub-general cita-item">';
                echo '<p class="fecha">' . $fecha . '</p>';
                echo '<p><mark class="marklued">' . htmlspecialchars($cliente) . '</mark><br> Requiere <span class="bueld">una Instalación</span> en el domicilio: <span class="bueld">' . htmlspecialchars($calle) . ' #' . htmlspecialchars($numero) . ' ' . htmlspecialchars($numero_int) . ', ' . htmlspecialchars($colonia) . ', ' . htmlspecialchars($ciudad) . ' referencias: ' . htmlspecialchars($referencias) . '</span> <br> el día <span class="bueld">' . $fecha . '</span> a las <span class="bueld">' . $hora . '</span></p>';
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

  // Filtro de citas por nombre en tiempo real
  document.getElementById('search-input').addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const citas = document.querySelectorAll('.cita-item');

      citas.forEach(cita => {
          const cliente = cita.querySelector('.marklued').textContent.toLowerCase();
          if (cliente.includes(searchValue)) {
              cita.style.display = '';  // Mostrar cita si coincide
          } else {
              cita.style.display = 'none';  // Ocultar cita si no coincide
          }
      });
  });

  // Ordenar citas por fecha
  document.getElementById('ordenar-recientes').addEventListener('click', function() {
      ordenarCitas('recientes');
  });

  document.getElementById('ordenar-antiguas').addEventListener('click', function() {
      ordenarCitas('antiguas');
  });

  function ordenarCitas(orden) {
      const citas = Array.from(document.querySelectorAll('.cita-item'));
      citas.sort((a, b) => {
          const fechaA = convertirFecha(a.querySelector('.fecha').textContent);
          const fechaB = convertirFecha(b.querySelector('.fecha').textContent);

          return orden === 'recientes' ? fechaB - fechaA : fechaA - fechaB;
      });

      const container = document.getElementById('resultados');
      container.innerHTML = '';
      citas.forEach(cita => {
          container.appendChild(cita);
      });
  }

  function convertirFecha(fechaTexto) {
      const partes = fechaTexto.split(' ');
      const dia = partes[0];
      const mes = partes[2];
      const anio = partes[4];

      const meses = {
          'enero': '01',
          'febrero': '02',
          'marzo': '03',
          'abril': '04',
          'mayo': '05',
          'junio': '06',
          'julio': '07',
          'agosto': '08',
          'septiembre': '09',
          'octubre': '10',
          'noviembre': '11',
          'diciembre': '12'
      };

      const mesNumero = meses[mes.toLowerCase()];

      return new Date(`${anio}-${mesNumero}-${dia}`);
  }
  </script>


</body>
</html>
