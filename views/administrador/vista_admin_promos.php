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
    .promo-status {
        border: none;
        border-radius: 4px;
        padding: 10px 20px;
        cursor: pointer;
        text-align: center;
    }

    .promo-status.activo {
        color: green;
    }

    .promo-status.inactivo {
        color: red;
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



    <div class="main p-3">
    <div class="col-12 mb-4 card-bienvenida">
          <div class="text-center ">
            <div class="">
              <h5 class=" mensaje-bienvenida">Promociones</h5>
            </div>
          </div>
        </div>
      <div class="text-center">
        <div class="busqueda mx-auto">
          <input type="text" placeholder="Buscar" class="buscar-input" id="search-input">
          <img src="../../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer;">
        </div>
      </div>
      <br>

     
    
  <!-- contenido general-->
  <div class="contenidoGeneral mt-4">
      <div class="general-container">
        <div class="d-flex justify-content-end mt-4">
        <button class="btn addButton" type="button" data-bs-toggle="modal" data-bs-target="#modalNuevaPromocion"><img src="../../img/admin/add.svg" alt="Añadir" class="icono-circulo">
            </button>
        </div>
        
        <!--aqui van las promos-->
        <?php
            require_once '../../class/database.php'; 

            $database = new Database();
            $database->conectarDB();

            $query = "SELECT id_promocion,nombre_promocion, valor, estatus FROM promociones";
            $promociones = $database->seleccionar($query);

            $database->desconectarDB();
        ?>

        <!-- Mostrar las promociones -->
        <div id="promos-container" class="row">
            <?php if (!empty($promociones)): ?>
                <?php foreach ($promociones as $promo): ?>
                    <div class="col-md-4 promo-item">
                        <div class="promo-card">
                            <div class="promo-name"><?php echo htmlspecialchars($promo->nombre_promocion); ?></div>
                            <div class="promo-value">Valor: <?php echo htmlspecialchars($promo->valor); ?></div>
                            
                            <!-- Formulario para cambiar el estatus -->
                            <form method="post" action="../../scripts/administrador/cambiar_estatus_promo.php" style="display: inline;">
                                <input type="hidden" name="id_promocion" value="<?php echo htmlspecialchars($promo->id_promocion); ?>">
                                <input type="hidden" name="estatus_actual" value="<?php echo htmlspecialchars($promo->estatus); ?>">
                                <button type="submit" name="cambiar_estatus" value="cambiar_estatus" class="promo-status <?php echo htmlspecialchars($promo->estatus); ?>">
                                    <?php echo htmlspecialchars($promo->estatus); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay promociones disponibles.</p>
            <?php endif; ?>
        </div>

       <!-- Modal agregar nueva promoción -->
       <div class="modal fade" id="modalNuevaPromocion" tabindex="-1" aria-labelledby="modalNuevaPromocionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNuevaPromocionLabel">Nueva Promoción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formNuevaPromocion" method="post" action="../../scripts/administrador/crearPromocion.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombrePromocion" class="form-label">Nombre de la Promoción</label>
                                <input type="text" class="form-control" id="nombrePromocion" name="nombre_promocion" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipoPromocion" class="form-label">Tipo de Promoción</label>
                                <select class="form-control" id="tipoPromocion" name="tipo_promocion" required>
                                    <option value="cantidad">Fijo</option>
                                    <option value="porcentual">Porcentual</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="valorPromocion" class="form-label">Valor</label>
                                <input type="number" step="0.01" class="form-control" id="valorPromocion" name="valor" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
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

    $(document).ready(function(){
        $("#search-input").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#promos-container .promo-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>
</html>
