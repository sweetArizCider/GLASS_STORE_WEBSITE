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

$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$cadena = "SELECT
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
FROM
    citas c
JOIN
    cliente_direcciones cd on c.cliente_direccion = cd.id_cliente_direcciones
JOIN
    cliente cli on cd.cliente = cli.id_cliente
JOIN
    persona p on cli.persona = p.id_persona
JOIN
    direcciones d on cd.direccion = d.id_direccion
WHERE
    c.estatus = 'en espera'
ORDER BY c.fecha $order";


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
    .secc-sub-general {
      border: 1px solid rgba(19, 38, 68, 0.30);
      border-radius: 10px;
      padding: 1rem;
      background-color: #f9f9f9;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
      text-align: left;
    }
    .text-end {
      text-align: right;
    }
    .buuton-dar-rol-danger{
      background-color: #c82333;
    color: #fff;
    font-family: 'Montserrat';
    font-weight: 400;
    margin: auto;
    border: none;
    border-radius: 30px;
    padding: 10px 20px;
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
      <div class="text-center">
      <div class="col-12 mb-4 card-bienvenida">
        <div class="text-center">
          <h6 class="mensaje-bienvenida">Gestionar Citas</h6>
          <br>
          <div class="text-center">         
              <button class="buuton-dar-rol" onclick="window.location.href='?order=asc'">Citas Proximas</button>
              <button class="buuton-dar-rol" onclick="window.location.href='?order=desc'">Citas Lejanas</button>
          </div>
        </div>
      </div><br>
        
      </div>
      <br>
      
      <div id="contenido" class="text-center">
        <div class="general-container">
          <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $cotizaciones = nl2br(htmlspecialchars($row['cotizaciones']));
            $hasCotizaciones = $cotizaciones !== 'no hay cotizaciones';
            $tipo_cita = $hasCotizaciones ? "Cita de toma de medidas" : "Instalación";
            $fecha = date('d \d\e F \d\e Y', strtotime($row['fecha']));
            $hora = date('h:i A', strtotime($row['hora']));
          ?>
          <div class="secc-sub-general">
            <p class="fecha"><?php echo $fecha; ?></p>
            <p><mark class="marklued"><?php echo htmlspecialchars($row['nombre_cliente']); ?></mark><br>
              Requiere <span class="bueld"><?php echo $tipo_cita; ?></span> en el domicilio: 
              <span class="bueld"><?php echo htmlspecialchars($row['direccion']); ?></span><br>
              el día <span class="bueld"><?php echo $fecha; ?></span> a las <span class="bueld"><?php echo $hora; ?></span></p>
            
            <?php if ($hasCotizaciones): ?>
            <p class="cotizacion-toggle" data-bs-toggle="collapse" data-bs-target="#cotizaciones<?php echo $row['id_cita']; ?>">Ver Cotizaciones</p>
            <div id="cotizaciones<?php echo $row['id_cita']; ?>" class="collapse">
              <p><?php echo $cotizaciones; ?></p>
            </div>
            <?php else: ?>
            <p>No hay cotizaciones</p>
            <?php endif; ?>

            <div class="text-end">
              <button class="buuton-dar-rol" data-bs-toggle="modal" data-bs-target="#asignarModal<?php echo $row['id_cita']; ?>" id="asignarButton<?php echo $row['id_cita']; ?>">Asignar</button>
              <button class="buuton-dar-rol-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal<?php echo $row['id_cita']; ?>" id="rechazarButton<?php echo $row['id_cita']; ?>">Rechazar</button>
            </div>
          </div>

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
    </div>
  </div>


<script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/jquery.min.js"></script>

<script>
  const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
});

$(document).ready(function() {
        $('.cotizacion-toggle').on('click', function() {
            var target = $(this).data('target');
            $(target).collapse('toggle');
        });
    });


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

    
  </script>
</body>
</html>
