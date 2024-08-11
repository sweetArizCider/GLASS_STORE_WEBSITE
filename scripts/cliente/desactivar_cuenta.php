<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
session_start();
include '../../class/database.php';

echo "Contenido de la sesión: ";
print_r($_SESSION);
echo "<br>";

if (isset($_SESSION["nom_usuario"])) {
    $nombre_usuario_actual = $_SESSION["nom_usuario"];
    
    $db = new Database();
    $db->conectarDB();
    $db->configurarConexionPorRol();

    try {
        echo "Nombre de usuario actual: " . $nombre_usuario_actual . "<br>";

        $cadena = "CALL obteneridusuariopornombre(:nombre_usuario)";
        $params = [':nombre_usuario' => $nombre_usuario_actual];
        $stmt = $db->ejecutarcita($cadena, $params);
        $id_usuario = $stmt->fetchColumn();
        $stmt->closeCursor();

        echo "ID de usuario: " . $id_usuario . "<br>";
        
        if ($id_usuario) {
            $stmt = $db->ejecutarcita("SET @session_user_id = :id_usuario", [':id_usuario' => $id_usuario]);
            $stmt->closeCursor();

            $stmt = $db->ejecutarcita("CALL desactivarcuenta()");
            $resultado = $stmt->fetch();
            
            echo "Resultado: " . $resultado['mensaje'] . "<br>";
            $stmt->closeCursor();
            session_destroy();
            header("Location: ../../index.php");
            exit();
        } else {
            echo "Error: Usuario no encontrado.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $db->desconectarDB();
} else {
    echo "No hay sesión de usuario válida.";
}
?>

</body>
</html>