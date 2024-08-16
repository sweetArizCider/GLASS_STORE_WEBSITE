<?php
session_start();
include '../class/database.php';

if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    try {
        $db = new database();
        $db->conectarDB();
        $pdo = $db->getPDO();

        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        // Llamar al procedimiento almacenado de autenticación
        $stmt = $pdo->prepare("CALL autenticacion1(:usuario, :contrasena)");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cerrar el cursor para liberar la conexión para nuevas consultas
        $stmt->closeCursor();

        if ($resultado) {
            // Autenticación exitosa, almacenar la sesión del usuario
            $_SESSION['nom_usuario'] = $usuario;

            // Recuperar el ID del usuario autenticado
            $id_usuario = $resultado['id_usuario'];

            // Verificar si el usuario tiene cotizaciones como invitado
            if (isset($_SESSION['invitado_id'])) {
                $invitado_id = $_SESSION['invitado_id'];

                // Asignar las cotizaciones del invitado al usuario autenticado
                $updateStmt = $pdo->prepare("
                    UPDATE detalle_producto
                    SET cliente = :id_usuario, invitado_id = NULL
                    WHERE invitado_id = :invitado_id
                ");
                $updateStmt->bindParam(':id_usuario', $id_usuario);
                $updateStmt->bindParam(':invitado_id', $invitado_id);
                $updateStmt->execute();

                // Limpiar la sesión del invitado
                unset($_SESSION['invitado_id']);
            }

            // Redirigir al inicio o a la página principal
            header("Location: ../index.php");
            exit();
        } else {
            // Autenticación fallida
            header("Location: ../views/iniciarSesion.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Manejo de errores
        header("Location: ../views/iniciarSesion.php");
    }
} else {
    // Datos incompletos
    header("Location: ../views/iniciarSesion.php?error=2");
    exit();
}
?>
