<?php
// Incluir el archivo de la clase de base de datos.
include '../../class/database.php';
session_start();

// Verificar si el formulario de búsqueda ha sido enviado.
if (isset($_POST['search'])) {
    // Obtener el término de búsqueda del formulario.
    $search = $_POST['search'];

    // Obtener el parámetro de ordenación (opcional).
    $orden = isset($_POST['orden']) ? $_POST['orden'] : 'recientes';

    // Crear una instancia de la clase database.
    $db = new database();
    $db->conectarDB();
    $db->configurarConexionPorRol();

    // Obtener el id del instalador de la sesión actual.
    $id_instalador = $_SESSION['id_instalador'];

    // Determinar el orden de las citas.
    $order_by = $orden === 'antiguas' ? 'ASC' : 'DESC';

    // Preparar la consulta SQL para obtener las citas del instalador.
    $citas = $db->getPDO()->prepare("
        SELECT c.id_cita, 
               c.fecha, 
               c.hora, 
               CONCAT(p.nombres, ' ', p.apellido_p, ' ', p.apellido_m) AS cliente,
               d.calle, 
               d.numero, 
               d.numero_int, 
               d.colonia, 
               d.ciudad, 
               d.referencias
        FROM citas c
        JOIN cliente_direcciones cd ON c.cliente_direccion = cd.id_cliente_direcciones
        JOIN persona p ON cd.cliente = p.id_persona
        JOIN direcciones d ON cd.direccion = d.id_direccion
        JOIN instalador_cita ic ON c.id_cita = ic.cita
        WHERE ic.instalador = :id_instalador
        AND (p.nombres LIKE :search 
             OR p.apellido_p LIKE :search 
             OR p.apellido_m LIKE :search)
        ORDER BY c.fecha $order_by, c.hora $order_by
    ");

    // Ejecutar la consulta con los parámetros.
    $citas->execute(['id_instalador' => $id_instalador, 'search' => '%' . $search . '%']);

    // Obtener los resultados de la consulta.
    $results = $citas->fetchAll(PDO::FETCH_OBJ);

    // Verificar si se obtuvieron resultados.
    if ($results) {
        // Iterar sobre los resultados y mostrar las citas.
        foreach ($results as $cita) {
            $fecha = date('d \d\e F \d\e Y', strtotime($cita->fecha));
            $hora = date('h:i A', strtotime($cita->hora));

            $cliente = $cita->cliente ?? 'Desconocido';
            $calle = $cita->calle ?? 'No disponible';
            $numero = $cita->numero ?? 'No disponible';
            $numero_int = $cita->numero_int ?? ''; // Puede estar vacío
            $colonia = $cita->colonia ?? 'No disponible';
            $ciudad = $cita->ciudad ?? 'No disponible';
            $referencias = $cita->referencias ?? 'No disponible';

            echo '<div class="secc-sub-general">';
            echo '<p class="fecha">' . $fecha . '</p>';
            echo '<p><mark class="marklued">' . htmlspecialchars($cliente) . '</mark><br> Requiere <span class="bueld">una Instalación</span> en el domicilio: <span class="bueld">' . htmlspecialchars($calle) . ' #' . htmlspecialchars($numero) . ' ' . htmlspecialchars($numero_int) . ', ' . htmlspecialchars($colonia) . ', ' . htmlspecialchars($ciudad) . ' referencias: ' . htmlspecialchars($referencias) . '</span> <br> el día <span class="bueld">' . $fecha . '</span> a las <span class="bueld">' . $hora . '</span></p>';
            echo '</div> <br>';
        }
    } else {
        echo '<p>No se encontraron citas.</p>';
    }
} else {
    echo '<p>No se proporcionó texto de búsqueda.</p>';
}
?>
