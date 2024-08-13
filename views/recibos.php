<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php"); // Redirige a la página de inicio de sesión si no está autenticado
    exit();
}

require_once '../class/database.php';

$db = new Database();
$db->conectarDB();
$user = $_SESSION["nom_usuario"];

// Procedimiento almacenado para obtener el rol del usuario
$stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
$stmt->execute([$user]);
$result = $stmt->fetch(PDO::FETCH_OBJ);

// Asegúrate de cerrar el cursor para liberar el conjunto de resultados
$stmt->closeCursor();

if ($result && ($result->nombre_rol == 'administrador' || $result->nombre_rol == 'cliente')) { 
    $_SESSION["nombre_rol"] = $result->nombre_rol;
    error_log("Usuario autenticado como " . $result->nombre_rol . ": " . $user);
} else {
    error_log("Usuario sin privilegios de Administrador o Cliente. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

// Filtrar las ventas con saldo 0
$query = "SELECT * FROM ventas WHERE saldo = 0";
$resultados = $db->seleccionar($query);

$db->desconectarDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Glass Store</title>
  <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/normalized.css">
  <link rel="stylesheet" href="../css/style_admin.css">
  <style>
        .hidden {
            display: none;
        }
        .accordion-button:not(.collapsed) {
            color: #0d6efd;
            background-color: #e7f1ff;
        }
        .accordion-item {
            margin-bottom: 10px;
        }
    </style>

</head>
<body>
  <!--Logo flotante del negocio-->
  <div id="logotipo-flotante">
    <img src="../img/index/GLASS.png" alt="Glass store">
  </div>

  <!--Barra lateral-->
  <!--Barra lateral-->
  <div class="wrapper">
    <aside id="sidebar">
      <div class="d-flex">
        <button class="toggle-btn" type="button">
          <img src="../img/index/menu.svg" alt="Menu">
        </button>
        <div class="sidebar-logo">
          <a href="#">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
      <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#personal" aria-expanded="false" aria-controls="personal">
            <img src="../img/admin/admin_icon.svg" alt="Personal">
            <span>Personal</span>
          </a>
          <ul id="personal" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_gestionainstalador.php" class="sidebar-link">Registrar</a>
            </li>
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_darRol.php" class="sidebar-link">Gestionar</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#citas" aria-expanded="false" aria-controls="citas">
            <img src="../img/admin/calendar.svg" alt="Citas">
            <span>Citas</span>
          </a>
          <ul id="citas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_citas.php" class="sidebar-link">Gestionar citas</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#cotizaciones" aria-expanded="false" aria-controls="cotizaciones">
            <img src="../img/admin/clipboard.svg" alt="Cotizaciones">
            <span>Cotizaciones</span>
          </a>
          <ul id="cotizaciones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_cotizacion.php" class="sidebar-link">Ver cotizaciones</a>
            </li>

            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_reporte.php" class="sidebar-link">Ver reportes</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#ventas" aria-expanded="false" aria-controls="ventas">
            <img src="../img/admin/recibos.svg" alt="Ventas">
            <span>Ventas</span>
          </a>
          <ul id="ventas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
          
          <li class="sidebar-item">
          <a href="../views/administrador/vista_admin_crear_venta.php" class="sidebar-link" >Crear venta</a>
          </li>
          <li class="sidebar-item">
          <a href="../views/administrador/vista_admin_ventas.php" class="sidebar-link">Gestionar ventas</a>
          </li>
          <li class="sidebar-item">
          <a href="./recibos.php" class="sidebar-link">Historial</a>
          </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#productos" aria-expanded="false" aria-controls="productos">
            <img src="../img/admin/products.svg" alt="Productos">
            <span>Productos</span>
          </a>
          <ul id="productos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_productos.php" class="sidebar-link">Gestionar productos</a>
            </li>
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_disenos.php" class="sidebar-link">Diseños</a>
            </li>
          </ul>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
            data-bs-target="#promociones" aria-expanded="false" aria-controls="promociones">
            <img src="../img/admin/off.svg" alt="Promociones">
            <span>Promociones</span>
          </a>
          <ul id="promociones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../views/administrador/vista_admin_promos.php" class="sidebar-link">Añadir</a>
            </li>
          </ul>
        </li>
        <div class="sidebar-itemr">
        <a href="../index.php" class="sidebar-link">
          <img src="../img/index/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-item">
        <a href="../scripts/cerrarSesion.php" class="sidebar-link">
        <img src="../img/admin/logout.svg" alt="Cerrar Sesión">
        <span>Cerrar Sesión</span>
        </a>
    </div>
      </ul>
      
    </aside>

    <div class="main p-3">
      <div class="text-center">
        <div class="busqueda mx-auto">
          <input type="text" placeholder="Buscar" class="buscar-input" id="search-input">
          <img src="../../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer;">
        </div>
      </div><br>

      <div class="contenidoGeneral mt-4">
        <div class="general-container">

          <div class="container">
            <div id="accordion">
              <?php if ($resultados): ?>
                <?php foreach ($resultados as $index => $fila): ?>
                  <div class="secc-sub-general " style="margin-bottom: 1em;" data-bs-toggle="collapse" data-bs-target="#venta<?php echo $index; ?>">
                    <p style="font-size: .9em;" class="bueld">ID Venta: <?php echo htmlspecialchars($fila->id_venta); ?></p>
                    <p style="margin-top:-.5em; font-size: 1.2em; text-transform: capitalize;"><mark class="marklued">Total: $<?php echo htmlspecialchars($fila->total); ?></mark></p>
                    <div id="venta<?php echo $index; ?>" class="collapse">
                      <div class="detalle">
                        <p><strong>Fecha Venta:</strong> <?php echo htmlspecialchars($fila->fecha); ?></p>
                        <p><strong>Saldo:</strong> $<?php echo htmlspecialchars($fila->saldo); ?></p>
                        <p><strong>Subtotal:</strong> $<?php echo htmlspecialchars($fila->subtotal); ?></p>
                        <p><strong>Total Promoción:</strong> $<?php echo htmlspecialchars($fila->total_promocion); ?></p>
                        <p><strong>Extras:</strong> $<?php echo htmlspecialchars($fila->extras); ?></p>
                    
                        
                      </div>
                    </div>
                  </div><br>

                  
                <?php endforeach; ?>
              <?php else: ?>
                <p>No se encontraron resultados.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
