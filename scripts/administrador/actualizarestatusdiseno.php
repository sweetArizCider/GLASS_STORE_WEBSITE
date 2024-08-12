<?php
include '../../class/database.php'; 
$conexion = new Database();
$conexion->conectarDB();

$id_diseno = $_POST['id_diseno'];
$nuevo_estatus = $_POST['nuevo_estatus'];

$sql = "UPDATE disenos SET estatus = '$nuevo_estatus' WHERE id_diseno = $id_diseno";
$resultado = $conexion->ejecutar($sql);

if ($resultado) {
    echo "Estatus actualizado";
} else {
    echo "Error al actualizar el estatus";
}
?>
