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

        .empty-day {
    visibility: hidden; /* Mantiene la estructura semanal sin mostrar contenido */
}

.disabled-day {
    color: #ccc; /* Color gris para indicar que el día está deshabilitado */
    pointer-events: none; /* Evita que se pueda seleccionar */
}

.selected-day {
    background-color: #007bff; /* Color de fondo para el día seleccionado */
    color: white; /* Color de texto */
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

        <input type="hidden" name="selected_date" id="selected_date">  <!-- guarda fecha ----------->
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

                                <!-- inicia seleccionar direccion 
                                no está leyendo bien la consulta, proque aun iniciada sesion con alguien que
                                si tiene direcciones registradas no las está imprimiendo 
                                ----------->
                                <label for="direccion" class="form-label">Escoge tu dirección: </label>
                                <select id="direccion" class="form-select" name="direccion" onchange="mostrarFormulario()"> 
                                    <?php
                                        include '../class/database.php'; 
                                        $conexion = new Database();
                                        $conexion->conectarDB();

                                        $consulta = "CALL verDireccionUsuarioActual('" . $_SESSION['nom_usuario'] . "');";

                                        $menu = $conexion->seleccionar($consulta);

                                        foreach($menu as $dire)
                                        {
                                            echo "<option value='{$dire->id_direccion}'>{$dire->direccion}</option>";
                                        }
                                    ?>
                                    <!-- guarda direccion
                                     transformar texto dirección a id  ----------->
                                    <option value="registrarDireccion">Registrar nueva dirección</option>
                                </select>
                                <!-- termina seleccionar direccion ----------->

                                    <!--para registrar direccion-->
                                    <div id="addressForm" class="address-form hidden">
    <form id="formDireccion" action="../scripts/registrarDireccion.php" method="post">
        <div class="mb-3">
            <label for="calle" class="form-label">Calle</label>
            <input type="text" class="form-control" id="calle" name="calle" placeholder="Escriba su calle...">
        </div>
        <div class="mb-3">
            <label for="numero" class="form-label">Número</label>
            <input type="text" class="form-control" id="numero" name="numero" placeholder="Escriba su número...">
        </div>
        <div class="mb-3">
            <label for="numero_int" class="form-label">Número interior</label>
            <input type="text" class="form-control" id="numero_int" name="numero_int" placeholder="Escriba su número interior...">
        </div>
        <div class="mb-3">
            <label for="colonia" class="form-label">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" placeholder="Escriba su colonia...">
        </div>
        <div class="mb-3">
            <label for="ciudad" class="form-label">Ciudad</label>
            <select id="ciudad" class="form-select" name="ciudad"> 
                <option>Torreón</option>
                <option>Gómez Palacio</option>
                <option>Lerdo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="referencias" class="form-label">Referencias</label>
            <input type="text" class="form-control" id="referencias" name="referencias" placeholder="Referencias...">
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Guardar Dirección</button>
        </div>
    </form>
</div>

                                <!-- inciia tipo de cita ----------->
                                <div class="col-12">
    <label for="tipo" class="form-label">¿Qué podemos hacer por usted?</label>
    <select class="form-select" name="tipo" id="tipo">
        <option value="medidas">Toma de medidas</option>
        <option value="instalacion">Instalación de un producto</option>
        <option value="entrega">Solamente entrega</option>
        <option value="personalizada">Ninguna de las anteriores</option>
    </select>
</div>

                 <!-- termina tipo de cita ----------->

                                                 <!-- guarda motivo
                                 debe guardarlo como una variable llamada notas ----------->
                                <div class="col-12">
                                    <label for="motivo" class="form-label">Cuéntanos el motivo de tu cita</label><br>
                                    <textarea name="motivo" id="motivo" cols="50" rows="7" class="text-motivo"></textarea>
                                </div>

                                <div class="col-12 container-select">
                                                    <!-- guarda hora
                    depende de la opcion es el valor que se guardará ----------->


    <input type="hidden" name="selected_date" id="selected_date">
    <label for="hora" class="form-label">Selecciona el horario de tu preferencia</label>
    <select class="form-select form-select-custom custom-scrollbar" id="hora" name="hora" required>
        <!-- Opciones de horas serán actualizadas por JavaScript -->
    </select>


                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="submit-button-register" type="submit">Agendar</button>
                                    </div>
                                </div>
                                <input type="hidden" name="selectedDate" id="selectedDateInput">
</form>
<!-- TERMINA EL FORM -------------------------------------------------------------------------->
                        
                        </div> <!-- cierra divs de diseño ----------->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script src="../js/bootstrap.bundle.min.js"></script>
<script>

    // para el formulario de direccion

document.addEventListener('DOMContentLoaded', function() {
        const selectDireccion = document.getElementById('direccion');
        const addressForm = document.getElementById('addressForm');

        // Función para mostrar el formulario debajo de la selección si se selecciona "Registrar nueva dirección"
        function mostrarFormulario() {
            if (selectDireccion.value === 'registrarDireccion') {
                addressForm.classList.remove('hidden');
            } else {
                addressForm.classList.add('hidden');
            }
        }
        
        selectDireccion.addEventListener('change', mostrarFormulario);
    });

    // Script del calendario
    //cargar todas las horas y filtrado de horas
    document.addEventListener('DOMContentLoaded', () => {
    const calendarDays = document.getElementById('calendarDays');
    const calendarMonth = document.getElementById('calendarMonth');
    const selectedDateDisplay = document.getElementById('selectedDateDisplay');
    const selectedDateInput = document.getElementById('selected_date');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const horaSelect = document.getElementById('hora');

    let date = new Date();
    let year = date.getFullYear();
    let month = date.getMonth();
    const today = new Date();
    let selectedDayElement = null;

    // Simulando horas disponibles cargadas desde PHP
    const horasDisponibles = ['09:00','09:30','10:00','10:30','11:00','11:30',
    '12:00','12:30','13:00','13:30',
    '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30'];

    function renderCalendar() {
        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);
        
        calendarMonth.textContent = firstDayOfMonth.toLocaleString('default', { month: 'long', year: 'numeric' });
        calendarDays.innerHTML = '';

        const startDay = firstDayOfMonth.getDay();
        const endDay = lastDayOfMonth.getDate();

        for (let i = 0; i < startDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.classList.add('calendar-day', 'empty-day');
            calendarDays.appendChild(emptyDay);
        }

        for (let i = 1; i <= endDay; i++) {
            const day = document.createElement('div');
            day.classList.add('calendar-day');
            day.textContent = i;

            const currentDate = new Date(year, month, i);

            if (currentDate < today) {
                day.classList.add('disabled-day');
            } else {
                day.addEventListener('click', () => selectDate(i, day));
            }

            calendarDays.appendChild(day);
        }

        updatePrevButton();
    }

    function selectDate(day, dayElement) {
        const selected = new Date(year, month, day);
        const selectedDateString = selected.toISOString().split('T')[0];
        selectedDateDisplay.textContent = selectedDateString;
        selectedDateInput.value = selectedDateString;
        updateHoraDropdown(selectedDateString);

        if (selectedDayElement) {
            selectedDayElement.classList.remove('selected-day');
        }
        dayElement.classList.add('selected-day');
        selectedDayElement = dayElement;
    }

    function updateHoraDropdown(fecha) {
        // Aquí deberías aplicar la lógica para filtrar horas según la fecha seleccionada.
        // Por simplicidad, en este ejemplo mostramos todas las horas disponibles.

        horaSelect.innerHTML = ''; // Limpiar opciones actuales
        horasDisponibles.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora;
            option.textContent = hora;
            horaSelect.appendChild(option);
        });
    }

    // Función para mostrar una alerta (opcional)
    function showAlert() {
        alertBox.style.display = 'block';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 2000);
    }

    function updatePrevButton() {
        if (year === today.getFullYear() && month === today.getMonth()) {
            prevMonthBtn.disabled = true;
        } else {
            prevMonthBtn.disabled = false;
        }
    }

    prevMonthBtn.addEventListener('click', () => {
        month--;
        if (month < 0) {
            month = 11;
            year--;
        }
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        month++;
        if (month > 11) {
            month = 0;
            year++;
        }
        renderCalendar();
    });

    renderCalendar();
});
</script>

</body>
</html>