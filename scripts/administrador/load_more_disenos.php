<?php
include '../../class/database.php';
$conexion = new Database();
$conexion->conectarDB();

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$consulta = "
    SELECT
        disenos.id_diseno, disenos.file_path, disenos.codigo, disenos.estatus, productos.nombre as producto_nombre
    FROM 
        disenos
    LEFT JOIN
        disenos_productos ON disenos.id_diseno = disenos_productos.diseno
    LEFT JOIN
        productos ON disenos_productos.producto = productos.id_producto
    ORDER BY disenos.estatus DESC, disenos.id_diseno ASC
    LIMIT 15 OFFSET $offset;
";

$disenos = $conexion->seleccionar($consulta);

foreach($disenos as $diseno):
    $imagePath = !empty($diseno->file_path) ? '../../img/disenos/' . $diseno->file_path : '../../img/index/default.png';
?>
<tr class='clickable-row' data-id='<?= $diseno->id_diseno ?>'>
    <td>
        <img src='<?= htmlspecialchars($imagePath) ?>' alt='<?= $diseno->codigo ?>' style='width:100px;height:auto;'>
    </td>
    <td><?= $diseno->codigo ?></td>
    <td>
        <form id='form_<?= $diseno->id_diseno ?>' method='POST' action=''>
            <input type='hidden' name='id_diseno' value='<?= $diseno->id_diseno ?>'>
            <input type='hidden' name='nuevo_estatus' value='<?= $diseno->estatus == "activo" ? "inactivo" : "activo" ?>'>
            <button type='submit' class='btn <?= $diseno->estatus == "activo" ? "btn-success" : "btn-danger" ?> btn-sm mb-2 w-100 status-btn'>
                <i class='bi <?= $diseno->estatus == "activo" ? "bi-check" : "bi-x" ?>'></i>
            </button>
        </form>
    </td>
    <td><?= $diseno->producto_nombre ?? 'Sin producto'; ?></td>
</tr>
<?php
endforeach;
?>
