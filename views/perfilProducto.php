<?php
session_start();
include '../class/database.php';
$id_usuario = 0;
$notificaciones = [];


if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Crear conexión a la base de datos
    $db = new database();
    $db->conectarDB();

    $pdo = $db->getPDO();

    // Consulta para obtener el rol del usuario basado en el nombre de usuario
    $consulta_rol = "CALL roles_usuario(?)";
    $params_rol = [$user];
    $resultado_rol = $db->seleccionar($consulta_rol, $params_rol);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;

        // Consulta para obtener los IDs del cliente e instalador basado en el nombre de usuario
        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $params_ids = [$user];
        $resultado_ids = $db->seleccionar($consulta_ids, $params_ids);

        if ($resultado_ids && !empty($resultado_ids)) {
            $fila = $resultado_ids[0];

            if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
                $id_cliente = $fila->id_cliente;
                $id_usuario = $id_cliente;

                $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_cliente WHERE cliente = ?";
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
    }
}

$productos_espera = [];
if ($id_usuario != 0) {
    // Obtener los detalles del producto en espera
    $consulta_productos = "CALL carrito(?)";
    $params_productos = [$id_usuario];
    $productos_espera = $db->seleccionar($consulta_productos, $params_productos);
}

function esReciente($fecha){
    $fechaNotif = new DateTime($fecha);
    $fechaActual = new DateTime();
    $intervalo = $fechaActual->diff($fechaNotif);
    return ($intervalo->d < 30); // Considera reciente si es de los últimos 30 días
}

$notificacionesRecientes = array_filter($notificaciones, function($notif) {
    return esReciente($notif->fecha);
});



if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_OBJ);

    if ($producto) {
        $stmtImg = $pdo->prepare("SELECT * FROM imagen WHERE producto = :id");
        $stmtImg->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtImg->execute();
        $imagenes = $stmtImg->fetchAll(PDO::FETCH_OBJ);

        $stmtCategory = $pdo->prepare("
            SELECT c.nombre
            FROM categorias c
            JOIN productos p ON c.id_categoria = p.categoria
            WHERE p.id_producto = :id
        ");
        $stmtCategory->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtCategory->execute();
        $categoria = $stmtCategory->fetchColumn();

        $stmtDisenos = $pdo->prepare("SELECT id_diseno, codigo, file_path FROM disenos WHERE muestrario = :id");
        $stmtDisenos->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtDisenos->execute();
        $disenos = $stmtDisenos->fetchAll(PDO::FETCH_OBJ);
    } else {
        echo "Producto no encontrado.";
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
    .card-img-left {
    border-radius: 10px;
}

.card {
    border-radius: 10px;
    margin-bottom: 20px;
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
                <a href="productos.php"><img src="../img/index/search.svg" alt="" width="25px"></a>
                <button class="botonMostrarFavoritos" data-bs-toggle="modal" data-bs-target="#favoritosModal"><img src="../img/index/favorites.svg" alt="" width="25px"></button>

                <a id="carrito" data-bs-toggle="modal" data-bs-target="#carritoModal"><img src="../img/index/clip.svg" alt="" width="25px"></a>

                <div class="dropdown">
                    <a href="#" id="user-icon" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../img/index/user.svg" alt="" width="25px" style="cursor: pointer">
                    </a>
                    <?php
                    
                    if (isset($_SESSION["nom_usuario"])) {
                        echo '<ul class="dropdown-menu" aria-labelledby="user-icon">';
                        echo '<li><a class="dropdown-item" href="perfil.php">Perfil</a></li>';
                        echo '<li><a class="dropdown-item" href="#" id="notification-icon" data-bs-toggle="modal" data-bs-target="#notificationModal">Notificaciones</a></li>';
                        require_once '../class/database.php';
                        $conexion = new database();
                        $conexion->conectarDB();
                        $user = $_SESSION["nom_usuario"];
                        $consulta = "CALL roles_usuario(?)";
                        $params = [$user];
                        $roles = $conexion->seleccionar($consulta, $params);
                        if ($roles) {
                            foreach ($roles as $rol) {
                                if ($rol->nombre_rol == 'administrador') {
                                    echo '<li><a class="dropdown-item" href="../views/administrador/vista_admin.php">Administrador</a></li>';
                                } elseif ($rol->nombre_rol == 'instalador') {
                                    echo '<li><a class="dropdown-item" href="../views/instalador/index_Instalador.php">Buzón</a></li>';
                                }
                            }
                        }
                        echo '<li><hr class="dropdown-divider"></li>';
                        echo '<li><a class="dropdown-item" href="../scripts/cerrarSesion.php">Cerrar Sesión</a></li>';
                        echo '</ul>';
                    } else {
                        echo '<ul class="dropdown-menu" aria-labelledby="user-icon">';
                        echo '<li><a class="dropdown-item" href="../views/iniciarSesion.php">Iniciar Sesión</a></li>';
                        echo '</ul>';
                    }
                    ?>
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
                        <a class="nav-link nav-left" href="https://api.whatsapp.com/send?phone=8717843809" target="_blank">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-left" href="/citas">Agendar</a>
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
    <a href="./productos.php" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
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
                                $imagePath = !empty($img->imagen) ? '../img/index/' . $img->imagen : '../img/index/default.png';
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
                        $imagePath = !empty($img->imagen) ? '../img/index/' . $img->imagen : '../img/index/default.png';
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
            <h2 class="precioPerfil">$<?php echo number_format($producto->precio, 2); ?> MXN</h2>
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
           
           
            <div id="cotizacionContainer" class=" mt-5">
    <form id="cotizacionForm" method="POST" action="../scripts/guardarDetalleProducto.php" class="formPerfil">
        
        <input type="hidden" name="producto" value="<?php echo $productId; ?>">
        <input type="hidden" name="diseno" id="diseno">
            <div class="input-group">
                <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="alto" class="form-label">Alto (metros)</label>
                    <input  type="number" class="form-control inputPerfilProductoCont " id="alto" name="alto" step="0.01" required>
                   
                </div>
                <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="ancho" class="form-label">Ancho (metros)</label>
                     <input type="number" class="form-control inputPerfilProductoCont" id="ancho" name="ancho" step="0.01" required>
                    
                </div>
                <div class="inputPerfil mb-3">
                <label class="labelPerfilProduct" for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" class="form-control inputPerfilProductoCont " id="cantidad" name="cantidad" required>
            
        </div>
        <div class="inputPerfil total mb-3">
        <label class="labelPerfilProduct" for="total" class="form-label">Precio Total</label>
            <input type="text" class="form-control inputPerfilProductoCont total" id="total" name="total" readonly>
            
        </div>
            </div>
        
       
        

        <?php if ($categoria === 'persianas') : ?>
            <div class="inputPerfil mb-3">
            <label class="labelPerfilProduct" for="color_accesorios">Color de Accesorios</label>
                <select  name="color_accesorios" id="color_accesorios" class="form-control inputPerfilProductoCont">
                    <option value="blanco">Blanco</option>
                    <option value="negro">Negro</option>
                    <option value="gris">Gris</option>
                    <!-- Añadir más opciones según sea necesario -->
                </select>
                
            </div>
        <?php endif; ?>

        <div class="d-grid">
            <button type="submit" class=" buttonPerfilProducto">Solicitar Cotización</button>
        </div>
    </form>
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
                        echo '<p>' . htmlspecialchars($notif->notificacion) . '</p>';
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

<!-- nuevo Modal de Favoritos, se estarian cargando abajo con js -->
<div class="modal fade" id="favoritosModal" tabindex="-1" aria-labelledby="favoritosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="favoritosModalLabel">Mis Favoritos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="favoritos-list" class="row">
                    <!-- Aquí se cargarán los productos favoritos -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- detalles producto en el carrito -->
<div class="modal fade" id="carritoModal" tabindex="-1" aria-labelledby="carritoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carritoModalLabel">Cotizaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carrito-list" class="row">
                    <!-- Aquí se cargarán los detalles del carrito -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="aceptar-btn" class="btn btn-primary">Aceptar</button>
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
    const cantidadInput = document.getElementById('cantidad');
    const totalInput = document.getElementById('total');
    const disenoInput = document.getElementById('diseno');
    const precioPorMetroCuadrado = <?php echo $producto->precio; ?>;

    function actualizarPrecioTotal() {
        const alto = parseFloat(altoInput.value) || 0;
        const ancho = parseFloat(anchoInput.value) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;

        const metrosCuadrados = alto * ancho;
        const precioTotal = metrosCuadrados * precioPorMetroCuadrado * cantidad;
        totalInput.value = precioTotal.toFixed(2) + ' MXN';
    }

    altoInput.addEventListener('input', actualizarPrecioTotal);
    anchoInput.addEventListener('input', actualizarPrecioTotal);
    cantidadInput.addEventListener('input', actualizarPrecioTotal);

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


  // Cerrar el modal
  function closeForm() {
    document.getElementById('login-form').style.display = 'none';
  }


 
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
                        var imagen = favorito.imagen ? '../img/index/' + favorito.imagen : '../img/index/default.png';
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
                } else {
                    favoritosList.append("<p>No tienes productos en favoritos.</p>");
                }
            },
            error: function(error) {
                console.error('Error al obtener los favoritos:', error);
                $('#favoritos-list').append("<p>Error al cargar los favoritos.</p>");
            }
        });
    }
});


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
                        var imagen = item.imagen_producto ? '../img/index/' + item.imagen_producto : '../img/index/default.png';
                        var productoHtml = `
                            <div class='col-md-12 mt-3 py-3 py-md-0'>
                                <div class='card shadow' style='display: flex; flex-direction: row;'>
                                    <input type='checkbox' class='form-check-input align-self-center producto-checkbox' value='${item.id_detalle_producto}' style='margin-right: 15px;'>
                                    <img src='${imagen}' alt='${item.nombre_producto}' class='card-img-left' style='width: 150px; height: 150px;'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>${item.nombre_producto}</h5>
                                        ${item.alto ? `<p class='card-text'>Alto: ${item.alto}</p>` : ''}
                                        ${item.largo ? `<p class='card-text'>Largo: ${item.largo}</p>` : ''}
                                        ${item.cantidad ? `<p class='card-text'>Cantidad: ${item.cantidad}</p>` : ''}
                                        ${item.monto ? `<p class='card-text'>Monto: ${item.monto}</p>` : ''}
                                        ${item.grosor ? `<p class='card-text'>Grosor: ${item.grosor}</p>` : ''}
                                        ${item.tipo_tela ? `<p class='card-text'>Tipo de Tela: ${item.tipo_tela}</p>` : ''}
                                        ${item.marco ? `<p class='card-text'>Marco: ${item.marco}</p>` : ''}
                                        ${item.tipo_cadena ? `<p class='card-text'>Tipo de Cadena: ${item.tipo_cadena}</p>` : ''}
                                        ${item.color ? `<p class='card-text'>Color: ${item.color}</p>` : ''}
                                        ${item.codigo_diseno ? `<p class='card-text'>Diseño: ${item.codigo_diseno}</p>` : ''}
                                    </div>
                                </div>
                            </div>`;
                        carritoList.append(productoHtml);
                    });
                } else {
                    carritoList.append("<p>No tienes productos en espera.</p>");
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
        var idDetalleProducto = $(this).val(); // Este valor debe ser el ID del detalle del producto
        $.ajax({
            url: '../scripts/actualizar_carrito.php',
            method: 'POST',
            data: {
                id_detalle_producto: idDetalleProducto
            },
            success: function(response) {
                console.log('Producto actualizado:', response);
                window.location.href = 'citas.php';
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
