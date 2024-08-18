<?php
session_start();

include '../../class/database.php'; 
$db = new Database();
$db->conectarDB();

$user = $_SESSION["nom_usuario"];

$stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
$stmt->execute([$user]);
$result = $stmt->fetch(PDO::FETCH_OBJ);
$stmt->closeCursor();

if ($result && ($result->nombre_rol == 'administrador' || $result->nombre_rol == 'cliente')) { 
  $_SESSION["nombre_rol"] = $result->nombre_rol;
  error_log("Usuario autenticado como " . $result->nombre_rol . ": " . $user);
} else {
  error_log("Usuario sin privilegios de Administrador o Cliente. Redirigiendo a iniciarSesion.php");
  header("Location: ../iniciarSesion.php");
  exit();
}


$stmt = $db->getPDO()->prepare("SELECT * FROM vista_reportes");
$stmt->execute();
$detalles_citas = $stmt->fetchAll(PDO::FETCH_OBJ);

$citas = [];
foreach ($detalles_citas as $detalle) {
    $citas[$detalle->cita]['nombre_cliente'] = $detalle->nombre_cliente;
    $citas[$detalle->cita]['nombre_instalador'] = $detalle->nombre_instalador;
    $citas[$detalle->cita]['detalles'][] = $detalle;
}

?>

<!DOCTYPE html>
<html lang="es">
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
          <a href="../../index.php">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
      <div class="sidebar-itemr">
        <a href="../../" class="sidebar-link">
          <img src="../../img/index/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
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
            <li class="sidebar-item">
              <a href="./vista_admin_citas_por_instalador.php" class="sidebar-link">Asignaciones</a>
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

            <li class="sidebar-item">
              <a href="./vista_admin_reporte.php" class="sidebar-link">Ver reportes</a>
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
       
      <div class="sidebar-item">
        <a href="../../scripts/cerrarSesion.php" class="sidebar-link">
        <img src="../../img/admin/logout.svg" alt="Cerrar Sesión">
        <span>Cerrar Sesión</span>
        </a>
    </div>
      </ul>
      
    </aside>

    
    <div class="col-12 mb-4 card-bienvenida">
        <div class="text-center">
          <h5 class="mensaje-bienvenida">Reporte de recibos</h5>
        </div>
    
    <div class="container">
      <div class="main p-3">
        <div class="text-center">

          
          <div class="d-flex justify-content-end mt-4">
              <div class="dropdown">
                


              </div>
            </div>
        </div>
        <br><br>

        <!-- Acordeón para detalles de las citas -->
        <?php foreach ($citas as $id_cita => $cita): ?>
    <div class="secc-sub-general" style="margin-bottom:1em;">
        <p><span style="font-size:.8em;">Elaborado por: <span class="bueld"><?php echo htmlspecialchars($cita['nombre_instalador']); ?></span></span></p>
        
        <h2 class="card-custom-header" id="heading<?php echo $id_cita; ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $id_cita; ?>" aria-expanded="false" aria-controls="collapse<?php echo $id_cita; ?>">
                <span class="marklued" style="font-size:1.1em; margin-top:-1em;">
                    <?php echo htmlspecialchars($cita['nombre_cliente']); ?>
                </span>
            </button>
        </h2>
        
        <div id="collapse<?php echo $id_cita; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $id_cita; ?>" data-bs-parent="#accordionExample">
            <div class="card-custom-body">
                <form method="post" action="../../scripts/administrador/procesar_reportes.php">
                    <?php 
                    $subtotal_monto = 0;
                    $subtotal_extras = 0;
                    ?>
                    <?php foreach ($cita['detalles'] as $detalle): ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($detalle->producto); ?></h5>
                        <p class="card-text"><strong>Alto:</strong> <?php echo htmlspecialchars($detalle->alto); ?></p>
                        <p class="card-text"><strong>Largo:</strong> <?php echo htmlspecialchars($detalle->largo); ?></p>
                        <p class="card-text"><strong>Cantidad:</strong> <?php echo htmlspecialchars($detalle->cantidad); ?></p>
                        <p class="card-text"><strong>Monto:</strong> <?php echo htmlspecialchars($detalle->monto); ?></p>
                        <p class="card-text"><strong>Características:</strong> <?php echo htmlspecialchars($detalle->caracteristicas); ?></p>
                        <p class="card-text"><strong>Extras:</strong> <?php echo htmlspecialchars($detalle->extras); ?></p>
                        <p class="card-text"><strong>Notas:</strong> <?php echo htmlspecialchars($detalle->notas); ?></p>

                        <?php 
                        $subtotal_monto += $detalle->monto;
                        $subtotal_extras += $detalle->extras;
                        ?>
                        <hr>
                        <!-- Checkboxes para seleccionar reportes -->
                        
                    <?php endforeach; ?>

                    <!-- Mostrar subtotal -->
                    <p class="card-text"><strong>Total:</strong> <?php echo htmlspecialchars($subtotal_monto + $subtotal_extras); ?></p>

                    <!-- Botón para enviar reportes aceptados en este acordeón -->
                    <input type="hidden" name="id_cita" value="<?php echo htmlspecialchars($id_cita); ?>">

                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>


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
