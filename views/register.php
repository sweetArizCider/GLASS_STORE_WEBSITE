<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/normalized.css">
</head>
<body>
<!-- whatsapp flotante -->
<div id="wa-button">
    <a href="https://api.whatsapp.com/send?phone=8717843809" target="_blank">
    <img src="../img/index/whatsappFloat.svg" alt="Contáctanos por WhatsApp">
    </a>
</div>
<!--Logotipo superior-->

<!-------------------------------------Contenido----------------------------------->
<div class="row">
    <div class="col-12 col-lg-5 back-left background-left-image"></div>
    <div class="col-12 col-lg-7">
        <div class="container formulario-registro">
            <div class="row">
                <div class="col-12">
                    <div class="mb-5 d-flex flex-column align-items-center">
                        <img src="../img/register/GLASS.png" alt="" class="logotipo-glass">
                        <h1 class="display-5 fw-bold text-center bienvenido">CREAR CUENTA</h1>
                        <p class="text-center m-0">¿Ya tienes una cuenta? <a style="cursor: pointer;" class="link-primary text-decoration-none" id="iniciar-sesion">Iniciar sesión</a></p>
                    </div>
                    <div class="login-form" id="login-form">
                        <button class="close-btn" onclick="closeForm()"><img src="../img/register/close.svg" alt=""></button>
                        <h6>Iniciar Sesión</h6>
                        <form action="/login">
                            <input type="email" placeholder="Correo" required name="correo" id="correo">
                            <input type="password" placeholder="Contraseña" required name="contraseña" id="contraseña">
                            <p class="pop-login-p">¿No tienes cuenta?</p>
                            <p class="space-cero"><a href="../html/register.html" class="pop-login-create">Crea una</a></p>
                            <button type="submit">Aceptar</button>
                        </form>
                    </div>
                </div>
                <div class="form-register">
                    <form action="../scripts/creaUsuario.php" method="POST"> <!--why hay dos scrips para crear usuario? >:( el bueno es creaUsuario, guarda_user ese no-->
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
                                    <input type="text" class="form-control border-0 border-bottom rounded-0" name="apellido_m" id="apellido_m" placeholder="Apellido Materno" required>
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
                                    <input type="text" class="form-control border-0 border-bottom rounded-0" name="telefono" id="telefono" placeholder="Teléfono">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-0 border-bottom rounded-0" name="usuario" id="usuario" placeholder="Nombre de Usuario" required>
                                    <label for="usuario" class="form-label">Usuario</label>
                                    <input type="hidden" name="rol" value="2"> <!-- Asegúrate de que el valor del rol sea correcto -->
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control border-0 border-bottom rounded-0" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                                    <label for="contrasena" class="form-label">Contraseña</label>
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

</body>
<script>
document.getElementById('iniciar-sesion').addEventListener('click', function() {
            var loginForm = document.getElementById('login-form');
            if (loginForm.style.display === 'none' || loginForm.style.display === '') {
                loginForm.style.display = 'block';
            } else {
                loginForm.style.display = 'none';
            }
    });
    //para cerrar
    function closeForm() {
            document.getElementById('login-form').style.display = 'none';
        }
</script>
</html>