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
  .dropdown img {
            width: 50px;
            height: auto;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .dropdown-item span {
            margin-left: 10px;
        }
        select {
            width: 300px;
            height: 50px;
        }

        .inputPerfil {
    margin-bottom: 1rem;
}

.labelPerfilProduct {
    display: block;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    display: block;
    margin-top: 0.25rem;
}
</style>

</head>
<body>
<?php
session_start();
include '../../class/database.php';

$db = new database();
$db->conectarDB();
$user = $_SESSION["nom_usuario"];

if (isset($_SESSION['message'])) {
  echo "<div class='alert alert-success'>";
  echo "<h2 align='center'>" . $_SESSION['message'] . "</h2>";
  echo "</div>";

  // Eliminar el mensaje después de mostrarlo
  unset($_SESSION['message']);
}

// Consulta para obtener el id_instalador
$stmt = $db->getPDO()->prepare("
    SELECT i.id_instalador
    FROM instalador i
    JOIN persona p ON i.persona = p.id_persona
    JOIN usuarios u ON p.usuario = u.id_usuario
    WHERE u.nom_usuario = ?
");
$stmt->execute([$user]);
$result = $stmt->fetch(PDO::FETCH_OBJ);

if ($result) {
    $_SESSION["id_instalador"] = $result->id_instalador;
    $id_instalador = $result->id_instalador;

    $stmt_citas = $db->getPDO()->prepare("CALL obtener_citas_instalador(?)");
    $stmt_citas->execute([$id_instalador]);
    $citas = $stmt_citas->fetchAll(PDO::FETCH_OBJ);
    
    $stmt_citas->closeCursor();

    foreach ($citas as $cita) {
        $cita->nombre_cliente = isset($cita->cliente) ? $cita->cliente : 'Cliente Desconocido';
        $cita->direccion = isset($cita->direccion) ? $cita->direccion : 'Dirección no disponible';
        $cita->tipo = isset($cita->tipo) ? $cita->tipo : 'Tipo no disponible';

        if (isset($cita->id_cita)) {
            $stmt_detalles = $db->getPDO()->prepare("CALL obtener_detalles_producto_cita_numero(?)");
            $stmt_detalles->execute([$cita->id_cita]);
            $detalles_producto = $stmt_detalles->fetchAll(PDO::FETCH_OBJ);

            $cita->productos = $detalles_producto;

            $stmt_detalles->closeCursor();
        } else {
            $cita->productos = [];
        }
    }
} else {
    echo 'No se encontró el ID del instalador para el usuario.';
    $citas = []; 
}
?>

  <!-- Logo flotante del negocio -->
  <div id="logotipo-flotante">
    <img src="../../img/index/GLASS.png" alt="Glass store">
  </div>

  <!-- Barra lateral -->
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
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
            <img src="../../img/instalador/home.svg" alt="Inicio">
            <span>Inicio</span>
          </a>
          <ul id="inicio" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="#" class="sidebar-link">Volver al Inicio</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#notificaciones" aria-expanded="false" aria-controls="notificaciones">
            <img src="../../img/instalador/notificacion.svg" alt="Notificaciones">
            <span>Notificaciones</span>
          </a>
          <ul id="notificaciones" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./vista_instalador_notificaciones.php" class="sidebar-link">Ver Notificaciones</a>
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
              <a href="../../views/instalador/vista_instalador_citas.php" class="sidebar-link">Ver Citas</a>
            </li>
          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#reporte" aria-expanded="false" aria-controls="reporte">
            <img src="../../img/admin/clipboard.svg" alt="Reportes">
            <span>Reportes</span>
          </a>
          <ul id="reporte" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="../../views/instalador/vista_instalador_reportes.php" class="sidebar-link">Hacer Reporte</a>
            </li>
          </ul>
        </li>
      </ul>
      <div class="sidebar-footer">
        <a href="./index_Instalador.php" class="sidebar-link">
          <img src="../../img/admin/home.svg" alt="Volver">
          <span>Volver</span>
        </a>
      </div>
      <div class="sidebar-footer">
        <a href="#" class="sidebar-link">
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
      </div>
      <!-- Contenido general -->
      <div class="contenidoGeneral mt-4">
        <div class="general-container">
          <div class="d-flex justify-content-end mt-4">
            <div class="dropdown">
             
              <ul class="dropdown-menu" aria-labelledby="dropdownOrdenar">
                <li><a class="dropdown-item" href="#" onclick="sortCitas('recientes')">Recientes</a></li>
                <li><a class="dropdown-item" href="#" onclick="sortCitas('antiguos')">Antiguos</a></li>
              </ul>
            </div>
          </div>
          <!-- Acordeón de Citas -->
          <div class="accordion" id="accordionCitas">
            <?php foreach ($citas as $index => $cita): ?>
              <div class="accordion-item" data-cliente="<?= htmlspecialchars($cita->nombre_cliente) ?>" data-fecha="<?= htmlspecialchars($cita->fecha) ?>">
                <h2 class="accordion-header" id="heading<?= htmlspecialchars($cita->id_cita) ?>">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= htmlspecialchars($cita->id_cita) ?>" aria-expanded="true" aria-controls="collapse<?= htmlspecialchars($cita->id_cita) ?>">
                    <?= htmlspecialchars($cita->cliente) ?> - <?= htmlspecialchars($cita->fecha) ?> <?= htmlspecialchars($cita->hora) ?>
                  </button>
                </h2>
                <div id="collapse<?= htmlspecialchars($cita->id_cita) ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= htmlspecialchars($cita->id_cita) ?>" data-bs-parent="#accordionCitas">
                  <div class="accordion-body">
                    <p><strong>Dirección:</strong> <?= htmlspecialchars($cita->direccion) ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($cita->tipo) ?></p>
                    <p><strong>Notas:</strong> <?= htmlspecialchars($cita->notas) ?></p>

                    <!-- Lista de Detalles del Producto -->
                    <h5>Detalles del Producto</h5>
                    <ul class="list-group">
                      <?php if (!empty($cita->productos)): ?>
                        <?php foreach ($cita->productos as $producto): ?>
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                              <?php if (!empty($producto->producto_nombre)): ?>
                                <p><strong>Nombre:</strong> <?= htmlspecialchars($producto->producto_nombre) ?></p>
                              <?php endif; ?>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="toggleReportForm('report-form-<?= htmlspecialchars($producto->id_detalle) ?>')">Generar Reporte</button>
                          </li>

                          <!-- Formulario de Reporte -->
                          <div class="report-form" id="report-form-<?= htmlspecialchars($producto->id_detalle) ?>" style="display: none;">
                            <div class="card p-3 mb-3">
                              <h5>Generar Reporte para <?= htmlspecialchars($producto->producto_nombre) ?></h5>
                              <!--<?php var_dump($producto); ?>-->


<div id="cotizacionContainer" class="mt-5">
    <form id="cotizacionForm" method="POST" action="../../scripts/instalador/generar_reporte.php" class="formPerfil">
        <input type="hidden" name="detalle_cita" value="<?php echo htmlspecialchars($producto->id_detalle_cita); ?>">
        <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($producto->id_categoria); ?>">
        <input type="hidden" name="diseno" id="diseno" value="<?php echo htmlspecialchars($producto->producto_diseno); ?>">

        <!-- Campos generales -->
        
        <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="alto" class="form-label">Alto (metros)</label>
                <input type="number" class="form-control inputPerfilProductoCont" id="alto" name="alto" value="<?php echo htmlspecialchars($producto->producto_alto); ?>" step="0.01" max="10" required>
            </div>

            <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="largo" class="form-label">Largo (metros)</label>
                <input type="number" class="form-control inputPerfilProductoCont" id="largo" name="largo" value="<?php echo htmlspecialchars($producto->producto_largo); ?>" step="0.01" max="50" required>
            </div>

            <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="diseno" class="form-label">Diseno</label>
                    <?php
                    // Consulta para obtener todos los diseños activos
                    $query = "SELECT id_diseno, codigo, file_path FROM disenos WHERE estatus = 'activo'";
                    $disenos = $db->seleccionar($query);
                    $selectedDesignId = htmlspecialchars($producto->producto_diseno, ENT_QUOTES, 'UTF-8');
                    echo '
                        <select id="diseno" name="diseno">
                            ';

                            if (!empty($disenos)) {
                                foreach ($disenos as $diseno) {
                                $isSelected = $diseno->id_diseno == $selectedDesignId ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($diseno->id_diseno, ENT_QUOTES, 'UTF-8') . '" ' . $isSelected . '>';
                                echo '<img src="' . htmlspecialchars($diseno->file_path, ENT_QUOTES, 'UTF-8') . '" alt="Imagen de diseño" style="width: 50px; height: auto;">';
                                echo htmlspecialchars($diseno->codigo, ENT_QUOTES, 'UTF-8');
                                echo '</option>';
                            }
                            } else {
                                echo '<option disabled>No hay diseños activos</option>';
                            }
                    ?>
          </div>

          <br><br>
            <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control inputPerfilProductoCont" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($producto->producto_cantidad); ?>" max="10" required>
        </div>

        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="monto" class="form-label">Monto</label>
            <input type="text" class="form-control inputPerfilProductoCont" id="monto" name="monto" value="<?php echo htmlspecialchars($producto->producto_monto); ?>">
        </div>

        <!-- Campos dependientes de la categoría -->
        <?php 
        
        if ($producto->id_categoria == 1) : // VIDRIOS ?>

        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="grosor" class="form-label">Grosor</label>
            <select class="form-control inputPerfilProductoCont" id="grosor" name="grosor">
                <option value="">Seleccione</option>
                <option value="6" <?php echo ($producto->producto_grosor == '6') ? 'selected' : ''; ?>>6</option>
                <option value="10" <?php echo ($producto->producto_grosor == '10') ? 'selected' : ''; ?>>10</option>
                <option value="12" <?php echo ($producto->producto_grosor == '12') ? 'selected' : ''; ?>>12</option>
            </select>
        </div>

        <?php elseif ($producto->id_categoria == 2) : // PERSIANAS ?>

        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="marco" class="form-label">Marco</label>
            <input type="text" class="form-control inputPerfilProductoCont" id="marco" name="marco" value="<?php echo htmlspecialchars($producto->producto_marco); ?>">
        </div>
        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="tipo_cadena" class="form-label">Tipo de Cadena</label>
            <input type="text" class="form-control inputPerfilProductoCont" id="tipo_cadena" name="tipo_cadena" value="<?php echo htmlspecialchars($producto->producto_tipo_cadena); ?>">
        </div>

          <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="color" class="form-label">Color</label>
            <input type="text" class="form-control inputPerfilProductoCont" id="color" name="color" value="<?php echo htmlspecialchars($producto->producto_color); ?>">
        </div>

        <?php elseif ($producto->id_categoria == 3 or $producto->id_categoria == 4) : // TAPICES y HERRERIAS ?>

          

        <?php endif; ?>


        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="extras" class="form-label">Extras</label>
            <input type="text" class="form-control inputPerfilProductoCont" id="extras" name="extras">
        </div>

        <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="notas" class="form-label">Notas</label>
            <textarea class="form-control inputPerfilProductoCont" id="notas" name="notas"></textarea>
        </div>

        
        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <button type="button" class="btn btn-secondary" onclick="toggleReportForm('report-form-<?= htmlspecialchars($producto->id_detalle) ?>')">Cancelar</button>

    </form>
</div>



                            </div>
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <li class="list-group-item">No hay detalles de productos para esta cita.</li>
                      <?php endif; ?>
                    </ul>
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
    function toggleReportForm(formId) {
      const form = document.getElementById(formId);
      if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
      } else {
        form.style.display = 'none';
      }
    }

    const hamBurger = document.querySelector(".toggle-btn");
    hamBurger.addEventListener("click", function () {
      document.querySelector("#sidebar").classList.toggle("expand");
    });

    // Búsqueda por nombre de cliente
    document.getElementById('search-input').addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const citas = document.querySelectorAll('.accordion-item');

      citas.forEach(cita => {
        const cliente = cita.querySelector('.accordion-button').textContent.toLowerCase();
        if (cliente.includes(searchValue)) {
          cita.style.display = '';  // Muestra la cita
        } else {
          cita.style.display = 'none';  // Oculta la cita
        }
      });
    });

    // Ordenar citas por fecha
    function sortCitas(order) {
      const accordion = document.getElementById('accordionCitas');
      const citas = Array.from(accordion.querySelectorAll('.accordion-item'));

      citas.sort((a, b) => {
        const dateA = new Date(a.getAttribute('data-fecha'));
        const dateB = new Date(b.getAttribute('data-fecha'));

        return order === 'recientes' ? dateB - dateA : dateA - dateB;
      });

      citas.forEach(cita => accordion.appendChild(cita));
    }
  </script>
</body>
</html>

