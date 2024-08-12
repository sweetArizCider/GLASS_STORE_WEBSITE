<?php
session_start();

if (!isset($_SESSION["nom_usuario"])) {
    error_log("Usuario no autenticado. Redirigiendo a iniciarSesion.php");
    header("Location: ../../views/iniciarSesion.php");
    exit();
}

include '../../class/database.php';

$db = new Database();
$db->conectarDB();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo_diseno = $_POST['codigo_diseno'] ?? null;
    $id_producto = $_POST['id_producto'] ?? null;

    if ($codigo_diseno && $id_producto) {
        try {
            // Llamar al procedimiento almacenado para agregar el diseño
            $stmt = $db->getPDO()->prepare("CALL agregar_diseno_a_producto(:codigo_diseno, :id_producto)");
            $stmt->bindParam(':codigo_diseno', $codigo_diseno, PDO::PARAM_STR);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            header("Location: ../../views/administrador/vista_admin_productos.php?status=success&message=Design added successfully");
        } catch (PDOException $e) {
            error_log("Error al agregar diseño: " . $e->getMessage());
            header("Location: ../../views/administrador/vista_admin_productos.php?status=error&message=" . urlencode($e->getMessage()));
        }
    } else {
        header("Location: ../../views/administrador/vista_admin_productos.php?status=error&message=Invalid design code or product ID");
    }
} else {
    header("Location: ../../views/administrador/vista_admin_productos.php?status=error&message=Invalid request method");
}

$db->desconectarDB();
