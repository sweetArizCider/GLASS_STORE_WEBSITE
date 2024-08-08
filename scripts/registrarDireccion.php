<?php 
include '../class/database.php';

session_start();  // Asegúrate de iniciar la sesión

// Verificar que el usuario esté en la sesión
if (!isset($_SESSION['nom_usuario']) || empty($_SESSION['nom_usuario'])) {
    die("Usuario no encontrado en la sesión.");
}

$usuario = $_SESSION['nom_usuario'];

// Conectar a la base de datos
$db = new Database();
$db->conectarDB();

// Verificar si la conexión se estableció correctamente
$pdo = $db->getPDO();
if ($pdo === null) {
    die("Error de conexión a la base de datos.");
}

// Sanitizar y validar entradas
$calle = filter_input(INPUT_POST, 'calle', FILTER_SANITIZE_STRING);
$numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
$numero_int = filter_input(INPUT_POST, 'numero_int', FILTER_SANITIZE_STRING);
$colonia = filter_input(INPUT_POST, 'colonia', FILTER_SANITIZE_STRING);
$ciudad = filter_input(INPUT_POST, 'ciudad', FILTER_SANITIZE_STRING);
$referencias = filter_input(INPUT_POST, 'referencias', FILTER_SANITIZE_STRING);

// Convertir a entero o NULL si está vacío
$numero = !empty($numero) ? (int)$numero : NULL;
$numero_int = !empty($numero_int) ? (int)$numero_int : NULL;

/* Validar que se están obteniendo las variables correctamente
echo "<pre>";
print_r(compact('usuario', 'calle', 'numero', 'numero_int', 'colonia', 'ciudad', 'referencias'));
echo "</pre>"; */

// Preparar y ejecutar la consulta
$sql = "CALL creardireccion(:usuario, :calle, :numero, :numero_int, :colonia, :ciudad, :referencias)";
$stmt = $pdo->prepare($sql);

$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->bindParam(':calle', $calle);
$stmt->bindParam(':numero', $numero, PDO::PARAM_INT);
$stmt->bindParam(':numero_int', $numero_int, PDO::PARAM_INT);
$stmt->bindParam(':colonia', $colonia);
$stmt->bindParam(':ciudad', $ciudad);
$stmt->bindParam(':referencias', $referencias);

/* Mostrar la consulta con los valores reemplazados
$executed_query = str_replace(
    [':usuario', ':calle', ':numero', ':numero_int', ':colonia', ':ciudad', ':referencias'],
    [$usuario, $calle, $numero, $numero_int, $colonia, $ciudad, $referencias],
    $sql
); 


echo "<pre>";
echo "Consulta ejecutada: " . $executed_query;
echo "</pre>";
*/


try {
    $stmt->execute();
    echo "<div class='alert alert-success mt-3'>Dirección añadida</div>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$db->desconectarDB();

header("refresh:3; ../views/citas.php");  // Redirige
?>
