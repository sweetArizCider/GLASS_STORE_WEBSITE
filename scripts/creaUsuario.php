<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
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
        .welcome-message {
            text-align: center;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<?php     
    include '../class/database.php';
    $db = new Database();
    $db->conectarDB();
    $db->configurarConexionPorRol();

    $userCreated = false;
    $userName = '';

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

        // Generar los hashes
        $contrasena_hash = hash('sha256', $contrasena);

        // Crear la consulta
        try {
            $stmt = $db->getPDO()->prepare("CALL crear_cuenta(:contrasena, :usuario, :rol, :nombres, :apellido_p, :apellido_m, :correo, :telefono)");
        
            // Vincular parámetros
            $stmt->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR); // Asegúrate de que $rol esté definido y sea correcto
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_p', $apellido_p, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_m', $apellido_m, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        
            // Ejecutar la consulta
            $stmt->execute();
            
            $userCreated = true;

        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
        
        $db->desconectarDB();
    } 
?>
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
