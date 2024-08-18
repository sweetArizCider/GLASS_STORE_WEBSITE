<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/normalized.css">
    <style>
        .is-invalid {
            border-color: #007bff;
        }
        .alert-custom {
            position: relative;
            margin-top: 10px;
            z-index: 1050;
        }
    </style>
</head>
<body>
    <!-- whatsapp flotante -->
    <div id="wa-button">
        <a href="https://api.whatsapp.com/send?phone=8717843809" target="_blank">
            <img src="../img/index/whatsappFloat.svg" alt="Contáctanos por WhatsApp">
        </a>
    </div>
    <!-- Logotipo superior -->

    <!-- -------------------------------------Contenido----------------------------------->
    <div class="row">
        <div class="col-12 col-lg-5 back-left background-left-image"></div>
        <div class="col-12 col-lg-7">
            <div class="container formulario-registro">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-5 d-flex flex-column align-items-center">
                            <img src="../img/register/GLASS.png" alt="" class="logotipo-glass">
                            <h1 class="display-5 fw-bold text-center bienvenido">CREAR CUENTA</h1>
                            <p class="text-center m-0">¿Ya tienes una cuenta? <a href="./iniciarSesion.php" style="cursor: pointer;" class="link-primary text-decoration-none" id="iniciar-sesion">Iniciar sesión</a></p>
                        </div>
                    </div>
                    <div class="form-register">
                        <div id="alertContainer"></div> <!-- Contenedor de alertas arriba del formulario -->
                        <form id="registerForm" action="../scripts/creaUsuario.php" method="POST">
                            <div class="row gy-3 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-0 border-bottom rounded-0" name="nombres" id="nombres" placeholder="Nombres" required>
                                        <label for="nombres" class="form-label">Nombres</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-0 border-bottom rounded-0" name="apellido_p" id="apellido_p" placeholder="Apellido Paterno" required>
                                        <label for="apellido_p" class="form-label">Apellido Paterno</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-0 border-bottom rounded-0" name="apellido_m" id="apellido_m" placeholder="Apellido Materno" >
                                        <label for="apellido_m" class="form-label">Apellido Materno</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control border-0 border-bottom rounded-0" name="correo" id="correo" placeholder="name@example.com" required>
                                        <label for="correo" class="form-label">Correo</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="tel" pattern="\d{10}" class="form-control border-0 border-bottom rounded-0" name="telefono" id="telefono" placeholder="Teléfono" required>
                                        <label for="telefono" class="form-label">Teléfono</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-0 border-bottom rounded-0" name="usuario" id="usuario" placeholder="Nombre de Usuario" required>
                                        <label for="usuario" class="form-label">Usuario</label>
                                        <input type="hidden" name="rol" value="2">
                                    </div>
                                    <small  class="text-muted">Mínimo 6 caracteres.</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control border-0 border-bottom rounded-0" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                                        <label for="contrasena" class="form-label">Contraseña</label>
                                    </div>
                                    <small class="text-muted">Mínimo 8 caracteres.</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control border-0 border-bottom rounded-0" name="confirmar_contrasena" id="confirmar_contrasena" placeholder="Confirmar Contraseña" required>
                                        <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="submit-button-register" type="submit">Registrarse</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Misión</h5>
                    <p>Transformar espacios con soluciones innovadoras y elegantes para el diseño de interiores, creando hogares y negocios funcionales, acogedores y que reflejen el estilo único de cada cliente.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="https://api.whatsapp.com/send?phone=8717843809" target="_blank" class="text-white">Contacto</a></li>
                        <li><a href="./products.html" class="text-white">Productos</a></li>
                        <li><a href="./citas.html" class="text-white">Agendar</a></li>
                        <li><a href="#about-us" id="link-nosotros" class="text-white">Nosotros</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contáctanos</h5>
                    <p><i class="bi bi-geo-alt"></i>Torreón Coahuila, México</p>
                    <p><i class="bi bi-envelope"></i> glassstore@gmail.com</p>
                    <p><i class="bi bi-phone"></i> +52 123 4564 456</p>
                </div>
            </div>
        </div>
        <div class="copy text-center py-3 w-100">
            <p class="mb-0">&copy; 2024 Glass Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const usuario = document.getElementById('usuario');
            const contrasena = document.getElementById('contrasena');
            const confirmarContrasena = document.getElementById('confirmar_contrasena');
            let valid = true;

            // Limpiar alertas previas
            document.getElementById('alertContainer').innerHTML = '';

            if (usuario.value.length < 6) {
                valid = false;
                usuario.classList.add('is-invalid');
                showAlert('El nombre de usuario debe tener al menos 6 caracteres.');
            } else {
                usuario.classList.remove('is-invalid');
            }

            if (contrasena.value.length < 8) {
                valid = false;
                contrasena.classList.add('is-invalid');
                showAlert('La contraseña debe tener al menos 8 caracteres.');
            } else {
                contrasena.classList.remove('is-invalid');
            }

            if (contrasena.value !== confirmarContrasena.value) {
                valid = false;
                confirmarContrasena.classList.add('is-invalid');
                showAlert('Las contraseñas no coinciden.');
            } else {
                confirmarContrasena.classList.remove('is-invalid');
            }

            if (valid) {
                this.submit();
            } else {
                document.getElementById('alertContainer').scrollIntoView({ behavior: 'smooth' });
            }
        });

        function showAlert(message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show alert-custom';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alertDiv);
        }
    </script>

    <script src="../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
