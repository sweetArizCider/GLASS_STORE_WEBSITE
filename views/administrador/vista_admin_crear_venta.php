<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

include '../../class/database.php';

function consultarReportesCliente($nombre_cliente) {
    $db = new database();
    $db->conectarDB();

    try {
        $stmt = $db->getPDO()->prepare("CALL consultarreportescliente(?)");
        $stmt->execute([$nombre_cliente]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
        return [];
    }
}

$db = new database();
$db->conectarDB();
$user = $_SESSION["nom_usuario"];

try {
    $stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
    $stmt->execute([$user]);
    $roles = $stmt->fetchAll(PDO::FETCH_OBJ);

    $isAdmin = false;

    foreach ($roles as $role) {
        if ($role->nombre_rol == 'administrador') {
            $isAdmin = true;
            break;
        }
    }

    if ($isAdmin) {
        $_SESSION["nombre_rol"] = 'administrador';
        error_log("Usuario autenticado como Administrador: " . $user);
    } else {
        error_log("Usuario sin privilegios de Administrador. Redirigiendo a iniciarSesion.php");
        header("Location: ../iniciarSesion.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

$reportes = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["nombre_cliente"])) {
    $nombre_cliente = $_POST["nombre_cliente"];
    $reportes = consultarReportesCliente($nombre_cliente);
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
          <a href="#">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#personal" aria-expanded="false" aria-controls="personal">
            <img src="../../img/admin/admin_icon.svg" alt="Personal">
            <span>Personal</span>
          </a>
          <ul id="personal" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_admin_gestionainstalador.php" class="sidebar-link">Registrar</a>
            </li>
            <li class="sidebar-item">
              <a href="./vista_admin_darRol.php" class="sidebar-link">Gestionar</a>
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
              <a href="./vista_admin_citas.php" class="sidebar-link">Gestionar citas</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#cotizaciones" aria-expanded="false" aria-controls="cotizaciones">
            <img src="../../img/admin/clipboard.svg" alt="Cotizaciones">
            <span>Cotizaciones</span>
          </a>
          <ul id="cotizaciones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_admin_cotizacion.php" class="sidebar-link">Ver cotizaciones</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#ventas" aria-expanded="false" aria-controls="ventas">
            <img src="../../img/admin/recibos.svg" alt="Ventas">
            <span>Ventas</span>
          </a>
          <ul id="ventas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
          
          <li class="sidebar-item">
          <a href="./vista_admin_crear_venta.php" class="sidebar-link" >Nueva</a>
          </li>
          <li class="sidebar-item">
          <a href="./vista_admin_ventas.php" class="sidebar-link">Pendientes</a>
          </li>
          <li class="sidebar-item">
          <a href="../recibos.php" class="sidebar-link">Historial</a>
          </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#recibos" aria-expanded="false" aria-controls="recibos">
            <img src="../../img/admin/recibos.svg" alt="Recibos">
            <span>Recibos</span>
          </a>
          <ul id="recibos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./recibos.php" class="sidebar-link">Buscar recibos</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#productos" aria-expanded="false" aria-controls="productos">
            <img src="../../img/admin/products.svg" alt="Productos">
            <span>Productos</span>
          </a>
          <ul id="productos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_admin_productos.php" class="sidebar-link">Gestionar productos</a>
            </li>
            <li class="sidebar-item">
              <a href="./vista_admin_disenos.php" class="sidebar-link">Diseños</a>
            </li>
          </ul>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#promociones" aria-expanded="false" aria-controls="promociones">
            <img src="../../img/admin/off.svg" alt="Promociones">
            <span>Promociones</span>
          </a>
          <ul id="promociones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_admin_promos.php" class="sidebar-link">Añadir</a>
            </li>
          </ul>
        </li>
      </ul>
      <div class="sidebar-footer">
        <a href="./vista_admin.php" class="sidebar-link">
          <img src="../../img/admin/home.svg" alt="Volver"><!--PONER UNA IMAGEN COMO DE VOLVER-->
          <span>Volver</span>
        </a>
      </div>
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
      </div><br>

    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#buscarCitaModal">
        Buscar Cita
      </button>

      <!-- Modal para buscar citas -->
      <div class="modal fade" id="buscarCitaModal" tabindex="-1" aria-labelledby="buscarCitaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="buscarCitaModalLabel">Buscar Cita por Cliente</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="post" action="">
                <div class="mb-3">
                  <label for="nombre_cliente" class="form-label">Ingrese el nombre completo del cliente</label>
                  <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resultados fuera del modal en formato acordeón -->
      <div class="accordion mt-5" id="resultadosAccordion">
        <?php if (!empty($reportes)): ?>
          <?php foreach ($reportes as $index => $reporte): ?>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading<?= $index ?>">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="true" aria-controls="collapse<?= $index ?>">
                  ID Cita: <?= htmlspecialchars($reporte->id_cita) ?> - ID Reporte: <?= htmlspecialchars($reporte->id_reporte) ?>
                </button>
              </h2>
              <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#resultadosAccordion">
                <div class="accordion-body">
                  <strong>Nombre Producto:</strong> <?= htmlspecialchars($reporte->nombre_producto) ?><br>
                  <strong>Monto:</strong> <?= htmlspecialchars($reporte->monto) ?><br>
                  <strong>Detalles del Reporte:</strong> <?= htmlspecialchars($reporte->detalles_reporte) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
          <p class="mt-3">No se encontraron reportes para el cliente ingresado.</p>
        <?php endif; ?>
      </div>

      <!-- Formulario para crear venta -->
      <div class="container mt-5">
        <h3>Crear Venta</h3>
        <form method="POST" action="../../scripts/administrador/crear_venta.php">
          <div class="mb-3">
            <label for="nombre_cliente_venta" class="form-label">Nombre completo del cliente</label>
            <input type="text" class="form-control" id="nombre_cliente_venta" name="nombre_cliente_venta" required>
          </div>
          <div class="mb-3">
            <label for="cita_id" class="form-label">ID de la Cita</label>
            <input type="number" class="form-control" id="cita_id" name="cita_id" required>
          </div>
          <button type="submit" class="btn btn-primary">Crear Venta</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    const hamBurger = document.querySelector(".toggle-btn");

    hamBurger.addEventListener("click", function () {
      document.querySelector("#sidebar").classList.toggle("expand");
    });
  </script>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
