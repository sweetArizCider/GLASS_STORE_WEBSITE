<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

include '../../class/database.php';

$db = new database();
$db->conectarDB();
$user = $_SESSION["nom_usuario"];

// Procedimiento almacenado para obtener los roles del usuario
try {
    $stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
    $stmt->execute([$user]);
    $roles = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt->closeCursor(); // Cerrar el cursor para liberar los resultados

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

    // Consultar la vista VentasConAbonos
    $query = "SELECT * FROM ventasconabonos";
    $resultados = $db->seleccionar($query);

} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado o consulta: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

// Consultar las promociones activas
try {
    $query = "SELECT id_promocion, nombre_promocion FROM promociones WHERE estatus = 'activo'";
    $promociones = $db->seleccionar($query);
} catch (Exception $e) {
    error_log("Error al ejecutar la consulta de promociones: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

$db->desconectarDB();
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
        <div class="sidebar-itemr">
        <a href="../../index.php" class="sidebar-link">
          <img src="../../img/admin/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-item">
        <a href="../../scripts/cerrarSesion.php" class="sidebar-link">
        <img src="../../img/admin/logout.svg" alt="Cerrar Sesión">
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
            <div class="d-flex justify-content-end mt-4">
                <!-- Filtros u otros elementos -->
            </div>

            <div class="ventas-container">
                <div class="accordion" id="recibosAccordion">
                    <?php if ($resultados): ?>
                        <?php foreach ($resultados as $index => $fila): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                        <div class="d-flex flex-column">
                                            <span><strong>ID Venta:</strong> <?php echo htmlspecialchars($fila->id_venta); ?></span>
                                            <span><strong>Fecha Venta:</strong> <?php echo htmlspecialchars($fila->fecha); ?></span>
                                            <span><strong>Total:</strong> <?php echo htmlspecialchars($fila->total); ?></span>
                                            <span><strong>Saldo:</strong> <?php echo htmlspecialchars($fila->saldo); ?></span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#recibosAccordion">
                                    <div class="accordion-body">
                                        <p><strong>Subtotal:</strong> <?php echo htmlspecialchars($fila->subtotal); ?></p>
                                        <p><strong>Total Promoción:</strong> <?php echo htmlspecialchars($fila->total_promocion); ?></p>
                                        <p><strong>Extras:</strong> <?php echo htmlspecialchars($fila->extras); ?></p>
                                        <p><strong>Total Abonos:</strong> <?php echo htmlspecialchars($fila->total_abonos); ?></p>
                                        <p><strong>Fecha Último Abono:</strong> <?php echo htmlspecialchars($fila->fecha_ultimo_abono); ?></p>
                                        <button class="btn button-primary" data-bs-toggle="modal" data-bs-target="#modalExtras<?php echo $index; ?>">Añadir extras</button>
                                        <button class="btn button-primary" data-bs-toggle="modal" data-bs-target="#modalAbonos<?php echo $index; ?>">Abonar</button>
                                        <button class="btn button-primary" data-bs-toggle="modal" data-bs-target="#modalPromos<?php echo $index; ?>">Aplicar promoción</button>
                                        <button class="btn button-primary" data-bs-toggle="modal" data-bs-target="#modalQuitarPromos<?php echo $index; ?>">Quitar promoción</button>
                                    </div>
                                </div>
                            </div><br>
                            <!-- Modal para Añadir Extras -->
                            <div class="modal fade" id="modalExtras<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalExtrasLabel<?php echo $index; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalExtrasLabel<?php echo $index; ?>">Añadir Extras</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formExtras<?php echo $index; ?>" action="../../scripts/administrador/agregar_extras.php" method="POST">
                                                <input type="hidden" name="id_venta" value="<?php echo $fila->id_venta; ?>">
                                                <div class="mb-3">
                                                    <label for="inputExtras<?php echo $index; ?>" class="form-label">Cantidad de dinero</label>
                                                    <input type="number" class="form-control" id="inputExtras<?php echo $index; ?>" name="extras" placeholder="Ingrese la cantidad de dinero" step="0.01" required>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary" form="formExtras<?php echo $index; ?>">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para Abonar -->
                            <div class="modal fade" id="modalAbonos<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalAbonosLabel<?php echo $index; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalAbonosLabel<?php echo $index; ?>">Abonar a la Venta</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formAbonos<?php echo $index; ?>" action="../../scripts/administrador/agregar_abono.php" method="POST">
                                                <input type="hidden" name="id_venta" value="<?php echo $fila->id_venta; ?>">
                                                <div class="mb-3">
                                                    <label for="inputAbono<?php echo $index; ?>" class="form-label">Cantidad a Abonar</label>
                                                    <input type="number" class="form-control" id="inputAbono<?php echo $index; ?>" name="abono" placeholder="Ingrese la cantidad a abonar" step="0.01" required>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary" form="formAbonos<?php echo $index; ?>">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para Aplicar Promociones -->
                            <div class="modal fade" id="modalPromos<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalPromosLabel<?php echo $index; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalPromosLabel<?php echo $index; ?>">Aplicar Promociones</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formPromos<?php echo $index; ?>" action="../../scripts/administrador/agregar_promocion.php" method="POST">
                                                <input type="hidden" name="id_venta" value="<?php echo $fila->id_venta; ?>">
                                                <div class="mb-3">
                                                    <label for="selectPromocion<?php echo $index; ?>" class="form-label">Seleccione la promoción a aplicar</label>
                                                    <select class="form-select" id="selectPromocion<?php echo $index; ?>" name="promocion" required>
                                                        <?php foreach ($promociones as $promocion): ?>
                                                            <option value="<?php echo htmlspecialchars($promocion->nombre_promocion); ?>"><?php echo htmlspecialchars($promocion->nombre_promocion); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary" form="formPromos<?php echo $index; ?>">Aplicar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para Quitar Promociones -->
<div class="modal fade" id="modalQuitarPromos<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalQuitarPromosLabel<?php echo $index; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQuitarPromosLabel<?php echo $index; ?>">Quitar Promociones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formQuitarPromos<?php echo $index; ?>" action="../../scripts/administrador/quitar_promocion.php" method="POST">
                    <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($fila->id_venta); ?>">
                    <div class="mb-3">
                        <label for="selectQuitarPromocion<?php echo $index; ?>" class="form-label">Seleccione la promoción a quitar</label>
                        <select class="form-select" id="selectQuitarPromocion<?php echo $index; ?>" name="nombre_promocion" required>
                            <?php foreach ($promociones as $promocion): ?>
                                <option value="<?php echo htmlspecialchars($promocion->nombre_promocion); ?>">
                                    <?php echo htmlspecialchars($promocion->nombre_promocion); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" form="formQuitarPromos<?php echo $index; ?>">Quitar</button>
            </div>
        </div>
    </div>
</div>

                            
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
