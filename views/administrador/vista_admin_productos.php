<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../iniciarSesion.php");
    exit();
}

include '../../class/database.php';

$db = new database();
$db->conectarDB();
$db->configurarConexionPorRol();
$user = $_SESSION["nom_usuario"];

// Procedimiento almacenado para obtener el rol del usuario
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
} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

$database = new Database();
$database->conectarDB();

// Obtener las categorías para el filtro
$categorias = $database->seleccionar("SELECT id_categoria, nombre FROM categorias");

// Inicializar la categoría seleccionada
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Preparar la consulta según la categoría seleccionada
if ($categoriaSeleccionada) {
    // Consulta con parámetros para filtrar por categoría
    $query = "CALL filtrarproductospornombrecategoria(:categoria)";
    $productos = $database->seleccionar($query, [':categoria' => $categoriaSeleccionada]);
} else {
    // Consulta sin parámetros para obtener todos los productos
    $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.estatus, p.categoria AS id_categoria, c.nombre AS categoria 
              FROM productos p 
              JOIN categorias c ON p.categoria = c.id_categoria";
    $productos = $database->seleccionar($query);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Glass Store</title>
  <link rel="shortcut icon" href="../../img/index/logoVarianteSmall.png" type="image/x-icon">
  <!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

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
      </div>

      <br>
      <div class="contenidoGeneral mt-4">
        <h1 class="text-center my-4">Gestionar Productos</h1>

        <div class="d-flex justify-content-end mt-4 flex-column flex-md-row">
  <a href="#addProductModal" class="btn btn-primary me-2 mb-2 mb-md-0" data-bs-toggle="modal">Añadir Producto</a>
  <a href="#addCategoryModal" class="btn btn-secondary me-2 mb-2 mb-md-0" data-bs-toggle="modal">Añadir Categoría</a>
  <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      Filtrar por Categoría
    </button>
    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
      <li><a class="dropdown-item" href="?categoria=">Todas las Categorías</a></li>
      <?php foreach ($categorias as $categoria): ?>
      <li><a class="dropdown-item" href="?categoria=<?php echo urlencode($categoria->nombre); ?>"><?php echo htmlspecialchars($categoria->nombre); ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Estatus</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
  <?php if (is_array($productos) && !empty($productos)): ?>
    <?php foreach ($productos as $producto): ?>
    <tr>
      <td><?php echo htmlspecialchars($producto->nombre); ?></td>
      <td><?php echo htmlspecialchars($producto->categoria); ?></td>
      <td><?php echo htmlspecialchars($producto->descripcion); ?></td>
      <td><?php echo htmlspecialchars($producto->precio); ?></td>
      <td><?php echo htmlspecialchars($producto->estatus); ?></td>
      <td>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal-<?php echo $producto->id_producto; ?>">Editar</button>
      </td>
    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editProductModal-<?php echo $producto->id_producto; ?>" tabindex="-1" aria-labelledby="editProductModalLabel-<?php echo $producto->id_producto; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editProductModalLabel-<?php echo $producto->id_producto; ?>">Editar Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="../../scripts/administrador/editaProducto.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id_producto" value="<?php echo $producto->id_producto; ?>">
              <div class="mb-3">
                <label for="edit-product-nombre-<?php echo $producto->id_producto; ?>" class="form-label">Nombre Actual</label>
                <input type="text" class="form-control" name="nombre_actual" id="edit-product-nombre-<?php echo $producto->id_producto; ?>" value="<?php echo htmlspecialchars($producto->nombre); ?>" readonly>
              </div>
              <div class="mb-3">
                <label for="edit-product-new-name-<?php echo $producto->id_producto; ?>" class="form-label">Nuevo Nombre</label>
                <input type="text" class="form-control" name="nuevo_nombre" id="edit-product-new-name-<?php echo $producto->id_producto; ?>" value="<?php echo htmlspecialchars($producto->nombre); ?>">
              </div>
              <div class="mb-3">
                <label for="edit-product-categoria-<?php echo $producto->id_producto; ?>" class="form-label">Categoría</label>
                <select class="form-control" name="categoria" id="edit-product-categoria-<?php echo $producto->id_producto; ?>">
                  <?php foreach ($categorias as $categoria): ?>
                  <option value="<?php echo $categoria->nombre; ?>" <?php echo $producto->nombre == $categoria->nombre ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoria->nombre); ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="edit-product-descripcion-<?php echo $producto->id_producto; ?>" class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" id="edit-product-descripcion-<?php echo $producto->id_producto; ?>"><?php echo htmlspecialchars($producto->descripcion); ?></textarea>
              </div>
              <div class="mb-3">
                <label for="edit-product-precio-<?php echo $producto->id_producto; ?>" class="form-label">Precio</label>
                <input type="text" class="form-control" name="precio" id="edit-product-precio-<?php echo $producto->id_producto; ?>" value="<?php echo htmlspecialchars($producto->precio); ?>">
              </div>
              <div class="mb-3">
                <label for="edit-product-estatus-<?php echo $producto->id_producto; ?>" class="form-label">Estatus</label>
                <select class="form-control" name="estatus" id="edit-product-estatus-<?php echo $producto->id_producto; ?>">
                  <option value="activo" <?php echo $producto->estatus == 'activo' ? 'selected' : ''; ?>>Activo</option>
                  <option value="inactivo" <?php echo $producto->estatus == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
              </div>
              <div class="mb-3">
            <label for="edit-product-image-<?php echo $producto->id_producto; ?>" class="form-label">Imagen del Producto</label>
            <input type="file" class="form-control" name="fileToUpload" id="edit-product-image-<?php echo $producto->id_producto; ?>">
          </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Descartar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="6" class="text-center">No se encontraron productos</td>
    </tr>
  <?php endif; ?>
</tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de añadir producto -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Añadir Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../scripts/administrador/agregarProducto.php" method="POST">
            <div class="mb-3">
              <label for="add-product-nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="add-product-nombre">
            </div>
            <div class="mb-3">
              <label for="add-product-categoria" class="form-label">Categoría</label>
              <select class="form-control" name="categoria" id="add-product-categoria">
                <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria->nombre; ?>"><?php echo htmlspecialchars($categoria->nombre); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="add-product-descripcion" class="form-label">Descripción</label>
              <textarea class="form-control" name="descripcion" id="add-product-descripcion"></textarea>
            </div>
            <div class="mb-3">
              <label for="add-product-precio" class="form-label">Precio</label>
              <input type="text" class="form-control" name="precio" id="add-product-precio">
            </div>
            <div class="mb-3">
              <label for="add-product-estatus" class="form-label">Estatus</label>
              <select class="form-control" name="estatus" id="add-product-estatus">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Añadir Producto</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de añadir categoría -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">Añadir Categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../scripts/administrador/crearCategoria.php" method="POST">
            <div class="mb-3">
              <label for="add-category-nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="add-category-nombre">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Añadir Categoría</button>
            </div>
          </form>
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
