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
  <style>
    .button-buscar-abono{
      background: #132644;
  border: 1.5px solid #132644;
  border-radius: 30px;
  font-family: Inter;
  font-size: .9em;
  font-weight: 400;
  color: #fff;
  cursor: pointer;
  padding: 8px 23px;
  text-decoration: none;
    }
    .button-buscar-abono-cerrar{
      background: #c82333;
  border: 1.5px solid #c82333;
  border-radius: 30px;
  font-family: Inter;
  font-size: .9em;
  font-weight: 400;
  color: #fff;
  cursor: pointer;
  padding: 8px 23px;
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
                            <p><strong>Total Abonos:</strong> $<?php echo htmlspecialchars($fila->total_abonos); ?></p>
                            <p><strong>Fecha Último Abono:</strong> <?php echo htmlspecialchars($fila->fecha_ultimo_abono); ?></p>
                            <button class="button-buscar-abono" data-bs-toggle="modal" data-bs-target="#modalAbonos<?php echo $index; ?>">Abonar</button>
                                        

                                    </div>
                                </div>
                            </div><br>

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
                                            <button type="button" class="button-buscar-abono-cerrar" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="button-buscar-abono" form="formAbonos<?php echo $index; ?>">Aceptar</button>
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

  <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
