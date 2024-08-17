<?php
session_start();
include '../class/database.php';

$id_usuario = 0;
$notificaciones = [];
$productos_por_pagina = 8; // Número de productos por página

if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Crear conexión a la base de datos
    $conexion = new database();
    $conexion->conectarDB();

    // Consulta para obtener el rol del usuario basado en el nombre de usuario
    $consulta_rol = "CALL roles_usuario(?)";
    $params_rol = [$user];
    $resultado_rol = $conexion->seleccionar($consulta_rol, $params_rol);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;

        // Consulta para obtener los IDs del cliente e instalador basado en el nombre de usuario
        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $params_ids = [$user];
        $resultado_ids = $conexion->seleccionar($consulta_ids, $params_ids);

        if ($resultado_ids && !empty($resultado_ids)) {
            $fila = $resultado_ids[0];

            if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
                $id_cliente = $fila->id_cliente;
                $id_usuario = $id_cliente;

                $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_cliente WHERE cliente = ? order by fecha desc";
                $paramsNotificaciones = [$id_cliente];
                $notificaciones = $conexion->seleccionar($consultaNotificaciones, $paramsNotificaciones);

            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_instalador = $fila->id_instalador;
                $id_usuario = $id_instalador;

                $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_instalador WHERE instalador = ?";
                $paramsNotificaciones = [$id_instalador];
                $notificaciones = $conexion->seleccionar($consultaNotificaciones, $paramsNotificaciones);
            }
        }
    }
}

$conexion = new database();
$conexion->conectarDB();

$productos_espera = [];
if ($id_usuario != 0) {
    // Obtener los detalles del producto en espera
    $consulta_productos = "CALL carrito(?)";
    $params_productos = [$id_usuario];
    $productos_espera = $conexion->seleccionar($consulta_productos, $params_productos);
}

// Cargar los primeros 8 productos
$consulta_productos = "
    SELECT p.id_producto, p.nombre, p.precio, MIN(i.imagen) as imagen
    FROM productos p
    LEFT JOIN imagen i ON p.id_producto = i.producto
    WHERE p.estatus = 'activo'
    GROUP BY p.id_producto
    LIMIT $productos_por_pagina
";
$productos_espera = $conexion->seleccionar($consulta_productos);

function esReciente($fecha){
    $fechaNotif = new DateTime($fecha);
    $fechaActual = new DateTime();
    $intervalo = $fechaActual->diff($fechaNotif);
    return ($intervalo->d < 30); // Considera reciente si es de los últimos 30 días
}

$notificacionesRecientes = array_filter($notificaciones, function($notif) {
    return esReciente($notif->fecha);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Glass Store</title>
  <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/normalized.css">
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
                <a href="../index.php"><img src="../img/index/inicio.svg" alt="" width="25px"></a>
                <button class="botonMostrarFavoritos" data-bs-toggle="modal" data-bs-target="#favoritosModal"><img src="../img/index/favorites.svg" alt="" width="25px"></button>
                <a id="carrito" data-bs-toggle="modal" data-bs-target="#carritoModal"><img src="../img/index/clip.svg" alt="" width="25px"></a>

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
            <li><a class="dropdown-item" href="#" id="notification-icon" data-bs-toggle="modal" data-bs-target="#notificationModal">Notificaciones</a></li>
            <?php
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
      <div class="container">
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
                      <li class="nav-item ">
                          <a class="nav-link" href="https://api.whatsapp.com/send?phone=528717843809" target="_blank">Contacto</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link nav-left" href="../views/productos.php">Productos</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link nav-left" href="../views/citas.php">Agendar</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link nav-left" href="#about-us" id="link-nosotros">Nosotros</a>
                      </li>
                  </ul>
              </div>
          </div>
      </div>
    </nav>

<!-- banner -->
<main>
    <div class="main-content-products">
        <div class="content-products">
            <h1>" TRANSFORMA TU ESPACIO CON ESTILO Y DISTINCIÓN "</h1><br>
            <div class="busqueda mx-auto">
                <input type="text" placeholder="Buscar" class="buscar-input" id="search-input" autocomplete="off" name="nombre_producto" style="display: flex; align-items: center; width: 100%;">
                <img src="../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer; margin-left: 10px;">
            </div>
        </div>
    </div>
</main>

<!-- aquí se cargan los productos con imágenes -->
<div class="container">
    <div class="row" style="margin-top: 50px;" id="product-list">
        <?php
        if (!empty($productos_espera)) {
            foreach ($productos_espera as $producto) {
                $imagen = $producto->imagen ? '../img/disenos/' . $producto->imagen : '../img/disenos/default.png';
                $id_producto = $producto->id_producto;

                // Verificar si el producto es favorito para el usuario actual
                $esFavorito = false;
                if ($id_usuario != 0) { // Verificar solo si el usuario está autenticado
                    $esFavorito = $conexion->esFavorito($id_producto, $id_usuario);
                }

                // Determinar el icono a mostrar
                $iconoFavorito = $esFavorito ? '../img/index/heartCover.svg' : '../img/index/addFavorites.svg';
                echo "
                <div class='col-md-3 mt-3 py-3 py-md-0 product-item' data-name='{$producto->nombre}'>
                    <div class='card shadow' id='c'>
                        <a href='./perfilProducto.php?id={$id_producto}' style='text-decoration: none; color: inherit;'>
                            <img src='{$imagen}' alt='{$producto->nombre}' class='card image-top pad'>
                        </a>
                        
                        <div class='icon-overlay-container' onclick='changeIcon(this, {$id_producto})'>
                            <img src='{$iconoFavorito}' alt='Favorite Icon' class='icon-overlay'>
                        </div>
                        <div class='card-body'>
                            <h3 class='card-title text-center title-card-new'>{$producto->nombre}</h3>
                            <p class='card-text text-center card-price'>\${$producto->precio}</p>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<div class='col-12'><p class='text-center'>No se encontraron productos.</p></div>";
        }
        ?>
    </div>
    <div class="row">
        <div class="col-12 text-center mt-3">
            <button id="load-more">Ver más</button>
        </div>
    </div>
</div>

<!-- Modal de Cotizaciones -->
<div class="modal fade" id="carritoModal" tabindex="-1" aria-labelledby="carritoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carritoModalLabel">Cotizaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_SESSION["nom_usuario"])): ?>
                    <!-- Usuario logueado -->
                    <?php if (!empty($productos_espera)): ?>
                        <div id="carrito-list" class="row">
                            <!-- Aquí se cargarán los detalles del carrito -->
                        </div>
                    <?php else: ?>
                       
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Usuario no logueado -->
                    <div class="text-center">
                        <p><a href="../views/iniciarSesion.php">Inicia sesión</a> para ver tus cotizaciones y acceder a ellas cuando quieras. ¡ <a href="../views/register.php">Crea tu cuenta</a> y disfruta de una experiencia personalizada!</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
    <?php if (isset($_SESSION["nom_usuario"]) && !empty($productos_espera)): ?>
        <button type="button" id="aceptar-btn" class="btn btn-primary">Aceptar</button>
        <button type="button" id="limpiar-btn" class="btn btn-danger">Limpiar</button>
    <?php endif; ?>
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
                    <p class="text-center">Guarda tus productos favoritos y accede a ellos en cualquier momento.</p>
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

<!--Footer-->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Misión</h5>
                <p>Transformar espacios con soluciones innovadoras y elegantes para el diseño de interiores, creando hogares y negocios funcionales, acogedores y que reflejen el estilo único de cada cliente.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Links</h5>
                <ul class="list-unstyled">
                    <li><a href="https://api.whatsapp.com/send?phone=528717843809" target="_blank" class="text-white">Contacto</a></li>
                    <li><a href="/views/productos.php" class="text-white">Productos</a></li>
                    <li><a href="/views/citas.php" class="text-white">Agendar</a></li>
                    <li><a href="#about-us" id="link-nosotros" class="text-white">Nosotros</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Contáctanos</h5>
                <p><i class="bi bi-geo-alt"></i>Torreón Coahuila, México</p>
                <p><i class="bi bi-envelope"></i> glassstore@gmail.com</p>
                <p><i class="bi bi-phone"></i> +52 123 4564 456</p>
            </div>
        </div>
    </div>
    <div class="copy text-center py-3 w-100">
        <p class="mb-0">&copy; 2024 Glass Store. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    var currentPage = 1;
    var productosPorPagina = <?php echo $productos_por_pagina; ?>;

    // Filtro en tiempo real al escribir en el campo
    $('#search-input').on('input', function() {
        currentPage = 1; // Resetear la página actual a 1
        ejecutarBusqueda();
    });

    // Ejecutar búsqueda al hacer clic en el botón
    $('#search-button').on('click', function() {
        currentPage = 1; // Resetear la página actual a 1
        ejecutarBusqueda();
    });

    // Ejecutar búsqueda al presionar Enter
    $('#search-input').on('keydown', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Evitar comportamiento predeterminado
            currentPage = 1; // Resetear la página actual a 1
            ejecutarBusqueda();
        }
    });

    // Cargar más productos al hacer clic en "Ver más"
    $('#load-more').on('click', function() {
        currentPage++;
        cargarMasProductos(currentPage);
    });

    // Función para ejecutar la búsqueda de productos
    function ejecutarBusqueda() {
        var searchValue = $('#search-input').val().toLowerCase();
        
        if ($('#favoritosModal').is(':visible')) {
            // Buscar dentro del modal de favoritos
            $('#favoritos-list .product-item').each(function() {
                var productName = $(this).data('name').toLowerCase();
                if (productName.includes(searchValue)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            // Buscar en la lista principal de productos
            $.ajax({
                url: '../scripts/cargar_productos.php',
                method: 'GET',
                data: {
                    search: searchValue,
                    page: currentPage,
                    productos_por_pagina: productosPorPagina
                },
                dataType: 'json',
                success: function(response) {
                    $('#product-list').empty();
                    if (response.productos.length > 0) {
                        response.productos.forEach(function(producto) {
                            agregarProductoAlDOM(producto);
                        });
                        $('#load-more').show(); // Mostrar el botón "Ver más" después de la búsqueda
                    } else {
                        $('#product-list').append("<div class='col-12'><p class='text-center'>No se encontraron productos.</p></div>");
                        $('#load-more').hide();
                    }
                },
                error: function(error) {
                    console.error('Error al buscar productos:', error);
                }
            });
        }
    }

    // Función para cargar más productos
    function cargarMasProductos(pagina) {
        var searchValue = $('#search-input').val().toLowerCase();
        $.ajax({
            url: '../scripts/cargar_productos.php',
            method: 'GET',
            data: {
                search: searchValue,
                page: pagina,
                productos_por_pagina: productosPorPagina
            },
            dataType: 'json',
            success: function(response) {
                if (response.productos.length > 0) {
                    response.productos.forEach(function(producto) {
                        agregarProductoAlDOM(producto);
                    });
                } else {
                    $('#load-more').hide(); // Ocultar el botón si no hay más productos
                }
            },
            error: function(error) {
                console.error('Error al cargar más productos:', error);
            }
        });
    }

    // Función para agregar un producto al DOM
    function agregarProductoAlDOM(producto) {
    var imagen = producto.imagen ? '../img/disenos/' + producto.imagen : '../img/disenos/default.png';
    var iconoFavorito = producto.es_favorito ? '../img/index/heartCover.svg' : '../img/index/addFavorites.svg';

    var productoHtml = `
        <div class='col-md-3 mt-3 py-3 py-md-0 product-item' data-name='${producto.nombre}'>
            <div class='card shadow' id='c'>
                <a href='./perfilProducto.php?id=${producto.id_producto}' style='text-decoration: none; color: inherit;'>
                    <img src='${imagen}' alt='${producto.nombre}' class='card image-top pad'>
                </a>
                <div class='icon-overlay-container' onclick='changeIcon(this, ${producto.id_producto})'>
                    <img src='${iconoFavorito}' alt='Favorite Icon' class='icon-overlay'>
                </div>
                <div class='card-body'>
                    <h3 class='card-title text-center title-card-new'>${producto.nombre}</h3>
                    <p class='card-text text-center card-price'>\$${producto.precio}</p>
                </div>
            </div>
        </div>
    `;
    $('#product-list').append(productoHtml);
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

function changeIcon(element, id_producto) {
    <?php if (!isset($_SESSION["nom_usuario"])): ?>
        // Redirigir a la página de inicio de sesión si el usuario no está logueado
        window.location.href = "../views/iniciarSesion.php";
    <?php else: ?>
        var icon = element.querySelector('.icon-overlay');
        var isFavorite = icon.getAttribute('src') === '../img/index/heartCover.svg';
        if (isFavorite) {
            icon.setAttribute('src', '../img/index/addFavorites.svg');
        } else {
            icon.setAttribute('src', '../img/index/heartCover.svg');
        }
        saveToFavorites(id_producto);
    <?php endif; ?>
}

function saveToFavorites(id_producto) {
    $.ajax({
        url: '../scripts/guardar_favorito.php',
        method: 'POST',
        data: { id_producto: id_producto },
        success: function(response) {
            console.log(response);
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}
     // Cargar favoritos cuando el modal es mostrado
     $('#favoritosModal').on('shown.bs.modal', function () {
        cargarFavoritos();
    });

    // Función para cargar favoritos
    function cargarFavoritos() {
        <?php if (isset($_SESSION["nom_usuario"])): ?>
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
        <?php else: ?>
            var favoritosList = $('#favoritos-list');
            favoritosList.empty();
            favoritosList.append("<p>No tienes favoritos, por favor inicia sesión.</p>");
        <?php endif; ?>
    }

   $(document).ready(function() {
    $('#limpiar-btn').on('click', function() {
        $('.producto-checkbox:checked').each(function() {
            var idDetalleProducto = $(this).val();
            var card = $(this).closest('.card'); // Guardar referencia al elemento de la tarjeta para eliminarlo

            $.ajax({
                url: '../scripts/desactivar_cotizacion.php', // Archivo PHP que manejará la desactivación
                method: 'POST',
                data: { id_detalle_producto: idDetalleProducto },
                success: function(response) {
                    console.log('Producto desactivado:', response);
                    card.remove(); // Eliminar la tarjeta del DOM
                },
                error: function(error) {
                    console.error('Error al desactivar el producto:', error);
                }
            });
        });
    });
});

</script>

</body>
</html>
