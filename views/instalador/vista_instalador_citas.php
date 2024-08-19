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
    <div class="col-12 mb-4 card-bienvenida">
          <div class="text-center ">
            <div class="">
              <h5 class=" mensaje-bienvenida">Citas</h5>
              <p class=" mensaje-sub"><mark class="marklued">¡ Esperamos que hoy te encuentres bien !</mark></p>
            </div>
          </div>
        </div>
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

          </div>

          <!-- Sección para mostrar citas -->
          <div class="citas-container" id="resultados">
            <?php
            if (isset($_SESSION["id_instalador"])) {
                $id_instalador = $_SESSION["id_instalador"];
                $citas = $db->obtenercitasinstalador($id_instalador);
                $totalCitas = count($citas);

                if ($citas) {
                    foreach (array_slice($citas, 0, 3) as $cita) {
                        $fecha = date('d \d\e F \d\e Y', strtotime($cita->fecha));
                        $hora = date('h:i A', strtotime($cita->hora));
                        $cliente = $cita->cliente;
                        $direccion = $cita->direccion;
                        $tipo = $cita->tipo;

                        echo '<div class="secc-sub-general cita-item">';
                        echo '<p class="fecha">' . $fecha . '</p>';
                        echo '<p><mark class="marklued">' . htmlspecialchars($cliente) . '</mark><br> Requiere <span class="bueld">' . htmlspecialchars($tipo) . '</span> en el domicilio: <span class="bueld">' . htmlspecialchars($direccion) . '</span> <br> el día <span class="bueld">' . $fecha . '</span> a las <span class="bueld">' . $hora . '</span></p>';
                        echo '</div> <br>';
                    }

                    if ($totalCitas > 3) {
                        echo '<button id="verMasBtn" class="btn btn-secondary filters"">Ver más</button>';
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

  let citas = <?php echo json_encode($citas); ?>;
  let loadedCitas = 3;

  function renderCitas(citas) {
    const container = document.getElementById('resultados');
    container.innerHTML = '';

    citas.forEach(cita => {
      // Usa la fecha original y asegura el formateo correcto en inglés
      const fechaOriginal = cita.fecha;
      const [year, month, day] = fechaOriginal.split('-');
      const formattedDate = new Date(year, month - 1, day).toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
      });

      // Formatear la hora correctamente
      const hora = new Date(`1970-01-01T${cita.hora}`).toLocaleTimeString('en-US', {
        hour: '2-digit', minute: '2-digit', hour12: true
      });

      const cliente = cita.cliente;
      const direccion = cita.direccion;
      const tipo = cita.tipo;

      container.innerHTML += `
        <div class="secc-sub-general cita-item">
          <p class="fecha">${formattedDate}</p>
          <p><mark class="marklued">${cliente}</mark><br> Requiere <span class="bueld">${tipo}</span> en el domicilio: <span class="bueld">${direccion}</span> <br> el día <span class="bueld">${formattedDate}</span> a las <span class="bueld">${hora}</span></p>
        </div> <br>
      `;
    });
  }

  document.getElementById('verMasBtn').addEventListener('click', function () {
    const newCitas = citas.slice(loadedCitas, loadedCitas + 3);
    renderCitas(citas.slice(0, loadedCitas + 3));
    loadedCitas += 3;

    if (loadedCitas >= citas.length) {
      this.style.display = 'none';
    }

    // Hacer scroll hacia abajo después de cargar más citas
    document.getElementById('verMasBtn').scrollIntoView({ behavior: 'smooth' });
  });

  document.getElementById('search-input').addEventListener('input', function () {
    const searchValue = this.value.toLowerCase();
    const filteredCitas = citas.filter(cita => cita.cliente.toLowerCase().includes(searchValue));
    renderCitas(filteredCitas);
  });

  document.getElementById('ordenar-recientes').addEventListener('click', function () {
    citas.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
    renderCitas(citas.slice(0, loadedCitas));
  });

  document.getElementById('ordenar-antiguas').addEventListener('click', function () {
    citas.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
    renderCitas(citas.slice(0, loadedCitas));
  });
</script>
</body>
</html>
