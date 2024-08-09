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
          <!-- aqui vas a estar los reportes -->
          <?php
// Consulta para obtener las citas asociadas al instalador
$citasQuery = $db->getPDO()->prepare("
    SELECT DISTINCT c.id_cita, p.nombres, p.apellido_p, p.apellido_m
    FROM CITAS c
    JOIN CLIENTE_DIRECCIONES cd ON c.cliente_direccion = cd.id_cliente_direcciones
    JOIN CLIENTE cli ON cd.cliente = cli.id_cliente
    JOIN PERSONA p ON cli.persona = p.id_persona
    JOIN INSTALADOR_CITA ic ON c.id_cita = ic.cita
    JOIN DETALLE_CITA dc ON c.id_cita = dc.cita
    WHERE ic.instalador = ? AND dc.estatus = 'en espera'
");

$citasQuery->execute([$result->id_instalador]);
$citas = $citasQuery->fetchAll(PDO::FETCH_OBJ);
?>

<div class="accordion" id="citasAccordion">
    <?php foreach ($citas as $cita): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $cita->id_cita ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $cita->id_cita ?>" aria-expanded="false" aria-controls="collapse<?= $cita->id_cita ?>">
                    Cita ID: <?= $cita->id_cita ?> - Cliente: <?= $cita->nombres ?> <?= $cita->apellido_p ?> <?= $cita->apellido_m ?>
                </button>
            </h2>
            <div id="collapse<?= $cita->id_cita ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $cita->id_cita ?>" data-bs-parent="#citasAccordion">
                <div class="accordion-body">
                    <?php
                    // Consulta para obtener los detalles de la cita
                    $detallesQuery = $db->getPDO()->prepare("
                        SELECT dp.*, p.nombre AS producto_nombre, cat.nombre AS categoria_nombre
                        FROM DETALLE_CITA dc
                        JOIN DETALLE_PRODUCTO dp ON dc.detalle_producto = dp.id_detalle_producto
                        JOIN PRODUCTOS p ON dp.producto = p.id_producto
                        JOIN CATEGORIAS cat ON p.categoria = cat.id_categoria
                        WHERE dc.cita = ?
                    ");
                    $detallesQuery->execute([$cita->id_cita]);
                    $detalles = $detallesQuery->fetchAll(PDO::FETCH_OBJ);
                    ?>
                    <ul class="list-group">
                        <?php foreach ($detalles as $detalle): ?>
                            <li class="list-group-item">
                                <strong>Producto:</strong> <?= $detalle->producto_nombre ?> <br>
                                <strong>Alto:</strong> <?= $detalle->alto ?>, <strong>Largo:</strong> <?= $detalle->largo ?>, <strong>Categoría:</strong> <?= $detalle->categoria_nombre ?>
                                <!-- Botón para activar el modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReporte<?= $detalle->id_detalle_producto ?>">
                                    Generar Reporte
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="modalReporte<?= $detalle->id_detalle_producto ?>" tabindex="-1" aria-labelledby="modalReporteLabel<?= $detalle->id_detalle_producto ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalReporteLabel<?= $detalle->id_detalle_producto ?>">Generar Reporte - <?= $detalle->producto_nombre ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="../../scripts/instalador/generar_reporte.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="detalle_cita" value="<?= $detalle->id_detalle_producto ?>">
                                                    <input type="hidden" name="categoria" value="<?= $detalle->categoria_nombre ?>">
                                                    <div class="mb-3">
    <label for="alto" class="form-label">Alto (metros)</label>
    <input type="number" step="0.01" class="form-control" id="alto" name="alto" value="<?= $detalle->alto ?>" required>
</div>
<div class="mb-3">
    <label for="largo" class="form-label">Largo (metros)</label>
    <input type="number" step="0.01" class="form-control" id="largo" name="largo" value="<?= $detalle->largo ?>" required>
</div>

<div class="mb-3">
    <label for="diseno" class="form-label">Código de Diseño</label>
    <input type="text" class="form-control" id="diseno" name="diseno" required>
</div>
<div class="mb-3">
    <label for="cantidad" class="form-label">Cantidad</label>
    <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" required>
</div>


                                                    <?php if ($detalle->categoria_nombre == 'Vidrios'): ?>
                                                        <div class="mb-3">
                                                            <label for="grosor" class="form-label">Grosor</label>
                                                            <select class="form-control" id="grosor" name="grosor">
                                                                <option value="6">6mm</option>
                                                                <option value="10">10mm</option>
                                                                <option value="12">12mm</option>
                                                            </select>
                                                        </div>
                                                    <?php elseif ($detalle->categoria_nombre == 'Persianas'): ?>
                                                        <!-- Otros campos específicos para Persianas -->
                                                    <?php elseif ($detalle->categoria_nombre == 'Tapiz'): ?>
                                                        <!-- Otros campos específicos para Tapiz -->
                                                    <?php endif; ?>
                                                    <div class="mb-3">
                                                        <label for="extras" class="form-label">Extras</label>
                                                        <input type="number" class="form-control" id="extras" name="extras">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="notas" class="form-label">Notas</label>
                                                        <textarea class="form-control" id="notas" name="notas"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Reporte</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
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