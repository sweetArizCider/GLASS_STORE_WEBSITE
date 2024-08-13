<?php
session_start();
include '../../class/database.php';

// Crear instancia de la clase y conectar a la base de datos
$db = new database();
$db->conectarDB();
$pdo = $db->getPDO();

try {
    // Consultar el ID del instalador basado en la sesión
    $user = $_SESSION["nom_usuario"];
    $stmt = $pdo->prepare("
        SELECT i.id_instalador, p.nombres, p.apellido_p
        FROM instalador i
        JOIN persona p ON i.persona = p.id_persona
        JOIN usuarios u ON p.usuario = u.id_usuario
        WHERE u.nom_usuario = ?
    ");
    $stmt->execute([$user]);
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $_SESSION["id_instalador"] = $result->id_instalador;
        $nombreCompleto = htmlspecialchars($result->nombres . ' ' . $result->apellido_p);

        // Obtener el total de citas asignadas en el día
        $stmt = $pdo->prepare("CALL contarcitasdiainstalador(?, CURDATE(), @total_citas_dia)");
        $stmt->execute([$result->id_instalador]);

        $stmt = $pdo->query("SELECT @total_citas_dia AS total_citas_dia");
        $totalCitasDia = $stmt->fetch(PDO::FETCH_OBJ)->total_citas_dia;

        // Obtener el total de citas asignadas en el mes
        $stmt = $pdo->prepare("CALL contarcitasmesinstalador(?, MONTH(CURDATE()), YEAR(CURDATE()), @total_citas_mes)");
        $stmt->execute([$result->id_instalador]);

        $stmt = $pdo->query("SELECT @total_citas_mes AS total_citas_mes");
        $totalCitasMes = $stmt->fetch(PDO::FETCH_OBJ)->total_citas_mes;
    } else {
        echo 'No se encontró el ID del instalador para el usuario.';
        $nombreCompleto = "";
        $totalCitasDia = 0;
        $totalCitasMes = 0;
    }
} catch (PDOException $e) {
    echo "Error al obtener datos: " . $e->getMessage();
}
?>

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
  <div class="contenidoGeneral mt-4">
    <div class="general-container">
      <div class="row">
        <!-- Card de bienvenida -->
        <div class="col-12 mb-4 card-bienvenida">
          <div class="text-center ">
            <div class="">
              <h5 class=" mensaje-bienvenida">Bienvenido, <?php echo $nombreCompleto; ?></h5>
              <p class=" mensaje-sub"><mark class="marklued">¡ Esperamos que hoy te encuentres bien !</mark></p>
            </div>
          </div>
        </div>
        <!-- Contadores -->
        <div class="col-md-6 mb-4">
          <div class="secc-sub-general  text-center">
            <div class="card-body">
              <div class="">
              <p class="card-text"><strong>El día de hoy tienes</strong></p>
              </div>
              <h3 class="card-title contador"><?php echo $totalCitasDia; ?></h3>
              <p class="card-text"><strong>citas asignadas</strong></p>
              <p class="card-text dirigete">Dirígete al apartado de citas para <a href="./vista_instalador_citas.php"> más información</a>.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="secc-sub-general text-center">
            <div class="card-body">
              <div class="">
              <p class="card-text"><strong>Este  mes tienes</strong></p>
              </div>
              <h3 class="card-title contador"><?php echo $totalCitasMes; ?></h3>
              <p class="card-text"><strong>citas asignadas</strong></p>
              <p class="card-text dirigete">Dirígete al apartado de citas para <a href="./vista_instalador_citas.php"> más información</a>.</p>
            </div>
          </div>
        </div>
        <div class="col-12 mt-4">
          <div class="text-center">
            <div class="">
              <p class="">
                <strong>¡ En <span class="strong-bsname">GLASS STORE</span> agradecemos tu esfuerzo y dedicación!</strong>
              </p>
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
  </script>
</body>
</html>
