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
    <div class="secc-sub-general " style="margin-bottom: 1em;" data-bs-toggle="collapse" data-bs-target="#cotizaciones<?php echo $id_cita; ?>">
        <p style="font-size: .9em;" class="bueld">ID Cita: <?php echo $id_cita; ?></p>
        <p style="margin-top:-.5em; font-size: 1.2em; text-transform: capitalize;"><mark class="marklued"><?php echo htmlspecialchars($cita['cliente']); ?></mark></p>
        <div id="cotizaciones<?php echo $id_cita; ?>" class="collapse">
            <div class="detalle">
                <?php foreach ($cita['detalles'] as $detalle): ?>
                    <?php foreach ($detalle as $key => $value): ?>
                        <?php if ($value !== null && $key !== 'id_cita' && $key !== 'nombre_cliente' && $key !== 'nombre_instalador' && $key !== 'id_detalle'): ?>
                            <p><?php echo ucfirst(str_replace('_', ' ', $key)); ?>: <?php echo htmlspecialchars($value); ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach;

$db->desconectarDB();
?>
