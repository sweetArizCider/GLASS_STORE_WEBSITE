<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
      </ul>
      <div class="sidebar-footer">
        <a href="../index.php" class="sidebar-link">
          <img src="../img/admin/home.svg" alt="Volver"><!--PONER UNA IMAGEN COMO DE VOLVER-->
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-footer">
        <a href="../scripts/cerrarSesion.php" class="sidebar-link">
        <img src="../img/admin/logout.svg" alt="Cerrar Sesión">
        <span>Cerrar Sesión</span>
        </a>
    </div>
    </aside>
    <div class="container mt-4">
        <h1>Recibos</h1>

        <?php
        include '../class/database.php'; // Incluye el archivo con la conexión a la base de datos

        $database = new Database();
        $database->conectarDB();

        $criterio = isset($_POST['criterio']) ? $_POST['criterio'] : '';
        $nombre_completo = isset($_POST['nombre_completo']) ? $_POST['nombre_completo'] : '';
        $fecha_pago_1 = isset($_POST['fecha_pago_1']) ? $_POST['fecha_pago_1'] : '';
        $fecha_pago_2 = isset($_POST['fecha_pago_2']) ? $_POST['fecha_pago_2'] : '';

        $resultados = [];

        if ($criterio === 'nombre' && $nombre_completo) {
            // Llamada al procedimiento almacenado para filtrar por nombre
            $query = "CALL buscarrecibospornombrecliente(:nombre_cliente)";
            $params = [':nombre_cliente' => $nombre_completo];
            $resultados = $database->ejecutarProcedimiento($query, $params);
        } elseif ($criterio === 'fecha' && $fecha_pago_1 && $fecha_pago_2) {
            // Llamada al procedimiento almacenado para filtrar por fecha
            $query = "CALL buscarrecibosporfecha(:fecha_pago_1, :fecha_pago_2)";
            $params = [
                ':fecha_pago_1' => $fecha_pago_1,
                ':fecha_pago_2' => $fecha_pago_2
            ];
            $resultados = $database->ejecutarProcedimiento($query, $params);
        }

        $database->desconectarDB();
        ?>

        <!-- Formulario de Búsqueda -->
        <form action="recibos.php" method="post">
            <div class="form-group">
                <label for="criterio">Buscar Por:</label>
                <select id="criterio" name="criterio" class="form-select" required>
                    <option value="">Seleccione un criterio</option>
                    <option value="nombre" <?php echo $criterio === 'nombre' ? 'selected' : ''; ?>>Nombre Completo</option>
                    <option value="fecha" <?php echo $criterio === 'fecha' ? 'selected' : ''; ?>>Fecha de Pago</option>
                </select>
            </div>
            <div id="nombre_completo_div" class="form-group <?php echo $criterio === 'nombre' ? '' : 'hidden'; ?>">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($nombre_completo); ?>">
            </div>
            <div id="fecha_pago_div" class="form-group <?php echo $criterio === 'fecha' ? '' : 'hidden'; ?>">
                <label for="fecha_pago_1">Fecha de Pago Desde:</label>
                <input type="date" id="fecha_pago_1" name="fecha_pago_1" class="form-control" value="<?php echo htmlspecialchars($fecha_pago_1); ?>">
                <label for="fecha_pago_2" class="mt-2">Fecha de Pago Hasta:</label>
                <input type="date" id="fecha_pago_2" name="fecha_pago_2" class="form-control" value="<?php echo htmlspecialchars($fecha_pago_2); ?>">
            </div>
            <button type="submit" class="btn btn-dark mt-3">Buscar</button>
            <br><br>
        </form>

        <!-- Resultados de la Búsqueda -->
        <?php if ($resultados): ?>
        <div class="accordion" id="recibosAccordion">
            <?php foreach ($resultados as $index => $fila): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                        <div class="d-flex flex-column">
                            <span><strong>ID Venta:</strong> <?php echo htmlspecialchars($fila->id_venta); ?></span>
                            <span><strong>Fecha Venta:</strong> <?php echo htmlspecialchars($fila->fecha_venta); ?></span>
                            <span><strong>Total:</strong> <?php echo htmlspecialchars($fila->total); ?></span>
                            <span><strong>Saldo:</strong> <?php echo htmlspecialchars($fila->saldo); ?></span>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#recibosAccordion">
                    <div class="accordion-body">
                        <p><strong>Subtotal:</strong> <?php echo htmlspecialchars($fila->subtotal); ?></p>
                        <p><strong>Total Promoción:</strong> <?php echo htmlspecialchars($fila->total_promocion); ?></p>
                        <p><strong>Fecha de Pago:</strong> <?php echo htmlspecialchars($fila->fecha_pago); ?></p>
                        <p><strong>Cantidad Pagada:</strong> <?php echo htmlspecialchars($fila->cantidad_pagada); ?></p>
                        <p><strong>Extras:</strong> <?php echo htmlspecialchars($fila->extras); ?></p>
                        <p><strong>Notas:</strong> <?php echo htmlspecialchars($fila->notas); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p>No se encontraron resultados.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const criterioSelect = document.getElementById('criterio');
            const nombreCompletoDiv = document.getElementById('nombre_completo_div');
            const fechaPagoDiv = document.getElementById('fecha_pago_div');

            criterioSelect.addEventListener('change', function() {
                const criterio = criterioSelect.value;
                if (criterio === 'nombre') {
                    nombreCompletoDiv.classList.remove('hidden');
                    fechaPagoDiv.classList.add('hidden');
                } else if (criterio === 'fecha') {
                    fechaPagoDiv.classList.remove('hidden');
                    nombreCompletoDiv.classList.add('hidden');
                } else {
                    nombreCompletoDiv.classList.add('hidden');
                    fechaPagoDiv.classList.add('hidden');
                }
            });

            // Asegurarse de que los campos se muestran según el criterio al cargar la página
            criterioSelect.dispatchEvent(new Event('change'));
        });
    </script>
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
