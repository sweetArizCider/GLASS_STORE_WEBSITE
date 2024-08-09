<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    header("Location: ../../views/iniciarSesion.php");
    exit();
}

include '../../class/database.php';

$db = new database();
$db->conectarDB();

$usuario = $_POST['usuario'];
$rfc = $_POST['rfc'];
$nss = $_POST['nss'];
$curp = $_POST['curp'];

try {
    // Verificar si el usuario existe
    $stmt = $db->getPDO()->prepare("SELECT id_usuario FROM usuarios WHERE nom_usuario = ?");
    $stmt->execute([$usuario]);
    $usuarioData = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$usuarioData) {
        throw new Exception('Usuario no encontrado.');
    }

    // Obtener la persona asociada al usuario
    $stmt = $db->getPDO()->prepare("SELECT id_persona FROM persona WHERE usuario = ?");
    $stmt->execute([$usuarioData->id_usuario]);
    $persona = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$persona) {
        throw new Exception('Persona asociada al usuario no encontrada.');
    }

    // Verificar si la persona es un instalador
    $stmt = $db->getPDO()->prepare("SELECT id_instalador FROM instalador WHERE persona = ?");
    $stmt->execute([$persona->id_persona]);
    $instalador = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$instalador) {
        throw new Exception('El usuario ingresado no es un instalador.');
    }

    // Intentar actualizar los datos del instalador
    $stmt = $db->getPDO()->prepare("
        UPDATE instalador 
        SET rfc = ?, nss = ?, curp = ?
        WHERE id_instalador = ?
    ");
    $stmt->execute([$rfc, $nss, $curp, $instalador->id_instalador]);

    echo "<script>alert('Datos laborales actualizados correctamente.'); window.location.href = '../../views/administrador/vista_admin_gestionainstalador.php';</script>";
    exit();
} catch (PDOException $e) {
    // Capturar errores de la base de datos, como duplicidad o formato incorrecto
    $errorCode = $e->getCode();
    $message = 'Datos Invalidos, por favor intente nuevamente';
    if ($errorCode == '23000') { // Código de error para violaciones de unicidad
        if (strpos($e->getMessage(), 'rfc') !== false) {
            $message = 'El RFC ingresado ya está en uso. Por favor, ingrese uno diferente.';
        } elseif (strpos($e->getMessage(), 'nss') !== false) {
            $message = 'El NSS ingresado ya está en uso. Por favor, ingrese uno diferente.';
        } elseif (strpos($e->getMessage(), 'curp') !== false) {
            $message = 'El CURP ingresado ya está en uso. Por favor, ingrese uno diferente.';
        }
    }
    error_log("Error en la base de datos: " . $e->getMessage());
    echo "<script>alert('$message'); window.location.href = '../../views/administrador/vista_admin_gestionainstalador.php';</script>";
    exit();
} catch (Exception $e) {
    error_log("Error al procesar los datos: " . $e->getMessage());
    echo "<script>alert('Error: " . htmlspecialchars($e->getMessage()) . "'); window.location.href = '../../views/administrador/vista_admin_gestionainstalador.php';</script>";
    exit();
}
?>