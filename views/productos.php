<?php
session_start();
include '../class/database.php';
$id_usuario = 0;
$notificaciones = [];

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

                $consultaNotificaciones = "SELECT notificacion, fecha FROM notificaciones_cliente WHERE cliente = ?";
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

if (!isset($_SESSION['nombre_producto'])) {
    $_SESSION['nombre_producto'] = '';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_producto'])) {
    $nombreBuscado = trim($_POST['nombre_producto']);
    
    // Guardar el término de búsqueda en la sesión
    $_SESSION['nombre_producto'] = $nombreBuscado;

    // Redirigir a la misma página para evitar el reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$productos_espera = [];
if ($id_usuario != 0) {
    // Obtener los detalles del producto en espera
    $consulta_productos = "CALL carrito(?)";
    $params_productos = [$id_usuario];
    $productos_espera = $conexion->seleccionar($consulta_productos, $params_productos);
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
                        <a class="nav-link"  href="/">Inicio</a>
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





    <!-- banner -->
    <main>
      <div class="main-content-products">
        <div class="content-products">
          <h1>" TRANSFORMA TU ESPACIO CON ESTILO Y DISTINCIÓN "</h1><br>
          <div class="busqueda mx-auto">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: flex; align-items: center; width: 100%;">
                    <input type="text" placeholder="Buscar" class="buscar-input" id="search-input" autocomplete="off" name="nombre_producto" value="<?php echo isset($_SESSION['nombre_producto']) ? htmlspecialchars($_SESSION['nombre_producto']) : ''; ?>">
                    <img src="../img/productos/search.svg" alt="Buscar" id="search-button" style="cursor: pointer; margin-left: 10px;" onclick="this.closest('form').submit();">
                </form>
            </div>
        </div> 
      </div>
    </main>

<!-- aquí se cargan los productos con imágenes -->
<div class="container">
    <div class="row" style="margin-top: 50px;" id="product-list">
    <?php
require_once '../class/database.php';

$db = new database();
$db->conectarDB();

// Verificar si se ha enviado una búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_producto'])) {
    $nombreBuscado = trim($_POST['nombre_producto']);
    $resultados = $db->BuscarProductoPorNombre($nombreBuscado);

    if (!empty($resultados)) {
        foreach ($resultados as $producto) {
            $imagen = $producto->imagen ? '../img/productos/' . $producto->imagen : '../img/productos/default.png';
            $id_producto = $producto->id_producto;
            $esFavorito = $db->esFavorito($id_producto, $id_usuario);
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
} else {
    // Cargar productos predeterminados
    $resultados = $db->ObtenerProductosPredeterminados();

    if (!empty($resultados)) {
        foreach ($resultados as $producto) {
            $imagen = $producto->imagen ? '../img/productos/' . $producto->imagen : '../img/productos/default.png';
            $id_producto = $producto->id_producto;
            $esFavorito = $db->esFavorito($id_producto, $id_usuario);
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
}
?>

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
                  <li><a href="https://api.whatsapp.com/send?phone=8717843809" target="_blank" class="text-white">Contacto</a></li>
                  <li><a href="./products.html" class="text-white">Productos</a></li>
                  <li><a href="./citas.html" class="text-white">Agendar</a></li>
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
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    
    /*cambie esto para agregar unos mensanjes, pq no se estaban cargando*/
    $(document).ready(function() {
        console.log('jQuery version:', $.fn.jquery);
        console.log('jQuery UI version:', $.ui ? $.ui.version : 'jQuery UI no se ha cargado');

        $('#search-input').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '../class/database.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'autocomplete',
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

        $('#search-input').on('input', function() {
            var value = $(this).val().replace(/\s+/g, ' ').trim();
            $(this).val(value);
        });
    });


  document.getElementById('user-icon').addEventListener('click', function() {
    var loginForm = document.getElementById('login-form');
    if (loginForm.style.display === 'none' || loginForm.style.display === '') {
        loginForm.style.display = 'block';
    } else {
        loginForm.style.display = 'none';
    }
  });

  document.getElementById('link-nosotros').addEventListener('click', function(event) {
    event.preventDefault();
    document.querySelector('#about-us').scrollIntoView({
        behavior: 'smooth'
    });
  });

  // Manejar el click en el botón de búsqueda
  document.getElementById('search-button').addEventListener('click', function() {
    var nombreProducto = document.getElementById('search-input').value;
    if (nombreProducto.trim() !== '') {
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = ''; // Enviar al mismo archivo

      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'nombre_producto';
      input.value = nombreProducto;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    }
  });

  // Manejar la búsqueda al presionar la tecla Enter
  document.getElementById('search-input').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      document.getElementById('search-button').click();
    }
  });


  // Cerrar el modal
  function closeForm() {
    document.getElementById('login-form').style.display = 'none';
  }

  function changeIcon(element, id_producto) {
            var icon = element.querySelector('.icon-overlay');
            var isFavorite = icon.getAttribute('src') === '../img/index/heartCover.svg';
            if (isFavorite) {
                icon.setAttribute('src', '../img/index/addFavorites.svg');
            } else {
                icon.setAttribute('src', '../img/index/heartCover.svg');
            }
            saveToFavorites(id_producto);
        }

function saveToFavorites(id_producto) {
            $.ajax({
                url: '../scripts/guardar_favorito.php',
                method: 'POST',
                data: {
                    id_producto: id_producto
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }


document.getElementById('search-input').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      document.getElementById('search-button').click();
    }
  });
  /*
  document.addEventListener('DOMContentLoaded', () => {
    // Manejar clic en el icono de favoritos
    document.querySelectorAll('.favorito-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const idProducto = this.getAttribute('data-id-producto');
            const isFavorite = this.classList.contains('favorito');

            fetch('../scripts/guardar_favorito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    id_producto: idProducto
                })
            }).then(response => response.json())
              .then(result => {
                  if (result.success) {
                      if (isFavorite) {
                          this.src = '../img/index/addFavorites.svg'; // Cambiar icono para no favorito
                          this.classList.remove('favorito');
                      } else {
                          this.src = '../img/index/favoriteFilled.svg'; // Cambiar icono para favorito
                          this.classList.add('favorito');
                      }
                  } else {
                      alert(result.error || 'No se pudo actualizar el estado del favorito.');
                  }
              });
        });
    });
});
*/

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
</html>
