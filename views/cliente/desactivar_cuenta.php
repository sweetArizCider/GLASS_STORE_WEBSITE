<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desactivar Cuenta</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/normalized.css">
    <link rel="stylesheet" href="../../css/style_admin.css">
</head>

<body>
    <?php
    session_start();
    include '../../class/database.php'; // Asegúrate de que esta ruta sea correcta

    if (isset($_SESSION["nom_usuario"])) {
        $nombre_usuario_actual = $_SESSION["nom_usuario"];
        $db = new Database();
        $db->configurarConexionPorRol();
        $db->conectarDB();
    } else {
        echo "<div class='alert alert-danger'>No hay sesión de usuario válida.</div>";
    }
    ?>
    <!-- Barra lateral -->
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
                        data-bs-target="#desactivar" aria-expanded="false" aria-controls="desactivar">
                        <img src="../../img/instalador/clipboard.svg" alt="Desactivar cuenta">
                        <span>Desactivar cuenta</span>
                    </a>
                    <ul id="desactivar" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="./desactivar_cuenta.php" class="sidebar-link">Desactivar cuenta</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="./perfil.php" class="sidebar-link">
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

        <div class="container mt-5">
            <h1>Desactivar Cuenta</h1>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal">Desactivar Cuenta</button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirmar Desactivación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de que desea desactivar su cuenta?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="../../scripts/cliente/desactivar_cuenta.php" class="btn btn-danger">Desactivar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="../../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const hamBurger = document.querySelector(".toggle-btn");

            hamBurger.addEventListener("click", function () {
                document.querySelector("#sidebar").classList.toggle("expand");
            });
        </script>
    </div>
</body>

</html>
