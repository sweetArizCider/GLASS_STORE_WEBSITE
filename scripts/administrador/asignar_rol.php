<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    header("Location: ../../views/iniciarSesion.php");
    exit();
}

include '../../class/database.php';

$db = new database();
$db->conectarDB();

$nom_usuario = $_POST['nom_usuario'];
$nombre_rol = $_POST['nombre_rol'];

try {
    // Obtener ID del usuario
    $stmt = $db->getPDO()->prepare("SELECT id_usuario FROM usuarios WHERE nom_usuario = ?");
    $stmt->execute([$nom_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$usuario) {
        echo "<script>alert('Usuario no encontrado.'); window.location.href = '../../views/administrador/vista_admin_darRol.php';</script>";
        exit();
    }

    // Obtener ID del rol
    $stmt = $db->getPDO()->prepare("SELECT id_rol FROM roles WHERE nombre_rol = ?");
    $stmt->execute([$nombre_rol]);
    $rol = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$rol) {
        echo "<script>alert('Rol no encontrado.'); window.location.href = '../../views/administrador/vista_admin_darRol.php';</script>";
        exit();
    }

    // Verificar si el usuario ya tiene el rol asignado
    $stmt = $db->getPDO()->prepare("SELECT * FROM rol_usuario WHERE usuario = ? AND rol = ?");
    $stmt->execute([$usuario->id_usuario, $rol->id_rol]);
    $rol_usuario = $stmt->fetch(PDO::FETCH_OBJ);

    if ($rol_usuario) {
        echo "<script>alert('El usuario ya tiene este rol asignado.'); window.location.href = '../../views/administrador/vista_admin_darRol.php';</script>";
        exit();
    }

    // Asignar el rol al usuario
    $stmt = $db->getPDO()->prepare("INSERT INTO rol_usuario (rol, usuario, estatus) VALUES (?, ?, 'activo')");
    $stmt->execute([$rol->id_rol, $usuario->id_usuario]);

    echo "<script>alert('Rol asignado exitosamente.'); window.location.href = '../../views/administrador/vista_admin_darRol.php';</script>";
} catch (Exception $e) {
    error_log("Error al asignar el rol: " . $e->getMessage());
    echo "<script>alert('Error al asignar el rol.'); window.location.href = '../../views/administrador/vista_admin_darRol.php';</script>";
}
