<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
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
    <div class="background-cover">
        <?php
        session_start();
        if(isset($_SESSION["nom_usuario"]))
        {
            header("Location: ../index.php");
            exit();
        }
        else 
        {
        ?>
        <div class="login-form">
        <img src="../img/register/GLASS.png" alt="" class="logotipo-glass">
            <h2 class="text-center titleLogin">INICIAR SESIÓN</h2>
            <form class="formLogin" action="../scripts/verificaLogin.php" method="POST" class="mt-4">
            <div class="form-floating mb-3">
    <input type="text" class="form-control border-0 border-bottom rounded-0" name="usuario" id="usuario" placeholder="Nombre de Usuario" required>
    <label for="usuario" class="form-label">Usuario: </label>
</div>

<div class="form-floating mb-3">
    <input type="password" class="form-control border-0 border-bottom rounded-0" name="contrasena" id="contrasena" placeholder="Contraseña" required>
    <label for="contrasena" class="form-label">Contraseña: </label>
</div>
                <button type="submit" name="submit" class="buttonLogin">Verificar</button>
            </form>
            <p class="text-center mt-3 plogincuenta">¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.</p>
        </div>
        <?php
        }
        ?>


   

    <script>
    document.getElementById('iniciar-sesion').addEventListener('click', function() {
        var loginForm = document.getElementById('login-form');
        if (loginForm.style.display === 'none' || loginForm.style.display === '') {
            loginForm.style.display = 'block';
        } else {
            loginForm.style.display = 'none';
        }
    });

    function closeForm() {
        document.getElementById('login-form').style.display = 'none';
    }
    </script>
</body>
</html>
