<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    header("Location: ../../views/iniciarSesion.php");
    exit();
}

include '../../class/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $rol = isset($_POST['rol']) ? $_POST['rol'] : null; // Verificación de 'rol'
    $rfc = $_POST['rfc'];
    $nss = $_POST['nss'];
    $curp = $_POST['curp'];
    
    $db = new database();
    $db->conectarDB();
    $db->configurarConexionPorRol();
    
    try {
        // Sacar el ID por el nombre de usuario
        $stmt = $db->getPDO()->prepare("SELECT id_usuario FROM usuarios WHERE nom_usuario = ?");
        $stmt->execute([$usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $result['id_usuario'];

        if (!$id_usuario) {
            throw new Exception('Usuario no encontrado');
        }

        // Depuración: Verificar los valores
        error_log("ID Usuario: $id_usuario");
        error_log("Rol: $rol");
        error_log("RFC: $rfc");
        error_log("NSS: $nss");
        error_log("CURP: $curp");

        // Llamar al procedimiento almacenado para actualizar datos laborales
        $stmt = $db->getPDO()->prepare("CALL actualizardatoslaborales(?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $rfc, $nss, $curp]);

        echo "Datos laborales actualizados correctamente.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        error_log("Error en actualizar datos laborales: " . $e->getMessage());
    }
    
    $db->desconectarDB();
}
