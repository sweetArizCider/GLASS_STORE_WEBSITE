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
    // Obtener el ID del cliente
    $query_cliente = "SELECT c.id_cliente 
                      FROM cliente c
                      JOIN persona p ON c.persona = p.id_persona
                      JOIN usuarios u ON p.usuario = u.id_usuario
                      WHERE u.nom_usuario = :nombre_usuario";

    $stmt_cliente = $db->ejecutarcita($query_cliente, [':nombre_usuario' => $user]);
    $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
    $id_cliente = $cliente['id_cliente'];

    // Obtener las citas del cliente
    $query_citas = "SELECT 
                        c.id_cita,
                        c.fecha,
                        c.hora,
                        c.estatus AS estatus_cita,
                        concat(d.calle, ' ', d.numero, ' ', d.numero_int, ', ', d.colonia, ', ', d.ciudad, '. referencias: ', d.referencias) as direccion,
                        ifnull(
                            replace(
                                (SELECT group_concat(
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
                                ) 
                                FROM detalle_cita dc 
                                JOIN detalle_producto dp ON dc.detalle_producto = dp.id_detalle_producto 
                                JOIN productos prod ON dp.producto = prod.id_producto 
                                LEFT JOIN disenos dis ON dp.diseno = dis.id_diseno
                                WHERE dc.cita = c.id_cita),
                                ',', '\n'
                            ),
                            'no hay cotizaciones'
                        ) as cotizaciones,
                        (SELECT concat(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m)
                         FROM instalador_cita ic
                         JOIN instalador i ON ic.instalador = i.id_instalador
                         JOIN persona p ON i.persona = p.id_persona
                         WHERE ic.cita = c.id_cita
                         LIMIT 1) AS instalador_asignado
                    FROM citas c
                    JOIN cliente_direcciones cd ON c.cliente_direccion = cd.id_cliente_direcciones
                    JOIN direcciones d ON cd.direccion = d.id_direccion
                    WHERE cd.cliente = :id_cliente";

    $stmt_citas = $db->ejecutarcita($query_citas, [':id_cliente' => $id_cliente]);

    if ($stmt_citas === false) {
        echo "Error en la consulta.";
        $db->desconectarDB();
        exit();
    }

    $citas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Error al ejecutar las consultas: " . $e->getMessage());
    header("Location: ../iniciarSesion.php");
    exit();
}

$db->desconectarDB();
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial de Citas</title>
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
    
<!--Barra lateral-->
<div class="wrapper">
    <aside id="sidebar">
      <div class="d-flex">
        <button class="toggle-btn" type="button">
          <img src="../../img/index/menu.svg" alt="Menu">
        </button>
        <div class="sidebar-logo">
          <a href="../../../">GLASS STORE</a>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
             <img src="../../img/instalador/home.svg" alt="Perfil">
            <span>Inicio</span>
          </a>
          <ul id="inicio" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./perfil.php" class="sidebar-link">Volver al Inicio</a>
            </li>

          </ul>
        </li>
        <li class="sidebar-item">
          <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
             data-bs-target="#citas" aria-expanded="false" aria-controls="citas">
            <img src="../../img/admin/clipboard.svg" alt="Citas">
            <span>Citas</span>
          </a>
          <ul id="citas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
            <li class="sidebar-item">
              <a href="./citas_cliente.php" class="sidebar-link">Tus Citas</a>
            </li>
          </ul>
        </li>
        
        
      </ul>

      <div class="sidebar-footer">
        <a href="../../scripts/cerrarSesion.php" class="sidebar-link">
            <img src="../../img/admin/logout.svg" alt="Cerrar Sesión">
            <span>Cerrar Sesión</span>
        </a>
    </div>
    </aside>
  
  <!-- Barra lateral y logo flotante (omitido para brevedad) -->

  <div class="main p-3">
      <div class="text-center">
      <div class="col-12 mb-4 card-bienvenida">
        <div class="text-center">
          <h5 class="mensaje-bienvenida">Historial de Citas</h5>
        </div>
      </div>
      <br>
        
      </div>
      <br>
      
      <div id="contenido" class="text-center">
        <div class="general-container">
          <?php foreach ($citas as $row): 
            $cotizaciones = nl2br(htmlspecialchars($row['cotizaciones']));
            $hasCotizaciones = $cotizaciones !== 'no hay cotizaciones';
            $fecha = date('d \d\e F \d\e Y', strtotime($row['fecha']));
            $hora = date('h:i A', strtotime($row['hora']));
          ?>
          <div class="secc-sub-general">
            <p class="fecha"><?php echo $fecha; ?></p>
            <p>
              Cita programada en el domicilio: 
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

            <?php if ($row['instalador_asignado']): ?>
              <p>Instalador asignado: <?php echo htmlspecialchars($row['instalador_asignado']); ?></p>
            <?php else: ?>
              <p>No hay instalador asignado</p>
            <?php endif; ?>

          </div>

          <?php endforeach; ?>
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
</script>
</body>
</html>
