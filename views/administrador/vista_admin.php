<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

include '../../class/database.php';

try {
    $db = new database();
    $db->conectarDB();
    $user = $_SESSION["nom_usuario"];

    // Obtener el nombre completo del usuario desde la tabla PERSONA
    $stmt = $db->getPDO()->prepare("
        SELECT p.nombres, p.apellido_p, p.apellido_m 
        FROM persona p
        JOIN usuarios u ON p.usuario = u.id_usuario
        WHERE u.nom_usuario = ?
    ");
    $stmt->execute([$user]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nombreCompleto = htmlspecialchars($result['nombres'] . ' ' . $result['apellido_p']);

    // Procedimiento almacenado para obtener los roles del usuario
    $stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
    $stmt->execute([$user]);
    $roles = $stmt->fetchAll(PDO::FETCH_OBJ);

    $isAdmin = false;

    // Verificar cada rol devuelto
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
        // Si el usuario no es administrador, redirigir a la página de inicio de sesión
        error_log("Usuario sin privilegios de Administrador. Redirigiendo a iniciarSesion.php");
        header("Location: ../iniciarSesion.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

try {
    $stmt = $db->getPDO()->prepare("CALL obtener_valor_referencia_ventas_mes_pasado(@valor_referencia)");
    $stmt->execute();
    
    $stmt = $db->getPDO()->prepare("SELECT @valor_referencia AS valor_referencia");
    $stmt->execute();
    $resultReferencia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $valorReferencia = isset($resultReferencia['valor_referencia']) ? $resultReferencia['valor_referencia'] : 0;
    $stmt = $db->getPDO()->prepare("CALL obtener_suma_ventas_concluidas_por_mes(@suma_total_ventas)");
    $stmt->execute();
    
    $stmt = $db->getPDO()->prepare("SELECT @suma_total_ventas AS suma_total_ventas");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $sumaTotalVentas = isset($result['suma_total_ventas']) ? $result['suma_total_ventas'] : 0;
    
    $cambio = $sumaTotalVentas - $valorReferencia;
} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado para ventas: " . $e->getMessage());
    $sumaTotalVentas = 0; 
    $valorReferencia = 0; 
    $cambio = 0;
}
try {
  
  $stmt = $db->getPDO()->prepare("CALL producto_mas_vendido()");
  $stmt->execute();
  $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  $productoMasVendido = isset($productos[0]) ? $productos[0] : [];
  $productoMenosVendidos = array_slice($productos, 1);
} catch (Exception $e) {
  error_log("Error al ejecutar el procedimiento almacenado para productos: " . $e->getMessage());
  $productoMasVendido = [];
  $productoMenosVendidos = [];
}
try {
  $stmt = $db->getPDO()->prepare("CALL calcularpromediototalventassaldocero()");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
  $promedioTotalVentasSaldoCero = isset($result['PromedioTotalVentasSaldoCero']) ? $result['PromedioTotalVentasSaldoCero'] : 0;
} catch (Exception $e) {
  error_log("Error al ejecutar el procedimiento almacenado para promedio de ventas: " . $e->getMessage());
  $promedioTotalVentasSaldoCero = 0; 
}
try {
  $stmt = $db->getPDO()->prepare("CALL calcularmaxmintotalventassaldocero()");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
  $maxTotalVentasSaldoCero = isset($result['MaxTotalVentasSaldoCero']) ? $result['MaxTotalVentasSaldoCero'] : 0;
  $minTotalVentasSaldoCero = isset($result['MinTotalVentasSaldoCero']) ? $result['MinTotalVentasSaldoCero'] : 0;
} catch (Exception $e) {
  error_log("Error al ejecutar el procedimiento almacenado para máximo y mínimo de ventas: " . $e->getMessage());
  $maxTotalVentasSaldoCero = 0; 
  $minTotalVentasSaldoCero = 0; 
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
    .card-custom {
      border-radius: 0.75rem;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border: 1px solid #ddd;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .card-custom:hover {
      border-color: #007bff;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }
    .card-custom .card-body {
      padding: 1.5rem;
    }
    .card-custom .card-title {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }
    .card-custom .card-text {
      font-size: 1.25rem;
    }
    .card-custom .indicator {
      display: flex;
      align-items: center;
      font-size: 1rem;
    }
    .indicator i {
      margin-right: 0.5rem;
    }
    .indicator.up {
      color: #28a745;
    }
    .indicator.down {
      color: #dc3545;
    }
    .main-content {
      margin-top: 20px;
    }
    .product-card {
      display: flex;
      flex-direction: column;
      height: 100%;
      text-align: center;
    }
    .product-card img {
      max-width: 100%;
      height: auto; /* Ajusta la altura automáticamente para mantener la proporción */
      max-height: 150px; /* Limita la altura máxima de las imágenes */
    }
    .product-card-title {
      font-size: 1.25rem;
    }
    .welcome-card {
      background-color: #007bff;
      color: white;
      border-radius: 0.75rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
    }
    .welcome-card h2 {
      font-size: 2rem;
      margin-bottom: 10px;
    }
    .welcome-card p {
      font-size: 1.25rem;
    }
    .extra-message {
      margin-top: 20px;
      font-size: 1.1rem;
      font-style: italic;
      text-align: center;
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
          <a href="../../index.php">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
      <div class="sidebar-itemr">
        <a href="./vista_admin.php" class="sidebar-link">
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
             data-bs-target="#ventas" aria-expanded="false" aria-controls="ventas">
            <img src="../../img/admin/recibos.svg" alt="Ventas">
            <span>Ventas</span>
          </a>
          <ul id="ventas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
          
          <li class="sidebar-item">
          <a href="./vista_admin_crear_venta.php" class="sidebar-link" >Crear venta</a>
          </li>
          <li class="sidebar-item">
          <a href="./vista_admin_ventas.php" class="sidebar-link">Gestionar ventas</a>
          </li>
          <li class="sidebar-item">
          <a href="../recibos.php" class="sidebar-link">Historial</a>
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

    <div class="main p-3">
      <div class="general-container">
      <!-- Mensaje de bienvenida -->
      <div class="">
      <div class="text-center ">
        <h2 class="mensaje-bienvenida">¡Bienvenido, <?php echo $nombreCompleto; ?>!</h2>
        <p>Esperamos que te encuentres bien el día de hoy. Aquí tienes un resumen de tus estadísticas recientes.</p>
      </div>
      </div>

      <!-- Tarjetas de estadísticas de ventas -->
      <div class="container main-content">
        <div class="row">
          <div class="col-md-4 col-lg-3 mb-3">
            <div class="card card-custom">
              <div class="card-body">
                <h5 class="card-title">Total Vendido Este Mes</h5>
                <p class="card-text">$<?php echo number_format($sumaTotalVentas, 2); ?></p>
                <div class="indicator <?php echo $cambio >= 0 ? 'up' : 'down'; ?>">
                  <i class="bi <?php echo $cambio >= 0 ? 'bi-arrow-up-circle' : 'bi-arrow-down-circle'; ?>"></i>
                  <?php echo number_format(abs($cambio), 2); ?> <?php echo $cambio >= 0 ? '↑' : '↓'; ?>
                </div>
              </div>
            </div>
          </div>

           <!-- Tarjeta del promedio de ventas con saldo cero -->
           <div class="col-md-4 col-lg-3 mb-3">
            <div class="card card-custom">
              <div class="card-body">
                <h5 class="card-title">Promedio Ventas en este mes</h5>
                <p class="card-text">$<?php echo number_format($promedioTotalVentasSaldoCero, 2); ?></p>
              </div>
            </div>
          </div>

          <!-- Tarjeta para el máximo y mínimo total de ventas con saldo cero -->
      <div class="col-md-4 mb-3">
        <div class="card card-custom">
          <div class="card-body">
            <h5 class="card-title">Mayor y menor venta este mes</h5>
            <p class="card-text">Mayor: $<?php echo number_format($maxTotalVentasSaldoCero, 2); ?></p>
            <p class="card-text">Menor: $<?php echo number_format($minTotalVentasSaldoCero, 2); ?></p>
          </div>
        </div>
      </div>
        </div>
      </div>

      <!-- Mensaje extra -->
      <div class="extra-message">
        <p>Recuerda, cada pequeño esfuerzo suma a grandes logros. ¡Gracias por tu dedicación!</p>
      </div>

      <!-- Área de búsqueda y productos más vendidos -->
      <div class="text-center my-4">
        <h2>Productos Más Vendidos</h2>
      </div>
      
      <div class="container main-content">
        <div class="row">
          <!-- Producto más vendido -->
          <div class="col-md-4 mb-3">
            <div class="card card-custom product-card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($productoMasVendido['nombre'] ?? ''); ?></h5>
                <img src="../../img/productos/<?php echo htmlspecialchars($productoMasVendido['imagen'] ?? ''); ?>" alt="<?php echo htmlspecialchars($productoMasVendido['imagen'] ?? ''); ?>" class="img-fluid">
                <p class="card-text">Cantidad Vendida: <?php echo htmlspecialchars($productoMasVendido['cantidad_vendida'] ?? 0); ?></p>
              </div>
            </div>
          </div>

          <!-- Productos menos vendidos -->
          <?php foreach ($productoMenosVendidos as $producto): ?>
            <div class="col-md-4 mb-3">
              <div class="card card-custom product-card">
                <div class="card-body">
                  <h6 class="card-title product-card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h6>
                  <img src="../../img/productos/<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>" alt="<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>" class="img-fluid">
                  <p class="card-text">Cantidad Vendida: <?php echo htmlspecialchars($producto['cantidad_vendida']); ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
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
