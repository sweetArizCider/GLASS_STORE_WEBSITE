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

try {
    $stmt = $db->getPDO()->prepare("CALL roles_usuario(?)");
    $stmt->execute([$user]);
    $roles = $stmt->fetchAll(PDO::FETCH_OBJ);

    $isAdmin = false;

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
        error_log("Usuario sin privilegios de Administrador. Redirigiendo a iniciarSesion.php");
        header("Location: ../iniciarSesion.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Error al ejecutar el procedimiento almacenado: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

$db = new Database();
$db->conectarDB();
$cadena = "select
    c.id_cita,
    concat(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m) as nombre_cliente,
    c.fecha,
    c.hora,
    concat(d.calle, ' ', d.numero, ' ', d.numero_int, ', ', d.colonia, ', ', d.ciudad, '. referencias: ', d.referencias) as direccion,
    c.notas,
    ifnull(
        replace(
            (select group_concat(
                concat(
                    prod.nombre, ': $', dp.monto,
                    if(dp.alto is not null, concat(', alto: ', dp.alto), ''),
                    if(dp.largo is not null, concat(', largo: ', dp.largo), ''),
                    if(dp.cantidad is not null, concat(', cantidad: ', dp.cantidad), ''),
                    if(dp.grosor is not null, concat(', grosor: ', dp.grosor), ''),
                    if(dp.tipo_tela is not null, concat(', tipo de tela: ', dp.tipo_tela), ''),
                    if(dp.marco is not null, concat(', marco: ', dp.marco), ''),
                    if(dp.tipo_cadena is not null, concat(', tipo de cadena: ', dp.tipo_cadena), ''),
                    if(dp.color is not null, concat(', color: ', dp.color), ''),
                    if(dp.diseno is not null, concat(', diseño: ', dis.codigo), '')
                ) separator '\n'
            ) from detalle_cita dc 
            join detalle_producto dp on dc.detalle_producto = dp.id_detalle_producto 
            join productos prod on dp.producto = prod.id_producto 
            left join disenos dis on dp.diseno = dis.id_diseno
            where dc.cita = c.id_cita), 
            ',', '\n'
        ),
        'no hay cotizaciones'
    ) as cotizaciones
from
    citas c
join
    cliente_direcciones cd on c.cliente_direccion = cd.id_cliente_direcciones
join
    cliente cli on cd.cliente = cli.id_cliente
join
    persona p on cli.persona = p.id_persona
join
    direcciones d on cd.direccion = d.id_direccion
where
    c.estatus = 'en espera';";

$result = $db->ejecutar($cadena, []);

if ($result === false) {
    echo "Error en la consulta.";
    $db->desconectarDB();
    exit();
}

$instaladoresQuery = "
    SELECT
        i.id_instalador,
        p.nombres,
        p.apellido_p,
        p.apellido_m
    FROM instalador i
    JOIN persona p ON i.persona = p.id_persona;
";

$instaladoresResult = $db->ejecutar($instaladoresQuery, []);
$instaladores = $instaladoresResult ? $instaladoresResult->fetchAll(PDO::FETCH_ASSOC) : [];

$verificarAsignacionesQuery = "
    SELECT cita, COUNT(*) AS total_asignados
    FROM instalador_cita
    GROUP BY cita
";

$asignacionesResult = $db->ejecutar($verificarAsignacionesQuery, []);
$asignaciones = $asignacionesResult ? $asignacionesResult->fetchAll(PDO::FETCH_ASSOC) : [];

$asignacionesArray = [];
foreach ($asignaciones as $asignacion) {
    $asignacionesArray[$asignacion['cita']] = $asignacion['total_asignados'];
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
    .accordion-button::after {
      filter: invert(1);
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
        
      </div>
      <br>
      
      <div id="contenido" class="text-center">
    <div class="accordion" id="citasAccordion">
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?php echo $row['id_cita']; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $row['id_cita']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $row['id_cita']; ?>">
                    <?php echo htmlspecialchars($row['nombre_cliente']) . ' - ' . htmlspecialchars($row['fecha']) . ' - ' . htmlspecialchars($row['hora']); ?>
                </button>
            </h2>
            <div id="collapse<?php echo $row['id_cita']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $row['id_cita']; ?>" data-bs-parent="#citasAccordion">
                <div class="accordion-body">
                    <p><?php echo htmlspecialchars($row['direccion']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($row['cotizaciones'])); ?></p>
                    <div class="text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#asignarModal<?php echo $row['id_cita']; ?>" id="asignarButton<?php echo $row['id_cita']; ?>">Asignar</button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal<?php echo $row['id_cita']; ?>" id="rechazarButton<?php echo $row['id_cita']; ?>">Rechazar</button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- Modal para Asignar Instaladores -->
        <div class="modal fade" id="asignarModal<?php echo $row['id_cita']; ?>" tabindex="-1" aria-labelledby="asignarModalLabel<?php echo $row['id_cita']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="asignarModalLabel<?php echo $row['id_cita']; ?>">Asignar Instaladores</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formAsignar<?php echo $row['id_cita']; ?>" method="POST" action="../../scripts/administrador/asignarInstalador.php">
                        <div class="modal-body">
                            <input type="hidden" name="id_cita" value="<?php echo $row['id_cita']; ?>">
                            <?php foreach ($instaladores as $instalador): ?>
                                <div class="form-check">
                                    <input class="form-check-input instalador-checkbox" type="checkbox" name="instaladores[]" value="<?php echo $instalador['id_instalador']; ?>" id="instalador<?php echo $instalador['id_instalador']; ?>" data-cita-id="<?php echo $row['id_cita']; ?>">
                                    <label class="form-check-label" for="instalador<?php echo $instalador['id_instalador']; ?>">
                                        <?php echo $instalador['nombres'] . ' ' . $instalador['apellido_p'] . ' ' . $instalador['apellido_m']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" id="submitAsignar<?php echo $row['id_cita']; ?>">Asignar Instaladores</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Rechazar -->
        <div class="modal fade" id="rechazarModal<?php echo $row['id_cita']; ?>" tabindex="-1" aria-labelledby="rechazarModalLabel<?php echo $row['id_cita']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rechazarModalLabel<?php echo $row['id_cita']; ?>">Rechazar Cita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="../../scripts/administrador/rechazarCitas.php">
                        <div class="modal-body">
                            <textarea class="form-control" name="motivo" rows="3" placeholder="Escribe el motivo del rechazo..." required></textarea>
                            <input type="hidden" name="id_cita" value="<?php echo $row['id_cita']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger" id="confirmarRechazo<?php echo $row['id_cita']; ?>">Confirmar Rechazo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>


<script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/jquery.min.js"></script>

<script>
    function asignarInstaladores(idCita) {
        const instaladores = Array.from(document.getElementById(`instaladores${idCita}`).selectedOptions).map(option => option.value);
        
        if (instaladores.length === 0) {
            alert('Debes seleccionar al menos un instalador.');
            return;
        }

        $.ajax({
            url: '../../scripts/administrador/asignarInstalador.php',
            type: 'POST',
            data: {
                id_cita: idCita,
                instaladores: instaladores
            },
            success: function(response) {
                alert('Instaladores asignados con éxito.');
                location.reload(); // Recargar la página después de asignar
            },
            error: function(xhr, status, error) {
                console.error('Error al asignar instaladores:', error);
                alert('Ocurrió un error al asignar instaladores: ' + xhr.responseText);
            }
        });
    }

    document.getElementById('search-button').addEventListener('click', function() {
        const searchValue = document.getElementById('search-input').value.toLowerCase();
        const citas = document.querySelectorAll('.accordion-item');
        
        citas.forEach(cita => {
            const header = cita.querySelector('.accordion-button').textContent.toLowerCase();
            if (header.includes(searchValue)) {
                cita.style.display = '';
            } else {
                cita.style.display = 'none';
            }
        });
    });
    $(document).ready(function() {
    function updateAceptarButtonVisibility() {
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', function () {
                const idCita = this.getAttribute('data-bs-target').replace('#collapse', '');
                const aceptarButton = document.getElementById('aceptarButton' + idCita);
                const checkboxes = document.querySelectorAll(`#asignarModal${idCita} .instalador-checkbox`);
                const tieneAsignados = Array.from(checkboxes).some(checkbox => checkbox.checked);
                
                aceptarButton.style.display = tieneAsignados ? 'block' : 'none';
            });
        });
    }

    updateAceptarButtonVisibility();

    document.querySelectorAll('form[id^="formAsignar"]').forEach(form => {
        form.addEventListener('submit', function () {
            const idCita = this.querySelector('input[name="id_cita"]').value;
            const aceptarButton = document.getElementById('aceptarButton' + idCita);
            const checkboxes = this.querySelectorAll('.instalador-checkbox');
            const tieneAsignados = Array.from(checkboxes).some(checkbox => checkbox.checked);
            aceptarButton.style.display = tieneAsignados ? 'block' : 'none';
        });
    });
});
$(document).ready(function() {
    function updateAceptarButtonVisibility(idCita) {
        const aceptarButton = document.getElementById('aceptarButton' + idCita);
        const checkboxes = document.querySelectorAll(`#asignarModal${idCita} .instalador-checkbox`);
        const tieneAsignados = Array.from(checkboxes).some(checkbox => checkbox.checked);
        aceptarButton.style.display = tieneAsignados ? 'block' : 'none';
    }

    document.querySelectorAll('form[id^="formAsignar"]').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            const idCita = formData.get('id_cita');

            $.ajax({
                url: '../../scripts/administrador/asignarInstalador.php',
                type: 'POST',
                data: formData,
                processData: false, 
                contentType: false, 
                success: function(response) {
                    
                    updateAceptarButtonVisibility(idCita);
                    alert('Instaladores asignados con éxito.');
                },
                error: function(xhr, status, error) {
                    console.error('Error al asignar instaladores:', error);
                    alert('Ocurrió un error al asignar instaladores: ' + xhr.responseText);
                }
            });
        });
    });

    $('.collapse').on('shown.bs.collapse', function() {
        const idCita = $(this).attr('id').replace('collapse', '');
        updateAceptarButtonVisibility(idCita);
    });
});

    const hamBurger = document.querySelector(".toggle-btn");

    hamBurger.addEventListener("click", function () {
      document.querySelector("#sidebar").classList.toggle("expand");
    });
  </script>
</body>
</html>
