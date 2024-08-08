<?php
session_start();
include '../class/database.php';

$id_usuario = 0;

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
                $id_usuario = $fila->id_cliente;
            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_usuario = $fila->id_instalador;
            } 
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <link rel="shortcut icon" href="../img/index/logoVarianteSmall.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/normalized.css">

    <style>
        .hidden {
            display: none;
        }
        .address-form {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .address-form label {
            font-weight: bold;
        }
        .address-form .form-control {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<!-- WhatsApp flotante -->
<div id="wa-button">
    <a href="https://api.whatsapp.com/send?phone=8717843809" target="_blank">
        <img src="../img/index/whatsappFloat.svg" alt="Contáctanos por WhatsApp">
    </a>
</div>

<!-- Logotipo superior -->

<!-- Contenido -->
<!-- INICIA EL FORM -------------------------------------------------------------------------->
<form action="../scripts/agendarCita.php" method="post"> 

<div class="row agendar">
    <div class="col-12 col-lg-5 back-left blue-left-citas">
        <img src="../img/index/GLASS.png" alt="" class="logo-citas">
        <h2 class="subtitle-agendar">¿Qué día tendremos el gusto de atenderte?</h2>
        
        <div class="container">
            <div class="calendar">
                <div class="calendar-header">
                    <button id="prevMonth" class="btn btn-light"><img src="../img/citas/previous.svg" alt="Anterior"></button>
                    <p class="month" id="calendarMonth"></p>
                    <button id="nextMonth" class="btn btn-light"><img src="../img/citas/next.svg" alt="Siguiente"></button>
                </div>
                <div class="calendar-days" id="calendarDays"></div>
            </div>

            <input type="hidden" name="selected_date" id="selected_date">
            <p>Día seleccionado: <span id="selectedDateDisplay"></span></p>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="container formulario-registro">
            <div class="row">
                <div class="col-12">
                    <div class="form-register agendar">
                        <h1 class="title-agendar">¡Siempre es un placer atenderte!</h1>
                        <div class="row gy-3 overflow-hidden">
                            <!-- Dirección -->
                            <div class="col-12 address-form">
                                <h2 class="subtitle-agendar">Dirección</h2>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label for="calle" class="form-label">Calle</label>
                                        <input type="text" class="form-control" id="calle" name="calle" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero" name="numero" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="numero_interior" class="form-label">Número Interior</label>
                                        <input type="text" class="form-control" id="numero_interior" name="numero_interior">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label for="colonia" class="form-label">Colonia</label>
                                        <input type="text" class="form-control" id="colonia" name="colonia" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="ciudad" class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                                    </div>
                                </div>
                                <input type="hidden" name="tipo" id="tipo" value="instalacion">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="referencias" class="form-label">Referencias</label>
                                        <textarea class="form-control" id="referencias" name="referencias" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="col-12">
                                <label for="motivo" class="form-label">Cuéntanos el motivo de tu cita</label><br>
                                <textarea name="motivo" id="motivo" cols="50" rows="7" class="text-motivo"></textarea>
                            </div>

                            
                            <div class="col-12 container-select">
                                <label for="hora" class="form-label">Selecciona el horario de tu preferencia</label>
                                <select class="form-select form-select-custom custom-scrollbar" id="hora" name="hora" required>
                                    <option value="09:00">9:00 am</option>
                                    <option value="09:30">9:30 am</option>
                                    <option value="10:00">10:00 am</option>
                                    <option value="10:30">10:30 am</option>
                                    <option value="11:00">11:00 am</option>
                                    <option value="11:30">11:30 am</option>
                                    <option value="12:00">12:00 pm</option>
                                    <option value="12:30">12:30 pm</option>
                                    <option value="13:00">1:00 pm</option>
                                    <option value="13:30">1:30 pm</option>
                                    <option value="14:00">2:00 pm</option>
                                    <option value="14:30">2:30 pm</option>
                                    <option value="15:00">3:00 pm</option>
                                    <option value="15:30">3:30 pm</option>
                                    <option value="16:00">4:00 pm</option>
                                    <option value="16:30">4:30 pm</option>
                                    <option value="17:00">5:00 pm</option>
                                    <option value="17:30">5:30 pm</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <button class="submit-button-register" type="submit">Agendar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<!-- TERMINA EL FORM -------------------------------------------------------------------------->
<!-- footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Misión</h5>
                <p>Transformar espacios con soluciones en vidrio y aluminio de alta calidad, creando ambientes modernos y funcionales que superen las expectativas de nuestros clientes.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Visión</h5>
                <p>Ser la empresa líder en la industria del vidrio y aluminio en México, reconocida por nuestra innovación, calidad y servicio excepcional al cliente.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Valores</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check"></i> Calidad</li>
                    <li><i class="bi bi-check"></i> Innovación</li>
                    <li><i class="bi bi-check"></i> Servicio al Cliente</li>
                    <li><i class="bi bi-check"></i> Integridad</li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script src="../js/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/agendarCita.js"></script>
<script>
    const calendarDays = document.getElementById('calendarDays');
    const selectedDateInput = document.getElementById('selected_date');
    const selectedDateDisplay = document.getElementById('selectedDateDisplay');

   
    function createCalendar() {
     
        const currentDate = new Date();

      
        generateCalendarDays(currentDate.getFullYear(), currentDate.getMonth());
    }


    function generateCalendarDays(year, month) {

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

  
        const firstDayOfWeek = firstDay.getDay();


        calendarDays.innerHTML = '';

   
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.classList.add('empty-day');
            calendarDays.appendChild(emptyDay);
        }

 
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            dayElement.textContent = day;
            dayElement.addEventListener('click', () => selectDate(year, month, day));
            calendarDays.appendChild(dayElement);
        }
    }

 
    function selectDate(year, month, day) {
 
        const selectedDate = new Date(year, month, day);
        const formattedDate = selectedDate.toISOString().split('T')[0];


        selectedDateInput.value = formattedDate;
        selectedDateDisplay.textContent = formattedDate;
    }


    createCalendar();
</script>
</body>
</html>
