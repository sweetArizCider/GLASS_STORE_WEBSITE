<?php
include '../class/database.php';

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 8;

$db = new database();
$db->conectarDB();

$cadena = "SELECT * FROM vista_citas_detalles ORDER BY id_cita LIMIT $limit OFFSET $offset";
$resultado = $db->seleccionar($cadena);

$citas = array();
if ($resultado) {
    foreach ($resultado as $row) {
        $id_cita = $row->id_cita;
        if (!isset($citas[$id_cita])) {
            $citas[$id_cita]['cliente'] = $row->nombre_cliente ?: $row->nombre_instalador;
            $citas[$id_cita]['detalles'] = [];
        }
        $citas[$id_cita]['detalles'][] = $row;
    }
}

foreach ($citas as $id_cita => $cita): ?>
    <div class="card">
        <div class="card-header" id="heading<?php echo $id_cita; ?>">
            <h5 class="mb-0">
                <button class="btn btn-link accordion-button collapsed" data-toggle="collapse" data-target="#collapse<?php echo $id_cita; ?>" aria-expanded="false" aria-controls="collapse<?php echo $id_cita; ?>">
                    Cita <?php echo $id_cita; ?> - Cliente: <?php echo $cita['cliente']; ?>
                </button>
            </h5>
        </div>
        <div id="collapse<?php echo $id_cita; ?>" class="collapse" aria-labelledby="heading<?php echo $id_cita; ?>" data-parent="#accordion">
            <div class="card-body">
                <?php foreach ($cita['detalles'] as $detalle): ?>
                    <div class="detalle">
                        <?php foreach ($detalle as $key => $value): ?>
                            <?php if ($value !== null && $key !== 'id_cita' && $key !== 'nombre_cliente' && $key !== 'nombre_instalador' && $key !== 'id_detalle'): ?>
                                <p><?php echo ucfirst(str_replace('_', ' ', $key)); ?>: <?php echo $value; ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <br>
<?php endforeach;

$db->desconectarDB();
?>
