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
    echo '<div class="card">';
    echo '<div class="card-header" id="heading'.$id_cita.'">';
    echo '<h5 class="mb-0">';
    echo '<button class="btn btn-link accordion-button collapsed" data-toggle="collapse" data-target="#collapse'.$id_cita.'" aria-expanded="false" aria-controls="collapse'.$id_cita.'">';
    echo 'Cita '.$id_cita.' - Cliente: '.$cita['cliente'];
    echo '</button>';
    echo '</h5>';
    echo '</div>';
    echo '<div id="collapse'.$id_cita.'" class="collapse" aria-labelledby="heading'.$id_cita.'" data-parent="#accordion">';
    echo '<div class="card-body">';
    
    foreach ($cita['detalles'] as $detalle) {
        echo '<div class="detalle">';
        foreach ($detalle as $key => $value) {
            if ($value !== null && $key !== 'id_cita' && $key !== 'nombre_cliente' && $key !== 'nombre_instalador' && $key !== 'id_detalle') {
                echo '<p>'.ucfirst(str_replace('_', ' ', $key)).': '.$value.'</p>';
            }
        }
        echo '<hr>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div><br>';
}

$db->desconectarDB();
