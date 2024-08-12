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
          <img src="../../img/index/home.svg" alt="Volver">
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
      <div class="col-12 mb-4 card-bienvenida">
          <div class="text-center ">
            <div class="">
              <h5 class="mensaje-bienvenida">Gestión de Diseños</h5>
              <p class="mensaje-sub"><mark class="marklued">¡Hoy es un gran día para crear algo extraordinario!</mark></p>

          </div>
        </div>

      <!-- añadir diseño botón -->
      <br>
      <button class="btn btn-secondary filters" type="button" id="dropdownOrdenar" data-bs-toggle="dropdown" aria-expanded="false">
        Ordenar <img src="../../img/instalador/filter.svg" alt="Filtrar" class="icono-filtro">
      </button>
      <ul class="dropdown-menu" aria-labelledby="dropdownOrdenar">
        <li><a class="dropdown-item" href="#" onclick="sortCitas('activo')">Activos</a></li>
        <li><a class="dropdown-item" href="#" onclick="sortCitas('inactivo')">Inactivos</a></li>
      </ul>
      <div class="d-flex justify-content-end mb-3">
        <a href="#addDisenoModal" class="btn btn-primary me-2" data-bs-toggle="modal">Añadir Diseño</a>
      </div>

      <!-- Modal para añadir diseño -->
      <div class="modal fade" id="addDisenoModal" tabindex="-1" aria-labelledby="addDisenoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addDisenoModalLabel">Añadir Diseño</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="addProductForm" action="../../scripts/administrador/agregarDiseno.php" method="POST" enctype="multipart/form-data">
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

      <!-- Tablas -->
      <?php
      include '../../class/database.php'; 
      $conexion = new Database();
      $conexion->conectarDB();

      $limit = 15;
      $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
      $query = isset($_POST['query']) ? $_POST['query'] : '';
      $sortEstatus = isset($_POST['sortEstatus']) ? $_POST['sortEstatus'] : '';
      $estatusCondition = '';

      if ($sortEstatus === 'activo') {
          $estatusCondition = "disenos.estatus = 'activo'";
      } elseif ($sortEstatus === 'inactivo') {
          $estatusCondition = "disenos.estatus = 'inactivo'";
      }

      // Elimina acentos y convierte a minúsculas para hacer una búsqueda insensible a mayúsculas y acentos
      $consulta = "
          SELECT
              disenos.id_diseno, 
              disenos.file_path, 
              disenos.codigo, 
              disenos.estatus,
              productos.nombre AS producto_nombre
          FROM 
              disenos
          LEFT JOIN 
              productos ON disenos.muestrario = productos.id_producto
          WHERE 
              (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(disenos.codigo, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) LIKE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE('%$query%', 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'))
          OR
              LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(productos.nombre, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) LIKE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE('%$query%', 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')))
      ";

      if ($estatusCondition !== '') {
          $consulta .= " AND $estatusCondition";
      }

      $consulta .= " ORDER BY disenos.estatus = 'activo' DESC, disenos.id_diseno DESC LIMIT $offset, $limit";

      $menu = $conexion->seleccionar($consulta);
      ?>

      <div class="container">
          <div class="row">
            <div class="col-md-2"></div>
              <div class="col-md-8 col-sm-1" >
                  <h3>Diseños</h3>
                  <div id="disenos-container">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Imagen</th>
                          <th>Código</th>
                          <th>Estatus</th>
                          <th>Producto</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($menu as $diseno): ?>
                          <?php
                            $imagePath = !empty($diseno->file_path) ? '../../img/disenos/' . $diseno->file_path : '../../img/index/default.png';
                          ?>
                          <tr class='clickable-row' data-id='<?= $diseno->id_diseno ?>'>
                            <td>
                              <img src='<?= htmlspecialchars($imagePath) ?>' alt='<?= $diseno->codigo ?>' style='width:100px;height:auto;'>
                            </td>
                            <td><?= $diseno->codigo ?></td>
                            <td>
                              <form class='update-status-form' method='POST' action='../../scripts/administrador/actualizarestatusdiseno.php'>
                                <input type='hidden' name='id_diseno' value='<?= $diseno->id_diseno ?>'>
                                <input type='hidden' name='nuevo_estatus' value='<?= $diseno->estatus == "activo" ? "inactivo" : "activo" ?>'>
                                <button type='submit' class='btn <?= $diseno->estatus == "activo" ? "btn-success" : "btn-danger" ?> btn-sm mb-2 w-100 status-btn'>
                                  <i class='bi <?= $diseno->estatus == "activo" ? "bi-check" : "bi-x" ?>'></i>
                                </button>
                              </form>
                            </td>
                            <td><?= $diseno->producto_nombre ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="d-flex justify-content-center">
                    <button id="load-more" class="btn btn-primary mt-3">Ver más</button>
                  </div>
              </div>
          </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
      <script>
        $(document).ready(function() {
          let offset = 15;
          const limit = 15;

          $('#load-more').click(function() {
            $.ajax({
              url: '', // Mismo archivo para manejar la solicitud
              type: 'POST',
              data: { query: $('#search-input').val(), offset: offset, limit: limit, sortEstatus: '' },
              success: function(response) {
                const newRows = $(response).find('#disenos-container tbody').html();
                $('#disenos-container tbody').append(newRows);
                offset += limit;
              }
            });
          });

          $('#search-input').on('input', function() {
            const query = $(this).val();
            $.ajax({
              url: '', // Mismo archivo para manejar la solicitud
              type: 'POST',
              data: { query: query, offset: 0, limit: limit, sortEstatus: '' },
              success: function(response) {
                const newTable = $(response).find('#disenos-container').html();
                $('#disenos-container').html(newTable);
                offset = limit;
              }
            });
          });

          $(document).on('submit', '.update-status-form', function(e) {
            e.preventDefault();

            const form = $(this);
            const idDiseno = form.find('input[name="id_diseno"]').val();
            const nuevoEstatus = form.find('input[name="nuevo_estatus"]').val();

            $.ajax({
              url: form.attr('action'),
              type: 'POST',
              data: { id_diseno: idDiseno, nuevo_estatus: nuevoEstatus },
              success: function(response) {
                if (response.trim() === "Estatus actualizado") {
                  form.find('input[name="nuevo_estatus"]').val(nuevoEstatus === 'activo' ? 'inactivo' : 'activo');
                  form.find('.status-btn')
                      .toggleClass('btn-success btn-danger')
                      .find('i')
                      .toggleClass('bi-check bi-x');
                } else {
                  alert("Error al actualizar el estatus.");
                }
              }
            });
          });
        });

        function sortCitas(estatus) {
            $.ajax({
                url: '', // Mismo archivo para manejar la solicitud
                type: 'POST',
                data: { query: $('#search-input').val(), offset: 0, limit: 15, sortEstatus: estatus },
                success: function(response) {
                    const newTable = $(response).find('#disenos-container').html();
                    $('#disenos-container').html(newTable);
                    offset = 15;
                }
            });
        }
      </script>
    </div>
  </div>
</body>
</html>
