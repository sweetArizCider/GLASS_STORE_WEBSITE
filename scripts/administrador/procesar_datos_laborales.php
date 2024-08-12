<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    header("Location: ../../views/iniciarSesion.php");
    exit();
}

include '../../class/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $rfc = $_POST['rfc'];
    $nss = $_POST['nss'];
    $curp = $_POST['curp'];
    
    $db = new database();
    $db->conectarDB();
    
    try {
        // Obtener el ID del usuario por su nombre de usuario
        $stmt = $db->getPDO()->prepare("SELECT id_usuario FROM usuarios WHERE nom_usuario = ?");
        $stmt->execute([$usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $result['id_usuario'];

        if (!$id_usuario) {
            throw new Exception('Usuario no encontrado');
        }

        // Verificar si el usuario existe en la tabla persona
        $stmt = $db->getPDO()->prepare("SELECT id_persona FROM persona WHERE usuario = ?");
        $stmt->execute([$id_usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception('Persona no encontrada para el usuario proporcionado.');
        } else {
            $id_persona = $result['id_persona'];
        }

        // Verificar si el usuario ya tiene el rol de instalador
        $stmt = $db->getPDO()->prepare("SELECT id_rol_usuario FROM rol_usuario WHERE usuario = ? AND rol = (SELECT id_rol FROM roles WHERE nombre_rol = 'instalador')");
        $stmt->execute([$id_usuario]);
        $rolExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rolExistente) {
            // Actualizar los datos laborales del instalador
            $stmt = $db->getPDO()->prepare("CALL actualizardatoslaborales(?, ?, ?, ?)");
            $stmt->execute([$id_persona, $rfc, $nss, $curp]);
            
            $_SESSION['message'] = "Datos actualizados con éxito.";
        } else {
            // Insertar los datos como nuevo instalador y asignar el rol
            $stmt = $db->getPDO()->prepare("INSERT INTO instalador (persona, rfc, nss, curp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_persona, $rfc, $nss, $curp]);

            // Asignar el rol de instalador
            $stmt = $db->getPDO()->prepare("INSERT INTO rol_usuario (rol, usuario, estatus) VALUES ((SELECT id_rol FROM roles WHERE nombre_rol = 'instalador'), ?, 'activo')");
            $stmt->execute([$id_usuario]);

            $_SESSION['message'] = "Nuevo instalador registrado con éxito.";
        }

    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        error_log("Error en procesar datos laborales: " . $e->getMessage());
    }
    
    $db->desconectarDB();
    header("Location: ../../views/administrador/vista_admin_gestionainstalador.php");
    exit();
}
?>
