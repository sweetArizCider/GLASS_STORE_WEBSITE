<?php
include '../class/database.php';  // Ajusta la ruta según sea necesario

$db = new database();
$db->conectarDB();

$search = isset($_GET['search']) ? $_GET['search'] : '';

$cadena = "SELECT * FROM vista_citas_detalles WHERE nombre_cliente LIKE :search OR id_cita LIKE :search ORDER BY id_cita";
$params = [':search' => '%' . $search . '%'];

$resultado = $db->seleccionar($cadena, $params);

$citas = array();
if ($resultado) {
    foreach ($resultado as $row) {
        $citas[$row->id_cita]['cliente'] = $row->nombre_cliente;
        $citas[$row->id_cita]['detalles'][] = $row;
    }
}

foreach ($citas as $id_cita => $cita) {
    echo '<div class="secc-sub-general" style="margin-bottom: 1em;" data-bs-toggle="collapse" data-bs-target="#cotizaciones'.$id_cita.'">';
    echo '<p style="font-size: .9em;" class="bueld">ID Cita: '.$id_cita.'</p>';
    echo '<p style="margin-top:-.5em; font-size: 1.2em; text-transform: capitalize;"><mark class="marklued">'.htmlspecialchars($cita['cliente']).'</mark></p>';
    echo '<div id="cotizaciones'.$id_cita.'" class="collapse">';
    echo '<div class="detalle">';

    foreach ($cita['detalles'] as $detalle) {
        foreach ($detalle as $key => $value) {
            if ($value !== null && $key !== 'id_cita' && $key !== 'nombre_cliente' && $key !== 'nombre_instalador' && $key !== 'id_detalle') {
                echo '<p>'.ucfirst(str_replace('_', ' ', $key)).': '.htmlspecialchars($value).'</p>';
            }
        }
        echo '<hr>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
}

$db->desconectarDB();
