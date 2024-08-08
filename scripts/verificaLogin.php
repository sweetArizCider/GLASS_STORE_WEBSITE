<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">

    <?php
    include '../class/database.php';

    $db = new Database();
    $db->conectarDB();

    $mensaje = '';
    $redirectUrl = '../views/iniciarSesion.php'; // URL por defecto en caso de error

    if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
        $usuario = $_POST['usuario'];
        $contra = $_POST['contrasena'];  // No hashees la contraseña aquí; el procedimiento lo hace

        try {
            // Usar el método corregido
            $query = "CALL autenticacion(:usuario, :contrasena)";
            $params = [
                ':usuario' => $usuario,
                ':contrasena' => $contra
            ];

            // Ejecutar procedimiento y obtener resultados
            $result = $db->ejecutarProcedimiento($query, $params);

            if ($result && count($result) > 0) {
                // Asumiendo que el resultado es un array de objetos o stdClass
                $mensaje = $result[0]->mensaje ?? 'Error en la autenticación';
                
                if ($mensaje == 'Autenticación exitosa') {
                    session_start();
                    $_SESSION["nom_usuario"] = $usuario;
                    $_SESSION["rol"] = $result[0]->rol ?? 'default'; // Asumiendo que el procedimiento almacenado devuelve el rol
                    $mensaje = "<h2 align='center'>BIENVENIDO ". $_SESSION["nom_usuario"]. "</h2>";
                    $redirectUrl = '../index.php'; // Cambia la URL de redirección
                } else {
                    $mensaje = $mensaje;
                }
            } else {
                $mensaje = 'Error en la autenticación.';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
        }
    } else {
        $mensaje = 'Debes llenar todos los campos';
    }

    // Mostrar mensaje y redirigir
    echo "<div class='alert alert-danger' align='center'>" . $mensaje . "</div>";
    header("Location: $redirectUrl");
    exit(); // Asegúrate de llamar a exit() después de header()

    // Desconectar la base de datos
    $db->desconectarDB();
    ?>

    </div>
</body>
</html>

