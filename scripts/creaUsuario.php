<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Registro de Usuario</title>
    <style>
        .welcome-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
            background-size: cover;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .error-welcome-user{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../img/index/background.jpeg) center/cover no-repeat;
            background-size: cover;


        }
        .welcome-message {
            text-align: center;
        }
        .hidden {
            display: none;
        }
        .error-container {
                    background-color: rgba(255, 255, 255); /* Fondo semitransparente */
                    padding: 20px;
                    padding-bottom: 20px !important;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 500px;
                    width: 100%;
                }
                .error-container h1 {
                    font-family: 'Montserrat';
                    color: #c82333;
                    font-size: 2.5em;
                    font-weight: 800;
                    margin-bottom: 15px;
                }
                .error-container p {
                    font-family: 'Montserrat';
                    font-size: .9em;
                    margin-bottom: 15px;
                }
                .error-container .btn {
                    background-color: #c82333;
                    color: #ffffff;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                    transition: background-color 0.3s;
                }
                .error-container .btn:hover {
                    background-color: #a71d2a;
                }
                .button-retry{
                    background: #c82333;
                    border: 1.5px solid #c82333;
                    border-radius: 30px;
                    font-family: Inter;
                    font-size: .9em;
                    font-weight: 400;
                    color: #fff;
                    cursor: pointer;
                    padding: 8px 18px;
                    text-decoration: none;
                }
    </style>
</head>
<body>
<?php     
    include '../class/database.php';
    $db = new Database();
    $db->conectarDB();

    $userCreated = false;
    $userName = '';
    $cotizacionesEncontradas = 0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Extraer datos del formulario
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        $nombres = $_POST['nombres'];
        $apellido_p = $_POST['apellido_p'];
        $apellido_m = $_POST['apellido_m'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $rol = $_POST['rol']; // en la vista de admin ella puede decidir el valor, en la de cliente no
        $invitado_id = isset($_COOKIE['invitado_id']) ? $_COOKIE['invitado_id'] : null;

        // Verificar la cookie de invitado y mostrarla en consola
        if ($invitado_id) {
            echo "<script>console.log('ID de invitado pasada: " . $invitado_id . "');</script>";
        } else {
            echo "<script>console.log('No se encontró un ID de invitado en la cookie.');</script>";
        }

        // Generar los hashes
        $contrasena_hash = hash('sha256', $contrasena);

        // Crear la consulta
        try {
            $stmt = $db->getPDO()->prepare("CALL crear_cuenta(:contrasena, :usuario, :rol, :nombres, :apellido_p, :apellido_m, :correo, :telefono, :invitado_id)");
        
            // Vincular parámetros
            $stmt->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_p', $apellido_p, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_m', $apellido_m, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindParam(':invitado_id', $invitado_id, PDO::PARAM_STR);
        
            // Ejecutar la consulta
            $stmt->execute();
            
            // Obtener el número de cotizaciones afectadas (usando una consulta separada si es necesario)
            $cotizacionesEncontradas = $stmt->rowCount();  // Esto cuenta las filas afectadas
            
            $userCreated = true;

        } catch (PDOException $e) {
            ?>
        <div class="error-welcome-user">
        <div class="welcome-message">
            <div class="error-container">
                <img src="../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                <h1>¡Usuario Ocupado!</h1>
                <p>Ha ocurrido un error al intentar crear tu cuenta. Por favor, ingresa un nuevo nombre de usuario.</p>
                
                <a href="../views/register.php" class="button-retry">Volver a Intentar</a>
                <br><br>
            </div>
        </div>
        </div>
        <?php
        }
        
        header("refresh:2;../views/register.php"); 
    } 
?>
<script>
    console.log('Cotizaciones encontradas: <?php echo $cotizacionesEncontradas; ?>');
</script>

<?php if ($userCreated): ?>
    <div class="welcome-container">
        <div class="welcome-message">
            <h1>Bienvenido, <?php echo htmlspecialchars($nombres); ?>!</h1>
            <p>Tu cuenta  <?php echo htmlspecialchars($usuario); ?>  ha sido creada exitosamente.</p>

        </div>
    </div>
<?php endif;  header("refresh:2;../views/iniciarSesion.php"); ?>

<script src="../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
