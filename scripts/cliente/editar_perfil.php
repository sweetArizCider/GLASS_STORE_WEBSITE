<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<?php
session_start();
include '../../class/database.php';

if (isset($_SESSION["nom_usuario"])) {
    $nombre_usuario_actual = $_SESSION["nom_usuario"];
    
    $db = new Database();
    $db->conectarDB();

    try {
        $cadena = "CALL obteneridusuariopornombre(:nombre_usuario)";
        $params = [':nombre_usuario' => $nombre_usuario_actual];
        $stmt = $db->ejecutarcita($cadena, $params);
        $id_usuario = $stmt->fetchColumn();
        $stmt->closeCursor();

        $nombre_usuario_nuevo = $_POST['nombre_usuario'];
        $nombres = $_POST['nombres'];
        $apellido_p = $_POST['apellido_p'];
        $apellido_m = $_POST['apellido_m'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $contrasena_actual = $_POST['contrasena_actual'] ?? null;
        $contrasena_nueva = $_POST['nueva_contrasena'] ?? null;
        $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? null;

        if ($contrasena_actual && $contrasena_nueva && $confirmar_contrasena) {
            $cadena = "SELECT contrasena FROM usuarios WHERE id_usuario = :id_usuario";
            $params = [':id_usuario' => $id_usuario];
            $stmt = $db->ejecutarcita($cadena, $params);
            $contrasena_db = $stmt->fetchColumn();
            $stmt->closeCursor();
            $contrasena_actual_hash = hash('sha256', $contrasena_actual);
            if (hash_equals($contrasena_db, $contrasena_actual_hash)) {
                if ($contrasena_nueva === $confirmar_contrasena) {
                    $contrasena_nueva_hash = hash('sha256', $contrasena_nueva);
                    $cadena = "CALL actualizarusuario(:id_usuario, :nombre_usuario, :nombres, :apellido_p, :apellido_m, :correo, :telefono, :contrasena)";
                    $params = [
                        ':id_usuario' => $id_usuario,
                        ':nombre_usuario' => $nombre_usuario_nuevo,
                        ':nombres' => $nombres,
                        ':apellido_p' => $apellido_p,
                        ':apellido_m' => $apellido_m,
                        ':correo' => $correo,
                        ':telefono' => $telefono,
                        ':contrasena' => $contrasena_nueva_hash
                    ];
                } else {
                    echo "<div class='alert alert-danger'>Las nuevas contraseñas no coinciden.</div>";
                    header("refresh:2; url=../../views/cliente/perfil.php");
                    exit;
                }
            } else {
                echo "<div class='alert alert-danger'>Contraseña actual incorrecta.</div>";
                header("refresh:2; url=../../views/cliente/perfil.php");
                exit;
            }
        } else {
            $cadena = "CALL actualizarusuario(:id_usuario, :nombre_usuario, :nombres, :apellido_p, :apellido_m, :correo, :telefono, NULL)";
            $params = [
                ':id_usuario' => $id_usuario,
                ':nombre_usuario' => $nombre_usuario_nuevo,
                ':nombres' => $nombres,
                ':apellido_p' => $apellido_p,
                ':apellido_m' => $apellido_m,
                ':correo' => $correo,
                ':telefono' => $telefono
            ];
        }

        $stmt = $db->ejecutarcita($cadena, $params);
        $stmt->closeCursor();

        $_SESSION["nom_usuario"] = $nombre_usuario_nuevo;
        echo "<div class='alert alert-success'>Perfil actualizado exitosamente.</div>";
        header("refresh:2; url=../../views/cliente/perfil.php");

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Hubo un error al actualizar el perfil: " . $e->getMessage() . "</div>";
        header("refresh:2; url=../../views/cliente/perfil.php");
    }

    $db->desconectarDB();
} else {
    header("Location: ../../views/cliente/iniciarSesion.php");
}
?>

</body>
</html>
