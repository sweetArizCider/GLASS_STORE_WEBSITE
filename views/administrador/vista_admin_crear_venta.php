
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
        $stmt = $db->getPDO()->prepare("CALL ConsultarReportesCliente(?)");
        $stmt->execute([$nombre_cliente]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
        return [];
    }
}

function obtenerPromocionesActivas() {
    $db = new database();
    $db->conectarDB();
    
    try {
        $stmt = $db->getPDO()->prepare("SELECT id_promocion, nombre_promocion FROM promociones WHERE estatus = 'activo'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        error_log("Error al obtener promociones: " . $e->getMessage());
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
$promociones = obtenerPromocionesActivas();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["nombre_cliente"])) {
    $nombre_cliente = $_POST["nombre_cliente"];
    $reportes = consultarReportesCliente($nombre_cliente);

    // Procesar los reportes para moverlos a detalle_venta
    foreach ($reportes as $reporte) {
        // Verificar si el reporte ya está vinculado a una venta
        $stmt = $db->getPDO()->prepare("SELECT COUNT(*) FROM detalle_venta WHERE reporte = ?");
        $stmt->execute([$reporte->id_reporte]);
        $reporte_vinculado = $stmt->fetchColumn();

        if ($reporte->estatus == 'aceptada' && $reporte_vinculado == 0) {
            // Insertar en detalle_venta si no está vinculado
            $stmt = $db->getPDO()->prepare("INSERT INTO detalle_venta (reporte, venta, subtotal) VALUES (?, ?, ?)");
            $stmt->execute([$reporte->id_reporte, $reporte->id_venta, $reporte->monto]);

            // Actualizar el estatus del reporte a 'finalizado'
            $stmt = $db->getPDO()->prepare("UPDATE reporte SET estatus = 'finalizado' WHERE id_reporte = ?");
            $stmt->execute([$reporte->id_reporte]);
        }
    }
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
  <style>
    .button-buscar-cita{
      background: #4AB3D5;
      border: 1.5px solid #4AB3D5;
      border-radius: 30px;
      font-family: Inter;
      font-size: .9em;
      font-weight: 400;
      color: #fff;
      cursor: pointer;
      padding: 8px 18px;
      text-decoration: none;
    }
    .button-buscar-cita-cerrar{
      background: #c82333;
      border: 1.5px solid #c82333;
      border-radius: 30px;
      font-family: Inter;
      font-size: .9em;
      font-weight: 400;
      color: #fff;
      cursor: pointer;
      padding: 8px 18px;
      text-decoration: none;
    }
    .button-buscar-cita-crear{
      background: #132644;
      border: 1.5px solid #132644;
      border-radius: 30px;
      font-family: Inter;
      font-size: .9em;
      font-weight: 400;
      color: #fff;
      cursor: pointer;
      padding: 8px 18px;
      text-decoration: none;
    }
  </style>
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
        <!-- Menú de navegación omitido para brevedad -->
      </ul>
      <div class="sidebar-footer">
        <a href="./vista_admin.php" class="sidebar-link">
          <img src="../../img/admin/home.svg" alt="Volver">
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
      <!-- Botón para abrir el modal -->
      <button type="button"  data-bs-toggle="modal" data-bs-target="#buscarCitaModal" class="button-buscar-cita">
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
                <button type="submit"  class="button-buscar-cita">Buscar</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" data-bs-dismiss="modal" class="button-buscar-cita-cerrar">Cerrar</button>
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
          <div class="mb-3">
            <label for="extras" class="form-label">Extras</label>
            <input type="number" class="form-control" id="extras" name="extras" step="0.01">
          </div>
          <div class="mb-3">
            <label for="promocion_id" class="form-label">Promoción Aplicada</label>
            <select class="form-control" id="promocion_id" name="promocion_id">
                <?php foreach ($promociones as $promocion): ?>
                    <option value="<?= $promocion->id_promocion ?>"><?= htmlspecialchars($promocion->nombre_promocion) ?></option>
                <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class=" button-buscar-cita-crear">Crear Venta</button>
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
