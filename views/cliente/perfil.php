<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/normalized.css">
    <link rel="stylesheet" href="../../css/style_admin.css">
    <style>
        .form-control:invalid {
            border-color: #dc3545;
        }
        .form-control:valid {
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION["nom_usuario"])) {
        $nombre_usuario = $_SESSION["nom_usuario"];
        
        include '../../class/database.php';
        $db = new Database();
        $db->conectarDB();

        // Obtener los datos del usuario
        $cadena = "CALL obtenerdatosusuario(:nombre_usuario)";
        $params = [':nombre_usuario' => $nombre_usuario];
        $stmt = $db->ejecutarcita($cadena, $params);

        // Verificar si se obtuvieron resultados
        if ($stmt && $stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $nombres = $usuario['nombres'];
            $apellido_p = $usuario['apellido_p'];
            $apellido_m = $usuario['apellido_m'];
            $correo = $usuario['correo'];
            $telefono = $usuario['telefono'];
        } else {
            echo "<div class='alert alert-danger'>No se encontraron datos del usuario.</div>";
        }

        $db->desconectarDB();
    } else {
        header("Location: iniciarSesion.php");
        exit();
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
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#inicio" aria-expanded="false" aria-controls="inicio">
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
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#recibos" aria-expanded="false" aria-controls="recibos">
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
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#desactivar" aria-expanded="false" aria-controls="desactivar">
                        <img src="../../img/instalador/off.svg" alt="Desactivar cuenta">
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

        <!-- Contenido principal -->
        <div class="main p-3">
            <div class="container mt-5">
                <h2>Editar Perfil</h2>
                <form action="../../scripts/cliente/editar_perfil.php" method="POST">
                    <!-- Datos Personales -->
                    <div class="card mb-4">
                        <div class="card-header">
                            Datos Personales
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombre_usuario">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="nombres">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($nombres); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido_p">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellido_p" name="apellido_p" value="<?php echo htmlspecialchars($apellido_p); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido_m">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellido_m" name="apellido_m" value="<?php echo htmlspecialchars($apellido_m); ?>">
                            </div>
                            <div class="form-group">
                                <label for="correo">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" pattern="\d{10}" value="<?php echo htmlspecialchars($telefono); ?>" required>
                                <small class="form-text text-muted">Debe ser un número de 10 dígitos.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div class="card mb-4">
                        <div class="card-header">
                            Cambiar Contraseña
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="contrasena_actual">Contraseña Actual</label>
                                <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual">
                            </div>
                            <div class="form-group">
                                <label for="nueva_contrasena">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena">
                            </div>
                            <div class="form-group">
                                <label for="confirmar_contrasena">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
