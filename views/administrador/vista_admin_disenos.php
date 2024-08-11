<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestionar diseños</title>
  <link rel="shortcut icon" href="../../img/index/logoVarianteSmall.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../css/normalized.css">
  <link rel="stylesheet" href="../../css/style_admin.css">

  <style>
    .clickable-row {
    cursor: pointer;
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
        <div class="sidebar-footer">
        <a href="../../index.php" class="sidebar-link">
          <img src="../../img/index/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-footer">
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
      <div class="container">

<!-- título ----------------------------------------------------->
    <h1 class="text-center my-4">Gestionar Diseños</h1>
</div>

<!-- añadir diseño boton ----------------------------------------------------->
<br>
      <div class="d-flex justify-content-end mb-3">
          <a href="#addDisenoModal" class="btn btn-primary me-2" data-bs-toggle="modal">Añadir Diseño</a>
      </div>

<!-- Modal para añadir diseño --------------------------------------------------->
<div class="modal fade" id="addDisenoModal" tabindex="-1" aria-labelledby="addDisenoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="addDisenoModalLabel">Añadir Diseño</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <form id="addProductForm" action="../../scripts/agregarDiseno.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="add-product-imagen" class="form-label">Imagen</label>
            <input type="file" class="form-control" id="add-product-imagen" name="imagen" accept="image/*" required>
          </div>

          <div class="mb-3">
            <label for="add-product-codigo" class="form-label">Código</label>
            <input type="text" class="form-control" id="add-product-codigo" name="codigo" required>
          </div> 

          <div class="mb-3">
            <label for="add-product-descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="add-product-descripcion" name="descripcion" maxlength="255" required>
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

<!-- Tablas --------------------------------------------------->

<?php
include '../../class/database.php'; 
$conexion = new Database();
$conexion->conectarDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_type'])) {
        if ($_POST['id_type'] == 'diseno_producto') {
            $id_diseno = $_POST['id_diseno'];
            $id_producto = $_POST['id_producto'];
            $nuevo_estatus_DP = $_POST['nuevo_estatus_DP'];

            $consulta_existencia = "
                SELECT COUNT(*) as count 
                FROM disenos_productos
                WHERE diseno = $id_diseno 
                AND producto = $id_producto
            ";
            $resultado_existencia = $conexion->seleccionar($consulta_existencia);

            if ($resultado_existencia[0]->count == 0) {
                $consulta_insercion = "
                    INSERT INTO disenos_productos (diseno, producto, estatus) 
                    VALUES ($id_diseno, $id_producto, '$nuevo_estatus_DP')
                ";
                $conexion->seleccionar($consulta_insercion);
            } else {
                $consulta_actualizacion = "
                    UPDATE disenos_productos
                    SET estatus = '$nuevo_estatus_DP'
                    WHERE diseno = '$id_diseno'
                    AND producto = '$id_producto'
                ";
                $conexion->seleccionar($consulta_actualizacion);
            }
        }
    } elseif (isset($_POST['id_diseno']) && isset($_POST['nuevo_estatus'])) {
        // Actualización de estatus de diseño
        $id_diseno = $_POST['id_diseno'];
        $nuevo_estatus = $_POST['nuevo_estatus'];

        $consulta_actualizacion = "
            UPDATE disenos
            SET estatus = '$nuevo_estatus'
            WHERE id_diseno = $id_diseno
        ";

        $conexion->seleccionar($consulta_actualizacion);
    }
}

$consulta = "
    SELECT
        disenos.id_diseno, disenos.file_path, disenos.codigo, disenos.estatus
    FROM 
        disenos;
";

$menu = $conexion->seleccionar($consulta);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Diseños</title>
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Diseños</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Código</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($menu as $diseno): ?>
                        <tr class='clickable-row' data-id='<?= $diseno->id_diseno ?>'>
                            <td><img src='<?= $diseno->file_path ?>' alt='<?= $diseno->codigo ?>' style='width:100px;height:auto;'></td>
                            <td><?= $diseno->codigo ?></td>
                            <td>
                                <form id='form_<?= $diseno->id_diseno ?>' method='POST' action=''>
                                    <input type='hidden' name='id_diseno' value='<?= $diseno->id_diseno ?>'>
                                    <input type='hidden' name='nuevo_estatus' value='<?= $diseno->estatus == "activo" ? "inactivo" : "activo" ?>'>
                                    <button type='submit' class='btn <?= $diseno->estatus == "activo" ? "btn-success" : "btn-danger" ?> btn-sm mb-2 w-100 status-btn'>
                                        <i class='bi <?= $diseno->estatus == "activo" ? "bi-check" : "bi-x" ?>'></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class='col-md-6'>
                <h3>Tapices</h3>
                <table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Tapiz</th>
                            <th>Descripción</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Obtener ID de diseño de la URL
                        $id_diseno = isset($_GET['id_diseno']) ? (int)$_GET['id_diseno'] : 0;

                        if ($id_diseno > 0) {
                            $consulta_productos = "
                                CALL obtener_diseno_producto($id_diseno);
                            ";
                            $productos = $conexion->seleccionar($consulta_productos);

                            foreach ($productos as $producto):
                        ?>
                        <tr>
                            <td><?= $producto->nombre ?></td>
                            <td><?= $producto->descripcion ?></td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='form_<?= $producto->id_diseno_producto ?>' value='form_<?= $producto->id_diseno_producto ?>'>
                                    <input type='hidden' name='id_type' value='diseno_producto'>
                                    <input type='hidden' name='id_diseno' value='<?= $producto->id_diseno ?>'>
                                    <input type='hidden' name='id_producto' value='<?= $producto->id_producto ?>'>
                                    <input type='hidden' name='nuevo_estatus_DP' value='<?= $producto->estatus == "activo" ? "inactivo" : "activo" ?>'>
                                    <button type='submit' class='btn <?= $producto->estatus == "activo" ? "btn-success" : "btn-danger" ?> btn-sm mb-2 w-100 status-btn'>
                                        <i class='bi <?= $producto->estatus == "activo" ? "bi-check" : "bi-x" ?>'></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                            endforeach;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var rows = document.querySelectorAll('.clickable-row');
        rows.forEach(function(row) {
            row.addEventListener('click', function(event) {
                if (!event.target.classList.contains('status-btn') && !event.target.closest('.status-btn')) {
                    var idDiseno = row.getAttribute('data-id');
                    // Redirigir con el id del diseño en la URL
                    window.location.href = "?id_diseno=" + idDiseno;
                }
            });
        });
    });

    const hamBurger = document.querySelector(".toggle-btn");

    hamBurger.addEventListener("click", function () {
        document.querySelector("#sidebar").classList.toggle("expand");
    });
    </script>
</body>
</html>
