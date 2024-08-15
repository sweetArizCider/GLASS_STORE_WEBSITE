<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../class/database.php';

$id_usuario = 0;
$notificaciones = [];

// Crear conexión a la base de datos
$db = new database();
$db->conectarDB();
$pdo = $db->getPDO();

if ($pdo === null) {
    die("Error al conectar a la base de datos.");
}

if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Consulta para obtener el rol del usuario basado en el nombre de usuario
    $consulta_rol = "CALL roles_usuario(?)";
    $params_rol = [$user];
    $resultado_rol = $db->seleccionar($consulta_rol, $params_rol);

    if (!$resultado_rol) {
        die("Error al obtener el rol del usuario.");
    }

    $nombre_rol = $resultado_rol[0]->nombre_rol;

    // Consulta para obtener los IDs del cliente e instalador basado en el nombre de usuario
    $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
    $params_ids = [$user];
    $resultado_ids = $db->seleccionar($consulta_ids, $params_ids);

    if (!$resultado_ids) {
        die("Error al obtener los IDs.");
    }

    $fila = $resultado_ids[0];

    if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
        $id_cliente = $fila->id_cliente;
        $id_usuario = $id_cliente;

        $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_cliente WHERE cliente = ? order by fecha desc";
        $paramsNotificaciones = [$id_cliente];
        $notificaciones = $db->seleccionar($consultaNotificaciones, $paramsNotificaciones);

    } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
        $id_instalador = $fila->id_instalador;
        $id_usuario = $id_instalador;

        $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_instalador WHERE instalador = ?";
        $paramsNotificaciones = [$id_instalador];
        $notificaciones = $db->seleccionar($consultaNotificaciones, $paramsNotificaciones);
    }
}

// Verificar si el ID del producto está presente en la URL
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Consultar los detalles del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = :id");
    if ($stmt) {
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_OBJ);

        if ($producto) {
            // Consultar imágenes del producto
            $stmtImg = $pdo->prepare("SELECT * FROM imagen WHERE producto = :id");
            $stmtImg->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtImg->execute();
            $imagenes = $stmtImg->fetchAll(PDO::FETCH_OBJ);

            // Consultar la categoría del producto
            $stmtCategory = $pdo->prepare("
                SELECT c.nombre
                FROM categorias c
                JOIN productos p ON c.id_categoria = p.categoria
                WHERE p.id_producto = :id
            ");
            $stmtCategory->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtCategory->execute();
            $categoria = $stmtCategory->fetchColumn();

            // Consultar diseños del producto
            $stmtDisenos = $pdo->prepare("SELECT id_diseno, codigo, file_path FROM disenos WHERE muestrario = :id");
            $stmtDisenos->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtDisenos->execute();
            $disenos = $stmtDisenos->fetchAll(PDO::FETCH_OBJ);

        } else {
            echo "Producto no encontrado.";
            exit;
        }
    } else {
        echo "Error al preparar la consulta del producto.";
        exit;
    }
} else {
    echo "ID de producto no proporcionado.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Glass Store</title>
<link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/normalized.css">
<link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">

<style>
    .card-img-left {
    border-radius: 10px;
}

.card {
    border-radius: 10px;
    margin-bottom: 20px;
}
@media (max-width: 768px) {
    .buttonPerfilProductocancelar{
        margin-left: 0 !important;
    }

}
</style>
</head>

<body>
<!-- whatsapp flotante -->
<div id="wa-button">
    <a href="https://api.whatsapp.com/send?phone=528717843809" target="_blank">
      <img src="../img/index/whatsappFloat.svg" alt="Contáctanos por WhatsApp">
    </a>
  </div>
     <!-- barra superior -->
     <div class="container blue">
      <div class="navbar-top">
          <div class="social-link">
              <a href="https://api.whatsapp.com/send?phone=528717843809" target="_blank" ><img src="../img/index/whatsapp.svg" alt="" width="30px"></a>
              <a href="https://www.facebook.com/profile.php?id=100064181314031" target="_blank"><img src="../img/index/facebook.svg" alt="" width="30px"></a>
              <a href="https://www.instagram.com/glassstoretrc?igsh=MXVhdHh1MDVhOGxzeA==" target="_blank"><img src="../img/index/instagram.svg" alt="" width="30px"></a>
          </div>

          <div class="logo">
              <img src="../img/index/GLASS.png" alt="" class="logo">
          </div>
          <div class="icons">
                <a href="../index.php"><img src="../img/index/inicio.svg" alt="" width="25px"></a>
                <button class="botonMostrarFavoritos" data-bs-toggle="modal" data-bs-target="#favoritosModal"><img src="../img/index/favorites.svg" alt="" width="25px"></button>
               
                <div class="dropdown">
    <a href="#" id="user-icon" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="../img/index/user.svg" alt="" width="25px" style="cursor: pointer">
    </a>
    <?php if (isset($_SESSION["nom_usuario"])): ?>
        <ul class="dropdown-menu" aria-labelledby="user-icon">
            <li class="dropdown-item" style="color: #6c757d; font-size: .8em; pointer-events: none; cursor: default;"> <!-- Estilo del nombre de usuario en gris claro -->
                <?php echo htmlspecialchars($_SESSION["nom_usuario"]); ?>
            </li>
            <li><a class="dropdown-item" href="../views/cliente/perfil.php">Perfil</a></li>
           
            <?php
            $user = $_SESSION["nom_usuario"];
            $consulta = "CALL roles_usuario(?)";
            $params = [$user];
            $roles = $db->seleccionar($consulta, $params);
            if ($roles) {
                foreach ($roles as $rol) {
                    if ($rol->nombre_rol == 'administrador') {
                        echo '<li><a class="dropdown-item" href="../views/administrador/vista_admin.php">Administrador</a></li>';
                    } elseif ($rol->nombre_rol == 'instalador') {
                        echo '<li><a class="dropdown-item" href="../views/instalador/index_Instalador.php">Buzón</a></li>';
                    }
                }
            }
            ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../scripts/cerrarSesion.php">Cerrar Sesión</a></li>
        </ul>
    <?php else: ?>
        <ul class="dropdown-menu" aria-labelledby="user-icon">
            <li><a class="dropdown-item" href="../views/iniciarSesion.php">Iniciar Sesión</a></li>
        </ul>
    <?php endif; ?>
</div>

            </div>
        </div>
    </div> 

  <!-- segunda barra -->
  <nav class="navbar sticky-top navbar-expand-md" id="navbar-color">
    <div class="container-fluid">
        <!-- menú hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
            <span><i><img src="../img/index/menu.svg" alt="Menu" width="30px"></i></span>
        </button>
        <div class="offcanvas offcanvas-start" id="offcanvasNavbar">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menú</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link"  href="./productos.php">Volver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-left" href="https://api.whatsapp.com/send?phone=528717843809" target="_blank">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-left" href="./citas.php">Agendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-left" href="/#about-us">Nosotros</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-3 d-flex justify-content-end">
</div>
 <div class="container mt-5" id="product-profile">
    <div class="row">
         <div class="col-md-6">
         <!-- Imagenes del producto -->
            <div id="carouselProductImages carouselImgInd" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner main-img">
                    <?php
                        if (!empty($imagenes)) {
                            $first = true;
                            foreach ($imagenes as $img) {
                                $imagePath = !empty($img->imagen) ? '../img/disenos/' . $img->imagen : '../img/index/default.png';
                                $activeClass = $first ? 'active' : '';
                                echo "<div class='carousel-item $activeClass'>
                                    <img src='" . htmlspecialchars($imagePath) . "' class='img-fluid h-100' alt='Imagen del Producto' style='object-fit: cover;'>
                                    </div>";
                                $first = false;
                             }
                         } else {
                                echo "<div class='carousel-item active'>
                                    <img src='../img/index/default.png' class='img-fluid' alt='Imagen no disponible'>
                                    </div>";
                         }
                    ?>
                </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProductImages" data-bs-slide="prev">
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProductImages" data-bs-slide="next">
                        <span class="visually-hidden">Next</span>
                    </button>
            </div>
            <div class="sub-imgs">
                <?php
                if (!empty($imagenes)) {
                    foreach ($imagenes as $img) {
                        $imagePath = !empty($img->imagen) ? '../img/disenos/' . $img->imagen : '../img/index/default.png';
                        echo "<div class='container-sub-img'>
                                <img src='" . htmlspecialchars($imagePath) . "' class='sub-img' alt='Imagen del Producto'>
                              </div>";
                    }
                } else {
                    echo "<div class='container-sub-img'>
                            <img src='../img/index/default.png' class='sub-img' alt='Imagen no disponible'>
                          </div>";
                }
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Información del producto -->
            <h1 class="titleProductPerfil"><?php echo htmlspecialchars($producto->nombre); ?></h1>
            <?php
  if ($producto->categoria == 3) {
    echo '<h2 class="precioPerfil">$' . number_format($producto->precio, 2) . ' MXN p/Rollo</h2>';
}elseif (strpos(strtolower($producto->nombre), 'pasamanos') !== false) {
    echo '<h2 class="precioPerfil">$' . number_format($producto->precio, 2) . ' MXN m</h2>';
} else {
    echo '<h2 class="precioPerfil">$' . number_format($producto->precio, 2) . ' MXN m<sup>2</sup></h2>';
}
?>

            <p class="descripcionPerfil"><?php echo htmlspecialchars($producto->descripcion); ?></p>
           
            <?php if (!empty($disenos)) : ?>
                <h5 class="disenosPerfil">Diseños disponibles:</h5>
                <div class="design-gallery">
                    <?php foreach ($disenos as $diseno) : ?>
                        <div class="design-item">
                            <img src="../img/disenos/<?php echo htmlspecialchars($diseno->file_path); ?>" 
                                 alt="<?php echo htmlspecialchars($diseno->codigo); ?>" 
                                 class="design-image" 
                                 data-id="<?php echo $diseno->id_diseno; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
           
            <div id="cotizacionContainer" class="mt-5">
    <form id="cotizacionForm" method="POST" action="../scripts/guardarDetalleProducto.php" class="formPerfil">
        <input type="hidden" name="producto" value="<?php echo $productId; ?>">
        <input type="hidden" name="diseno" id="diseno">
        <div class="input-group">
            <?php if (strpos(strtolower($producto->nombre), 'pasamanos') !== false) : ?>
                <div class="inputPerfil mb-3">
                    <label class="labelPerfilProduct" for="largo" class="form-label">Largo (metros)</label>
                    <input type="number" class="form-control inputPerfilProductoCont" id="largo" name="largo" step="0.01" max="50" required>
                </div>
            <?php else : ?>
                <div class="inputPerfil mb-3">
                    <label class="labelPerfilProduct" for="alto" class="form-label">Alto (metros)</label>
                    <input type="number" class="form-control inputPerfilProductoCont" id="alto" name="alto" step="0.01" max="10" required>
                </div>
                <div class="inputPerfil mb-3">
                    <label class="labelPerfilProduct" for="ancho" class="form-label">Ancho (metros)</label>
                    <input type="number" class="form-control inputPerfilProductoCont" id="ancho" name="ancho" step="0.01" max="10" required>
                </div>
            <?php endif; ?>
            <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control inputPerfilProductoCont" id="cantidad" name="cantidad" max="10" required>
            </div>
            <div class="inputPerfil total mb-3">
                <label class="labelPerfilProduct" for="total" class="form-label">Precio Total</label>
                <input type="text" class="form-control inputPerfilProductoCont total" id="total" name="total" readonly>
            </div>
            <?php if ($categoria === 'persianas') : ?>
            <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="color_accesorios">Color de Accesorios</label>
                <select  name="color_accesorios" id="color_accesorios" class="form-control inputPerfilProductoCont">
                    <option value="blanco">Blanco</option>
                    <option value="negro">Chocolate</option>
                    <option value="gris">Ivory</option>
                    <!-- Añadir más opciones según sea necesario -->
                </select>
                
            </div>
        <?php endif; ?>
        </div>

        <div class="d-grid d-md-flex justify-content-md-between">
            <button type="submit" class="buttonPerfilProducto mb-2 mb-md-0" style="width: 100%; max-width: 100%;">Guardar Cotización</button>
            <a href="./productos.php" class="buttonPerfilProductocancelar" style="max-width: 100%; margin-left:1em; text-decoration: none; text-align: center;">Cancelar</a>
        </div>
        
    </form>
    <p class="text-muted mt-2 text-center" style="font-size: 0.6rem; margin-top:1px;padding-left:20px; padding-right:20px;">
        Este es un simulador de cotización. Los precios mostrados son aproximados y están sujetos a cambios y modificaciones. Lamentamos cualquier inconveniente que esto pueda causar. ¡Gracias por su comprensión!. Para más información  <a href="https://api.whatsapp.com/send?phone=528717843809" target="_blank">contáctate.</a>
    </p>
</div>
        </div>
    </div>
</div>

<!-- modal notificaciones-->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notificaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                if (!empty($notificacionesRecientes)) {
                    foreach ($notificacionesRecientes as $notif) {
                        echo '<div class="notification">';
                        echo '<mark><p>' . htmlspecialchars($notif->notificacion) . '</p></mark>';
                        echo '<small>' . htmlspecialchars($notif->fecha) . '</small>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No tienes notificaciones recientes.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Favoritos -->
<div class="modal fade" id="favoritosModal" tabindex="-1" aria-labelledby="favoritosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="favoritosModalLabel">Mis Favoritos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_SESSION["nom_usuario"])): ?>
                    <!-- Usuario logueado -->
                    <p class="text-center">Guarda tus <a href="./productos.php">productos</a> favoritos y accede a ellos en cualquier momento.</p>
                    <div id="favoritos-list" class="row">
                        <!-- Aquí se cargarán los productos favoritos -->
                    </div>
                <?php else: ?>
                    <!-- Usuario no logueado -->
                    <div class="text-center">
                        <p><a href="../views/iniciarSesion.php">Inicia sesión</a> para guardar tus productos favoritos y acceder a ellos cuando quieras. ¡ <a href="../views/register.php">Crea tu cuenta</a> y disfruta de una experiencia personalizada!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const altoInput = document.getElementById('alto');
    const anchoInput = document.getElementById('ancho');
    const largoInput = document.getElementById('largo');
    const cantidadInput = document.getElementById('cantidad');
    const totalInput = document.getElementById('total');
    const disenoInput = document.getElementById('diseno');
    const precioPorMetroCuadrado = <?php echo $producto->precio; ?>;

    function actualizarPrecioTotal() {
        let total = 0;
        let cantidad = parseInt(cantidadInput.value) || 0;

        if (largoInput) {
            let largo = parseFloat(largoInput.value) || 0;
            largo = Math.max(0, largo);
            largo = largo > 50 ? 50 : largo;
            cantidad = cantidad > 10 ? 10 : cantidad;
            largoInput.value = largo;
            cantidadInput.value = cantidad;
            total = largo * precioPorMetroCuadrado * cantidad;
        } else {
            let alto = parseFloat(altoInput.value) || 0;
            let ancho = parseFloat(anchoInput.value) || 0;
            alto = Math.max(0, alto);
            ancho = Math.max(0, ancho);
            cantidad = Math.max(0, cantidad);
            alto = alto > 50 ? 50 : alto;
            ancho = ancho > 50 ? 50 : ancho;
            cantidad = cantidad > 10 ? 10 : cantidad;
            altoInput.value = alto;
            anchoInput.value = ancho;
            cantidadInput.value = cantidad;
            const metrosCuadrados = alto * ancho;
            total = metrosCuadrados * precioPorMetroCuadrado * cantidad;

            if ("<?php echo $categoria; ?>" === "tapices") {
                let metrostapiz= metrosCuadrados*cantidad;
                if (metrostapiz <= 5) {
                    total = precioPorMetroCuadrado;
                } else {
                    
                    let bloques = Math.ceil(metrostapiz / 5);
                    total = bloques * precioPorMetroCuadrado ;
                }
            } else {
                total = metrosCuadrados * precioPorMetroCuadrado * cantidad;
            }
        }

        totalInput.value = total.toFixed(2) + ' MXN';
    }

    function validarInput(event) {
        let value = event.target.value;
        if (parseFloat(value) < 0) {
            value = 0;
        }
        const partes = value.split('.');
        if (partes.length > 1 && partes[1].length > 2) {
            partes[1] = partes[1].slice(0, 2);
            value = partes.join('.');
        }
        event.target.value = value;
        actualizarPrecioTotal();
    }

    if (altoInput) altoInput.addEventListener('input', validarInput);
    if (anchoInput) anchoInput.addEventListener('input', validarInput);
    if (largoInput) largoInput.addEventListener('input', validarInput);
    cantidadInput.addEventListener('input', validarInput);

    document.querySelectorAll('.design-image').forEach(function(image) {
        image.addEventListener('click', function() {
            document.querySelectorAll('.design-image').forEach(function(img) {
                img.classList.remove('selected');
            });
            image.classList.add('selected');
            disenoInput.value = image.getAttribute('data-id');
        });
    });
});


$(document).ready(function() {
    $('#favoritosModal').on('shown.bs.modal', function () {
        cargarFavoritos();
    });
    
    function cargarFavoritos() {
        $.ajax({
            url: '../scripts/obtener_favoritos.php',
            method: 'GET',
            dataType: 'json',
            success: function(favoritos) {
                var favoritosList = $('#favoritos-list');
                favoritosList.empty();
                if (favoritos.length > 0) {
                    favoritos.forEach(function(favorito) {
                        var imagen = favorito.imagen ? '../img/disenos/' + favorito.imagen : '../img/index/default.png';
                        var favoritoHtml = `
                            <div class='col-md-3 mt-3 py-3 py-md-0 product-item'>
                                <div class='card shadow'>
                                    <a href='./perfilProducto.php?id=${favorito.id_producto}' style='text-decoration: none; color: inherit;'>
                                        <img src='${imagen}' alt='${favorito.nombre}' class='card-img-top'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>${favorito.nombre}</h5>
                                            <p class='card-text'>$ ${favorito.precio}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>`;
                        favoritosList.append(favoritoHtml);
                    });
                } 
            },
            error: function(error) {
                console.error('Error al obtener los favoritos:', error);
                $('#favoritos-list').append("<p>Error al cargar los favoritos.</p>");
            }
        });
    }
});

 // Cargar carrito cuando el modal es mostrado
 $(document).ready(function() {
        $('#carritoModal').on('shown.bs.modal', function () {
            cargarCarrito();
        });

        $('#aceptar-btn').on('click', function() {
            actualizarEstadoProductos();
        });

        function cargarCarrito() {
    $.ajax({
        url: '../scripts/obtener_carrito.php',
        method: 'GET',
        dataType: 'json',
        success: function(carrito) {
            var carritoList = $('#carrito-list');
            carritoList.empty();
            if (carrito.length > 0) {
                carrito.forEach(function(item) {
                    var imagen = item.imagen_producto ? '../img/disenos/' + item.imagen_producto : '../img/disenos/default.png';

                    // Concatenar las propiedades en una sola línea
                    var descripcion = [];
                    if (item.alto) descripcion.push('Alto: ' + item.alto);
                    if (item.largo) descripcion.push('Largo: ' + item.largo);
                    if (item.cantidad) descripcion.push('Cantidad: ' + item.cantidad);
                    if (item.monto) descripcion.push('Monto: ' + item.monto);
                    if (item.grosor) descripcion.push('Grosor: ' + item.grosor);
                    if (item.codigo_diseno) descripcion.push('Diseño: ' + item.codigo_diseno);
                    if (item.marco) descripcion.push('Accesorios: ' + item.marco);
                    if (item.monto) descripcion.push('Monto: $' + item.monto);

                    
                    
                    var descripcionProducto = descripcion.join(', ');

                    var productoHtml = `
                        <div class='col-md-12 mt-3 py-3 py-md-0'>
                            <div class='card shadow' style='display: flex; flex-direction: row;padding:1em 1em;'>
                                <input type='checkbox' class='form-check-input align-self-center producto-checkbox' value='${item.id_detalle_producto}' style='margin-right: 9px;'>
                                <img src='${imagen}' alt='${item.nombre_producto}' class='card-img-left' style='width: 150px; height: 150px;'>
                                <div class='card-body'>
                                    <h5 class='card-title'>${item.nombre_producto}</h5>
                                    <p class='card-text'>${descripcionProducto}</p>
                                </div>
                            </div>
                        </div>`;
                    carritoList.append(productoHtml);
                });
            } else {
                carritoList.append(` <div class='text-center'>
                ¿Aún no has solicitado una cotización? <a href='./productos.php'style='color: #007bff;'>¡Cotiza ahora!</a> y transforma tu espacio con nuestros productos.
                </div>`);
            }
        },
        error: function(error) {
            console.error('Error al obtener los productos del carrito:', error);
            $('#carrito-list').append("<p>Error al cargar los productos del carrito.</p>");
        }
    });
}
        function actualizarEstadoProductos() {
            $('.producto-checkbox:checked').each(function() {
                var idDetalleProducto = $(this).val();
                $.ajax({
                    url: '../scripts/actualizar_carrito.php',
                    method: 'POST',
                    data: { id_detalle_producto: idDetalleProducto },
                    success: function(response) {
                        console.log('Producto actualizado:', response);
                        window.location.href = './citas.php';
                    },
                    error: function(error) {
                        console.error('Error al actualizar el producto:', error);
                    }
                });
            });
        }
    });
</script>
</body>
</html>
