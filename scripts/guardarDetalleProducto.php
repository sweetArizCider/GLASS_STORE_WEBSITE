<?php
session_start();
include '../class/database.php';

$db = new database();
$db->conectarDB();
$db->configurarConexionPorRol();
$pdo = $db->getPDO();

if (isset($_POST['producto'], $_POST['alto'], $_POST['ancho'], $_POST['cantidad'], $_POST['total'])) {
    $producto = $_POST['producto'];
    $alto = $_POST['alto'];
    $ancho = $_POST['ancho'];
    $cantidad = $_POST['cantidad'];
    $total = $_POST['total'];
    $diseno = isset($_POST['diseno']) ? $_POST['diseno'] : null;
    $color_accesorios = isset($_POST['color_accesorios']) ? $_POST['color_accesorios'] : null;

    if (isset($_SESSION["nom_usuario"])) {
        $user = $_SESSION["nom_usuario"];

        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $params_ids = [$user];
        $resultado_ids = $db->seleccionar($consulta_ids, $params_ids);

        if ($resultado_ids && !empty($resultado_ids)) {
            $id_cliente = $resultado_ids[0]->id_cliente;

            $stmt = $pdo->prepare("
                INSERT INTO detalle_producto (producto, cliente, alto, largo, cantidad, marco, tipo_cadena, diseno)
                VALUES (:producto, :cliente, :alto, :ancho, :cantidad, :marco, :tipo_cadena, :diseno)
            ");
            $stmt->bindParam(':producto', $producto);
            $stmt->bindParam(':cliente', $id_cliente);
            $stmt->bindParam(':alto', $alto);
            $stmt->bindParam(':ancho', $ancho);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':marco', $color_accesorios);
            $stmt->bindParam(':tipo_cadena', $color_accesorios);
            $stmt->bindParam(':diseno', $diseno);

            if ($stmt->execute()) {
                echo "Detalle del producto guardado exitosamente.";
                header("refresh:2; ../views/productos.php");
            } else {
                echo "Error al guardar el detalle del producto.";
            }
        } else {
            echo "ID de cliente no encontrado.";
        }
    } else {
        echo "Usuario no autenticado.";
    }
} else {
    echo "Datos incompletos.";
}
?>
