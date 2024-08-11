<?php
session_start();

if (isset($_SESSION["nom_usuario"])) {
    $nombre_usuario = $_SESSION["nom_usuario"];
    
    include '../../class/database.php';
    $db = new database(); // Crear instancia de la clase database
    $db->conectarDB(); // Establecer la conexión a la base de datos
    $db->configurarConexionPorRol();

    // Verificar si la conexión es válida
    $pdo = $db->getPDO();
    if ($pdo) {
        // Consulta para obtener los recibos del usuario
        $query = "
            select v.fecha as fecha_venta, 
                v.subtotal, 
                v.total_promocion, 
                v.extras, 
                v.total, 
                v.saldo, 
                ifnull(sum(ha.cantidad_pagada), 0) as total_abonos,
                max(ha.fecha_pago) as fecha_ultimo_abono
            from ventas v
            left join historial_abonos ha on v.id_venta = ha.venta
            join detalle_venta dv on v.id_venta = dv.venta
            join reporte r on dv.reporte = r.id_reporte
            join detalle_cita dc on r.detalle_cita = dc.id_detalle_cita
            join citas ci on dc.cita = ci.id_cita
            join cliente_direcciones cd on ci.cliente_direccion = cd.id_cliente_direcciones
            join cliente c on cd.cliente = c.id_cliente
            join persona p on c.persona = p.id_persona
            join usuarios u on p.usuario = u.id_usuario
            where u.nom_usuario = :nom_usuario
            group by v.fecha, v.subtotal, v.total_promocion, v.extras, v.total, v.saldo;
        ";

        // Preparar y ejecutar la consulta
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([':nom_usuario' => $nombre_usuario]);
            $recibos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
            $recibos = []; // Inicializar la variable para evitar el warning
        }
    } else {
        echo "No se pudo conectar a la base de datos.";
        $recibos = []; // Inicializar la variable para evitar el warning
    }
    $db->desconectarDB(); // Cerrar la conexión
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos del Cliente</title>
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/normalized.css">
    <link rel="stylesheet" href="../../css/style_admin.css">
</head>
<body>
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
                    data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
                    <img src="../../img/instalador/home.svg" alt="Inicio">
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
                    data-bs-target="#recibos" aria-expanded="false" aria-controls="recibos">
                        <img src="../../img/instalador/notificacion.svg" alt="Recibos">
                        <span>Recibos</span>
                    </a>
                    <ul id="recibos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="./recibos_cliente.php" class="sidebar-link">Ver recibos</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                    data-bs-target="#desactivarCuenta" aria-expanded="false" aria-controls="desactivarCuenta">
                        <img src="../../img/instalador/clipboard.svg" alt="Desactivar cuenta">
                        <span>Desactivar cuenta</span>
                    </a>
                    <ul id="desactivarCuenta" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="./desactivar_cuenta.php" class="sidebar-link">Desactivar cuenta</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="../../index.php" class="sidebar-link">
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
        
        <div class="container mt-4">
            <div class="accordion" id="recibosAccordion">
                <?php if (!empty($recibos)) : ?>
                    <?php foreach ($recibos as $index => $recibo) : ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                    <div class="d-flex flex-column w-100">
                                        <span><strong>Fecha:</strong> <?php echo htmlspecialchars($recibo['fecha_venta']); ?></span>
                                        <span><strong>Total:</strong> <?php echo htmlspecialchars($recibo['total']); ?></span>
                                        <span><strong>Saldo:</strong> <?php echo htmlspecialchars($recibo['saldo']); ?></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#recibosAccordion">
                                <div class="accordion-body">
                                    <p><strong>Subtotal:</strong> <?php echo htmlspecialchars($recibo['subtotal']); ?></p>
                                    <p><strong>Total promoción:</strong> <?php echo htmlspecialchars($recibo['total_promocion']); ?></p>
                                    <p><strong>Extras:</strong> <?php echo htmlspecialchars($recibo['extras']); ?></p>
                                    <p><strong>Fecha último abono:</strong> <?php echo htmlspecialchars($recibo['fecha_ultimo_abono']); ?></p>
                                    <p><strong>Total abonos:</strong> <?php echo htmlspecialchars($recibo['total_abonos']); ?></p>
                                </div>
                            </div>
                        </div>
                        <br>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No se encontraron recibos para el usuario.</p>
                <?php endif; ?>
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
