<?php
session_start();
include '../class/database.php';

$id_usuario = 0;

if (isset($_SESSION["nom_usuario"])) {
    $user = $_SESSION["nom_usuario"];

    // Crear la instancia de la base de datos y conectar
    $conexion = new database();
    $conexion->conectarDB();

    //echo "adentro del if de obtener rol usuario ";
    //var_dump($conexion);

    // Obtener el rol del usuario
    $consulta_rol = "CALL roles_usuario(?)";
    $resultado_rol = $conexion->seleccionar($consulta_rol, [$user]);

    if ($resultado_rol && !empty($resultado_rol)) {
        $nombre_rol = $resultado_rol[0]->nombre_rol;

        // Obtener los IDs del usuario
        $consulta_ids = "CALL obtener_id_por_nombre_usuario(?)";
        $resultado_ids = $conexion->seleccionar($consulta_ids, [$user]);

        if ($resultado_ids && !empty($resultado_ids)) {
            $fila = $resultado_ids[0];
            if ($nombre_rol == 'cliente' && isset($fila->id_cliente)) {
                $id_usuario = $fila->id_cliente;
            } elseif ($nombre_rol == 'instalador' && isset($fila->id_instalador)) {
                $id_usuario = $fila->id_instalador;
            }
        }
    }

    //echo "antes de obtener id por nom_usuario";
    //var_dump($conexion);
  //chwcar si puede sacar cta
  $resultado = $conexion->ejecutarProcedimiento("CALL obtener_id_por_nombre_usuario(:nom_usuario)", [':nom_usuario' => $user]);

  //echo "resultado " . $resultado;


// if ($resultado) {
//     $id_cliente = $resultado[0]->id_cliente ?? null;
//     $id_instalador = $resultado[0]->id_instalador ?? null;

//     echo "ID Cliente: " . ($id_cliente ? $id_cliente : 'No encontrado') . "<br>";
//     echo "ID Instalador: " . ($id_instalador ? $id_instalador : 'No encontrado') . "<br>";
// } else {
//     echo "No se encontraron resultados para el usuario: " . $user;
// }

  if ($resultado) {
      $id_cliente = $resultado[0]->id_cliente ?? null;
    //   echo "ID Cliente: " . ($id_cliente ? $id_cliente : 'No encontrado') . "<br>";
  } else {
      echo "No se encontraron resultados para el usuario: " . $user;
  }

//   var_dump($id_cliente);
//   var_dump($user);
  
  $conexion->conectarDB();


if ($id_cliente) {
    // echo "ID Cliente dentro del if: " . ($id_cliente ? $id_cliente : 'No encontrado') . "<br>";

    try {
        $consulta = "
            SELECT 1
            FROM citas
            WHERE cliente_direccion = :clienteId
            AND estatus IN ('en espera', 'aceptada')
            LIMIT 1
        ";
        

        $stmt = $conexion->ejecuta2($consulta, [':clienteId' => $id_cliente]);


        if ($stmt === false) {
            throw new Exception("Error al ejecutar la consulta SQL.");
        }

        // Verificar si se encontraron citas
        $citaExistente = $stmt->fetchColumn();

        // echo "Valor devuelto por fetchColumn(): " . var_export($citaExistente, true) . "<br>";
        var_dump($citaExistente);

        if ($citaExistente === 'false') {
            echo "NO PUEDES CREAR CITAS"; //hay coincidencia
        } else {
            echo "HAZ UNA CITA"; //no hay coincidencia
        }

    } catch (Exception $e) {
        echo "Excepción capturada: " . $e->getMessage();
    }

} else {
    echo "No se ha enviado el id_cliente.";
}


// $puedeHacerCitas = false;

if ($citaExistente === '1') {
    $puedeHacerCitas = false; //no puedes, boton desabilitado
    echo '<div style="color: white; background-color: #800020; padding: 15px; border-radius: 8px; margin-top: 15px; font-weight: bold; text-align: center;">
    Tendrá que esperar a que le contesten de su anterior cita antes de agendar otra, favor esté pendiente de su cuenta.
  </div>';
} else {
    $puedeHacerCitas = true; //si puedes, boton habilitado
}

echo "Valor de puedeHacerCitas: " . var_export($puedeHacerCitas, true) . "<br>";

} //if de roles de usuario 
else {
    echo '<div style="color: white; background-color: #800020; padding: 15px; border-radius: 8px; margin-top: 15px; font-weight: bold; text-align: center;">
    Requiere registrarse para que pueda mandar una cita.
  </div>';


} //if de roles de usuario
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
            display: none !important; 
        }

        .address-form .form-control { margin-bottom: 10px; }

        .address-form { 
            border: none;  
            padding: 15px; 
            background-color: white; }

        .empty-day { 
            visibility: hidden; 
        }

        .disabled-day { 
            color: #ccc; pointer-events: none; 
        }
        
        .selected-day { 
            background-color: #007bff; color: white; 
        }

        .btn-volver { 
            background-color: white; 
            color: #132644; 
            border: 2px solid #132644; 
            padding: 10px 20px; 
            font-size: 16px; 
            font-weight: bold; 
            border-radius: 5px; 
            transition: background-color 0.3s, color 0.3s; cursor: pointer; 
        }

        .btn-volver:hover 
        { background-color: #132644; 
            color: white; 
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

<!-- form cita -------------------------------------------------------------------------->
<form id="formCita" action="../scripts/agendarCita.php" method="post">
    <div class="row agendar">

        <div class="col-12 col-lg-5 back-left blue-left-citas">
            <a href="../index.php" id="backToTopButton" class="btn btn-light btn-volver">
                <img src="../img/index/aprev.svg" alt="Volver" style="width: 20px; height: 20px;">
            </a>

            <img src="../img/index/GLASS.png" alt="" class="logo-citas">
           
        <div id="calen">
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
    </div> <!--barra azul-->

        <div class="col-12 col-lg-7">
            <div class="container formulario-registro">
                <div class="row">
                    <div class="col-12">
                        <div class="form-register agendar">
                            <h1 class="title-agendar">¡Siempre es un placer atenderte!</h1>
                            <div class="row gy-3 overflow-hidden">
                                <label for="direccion" class="form-label">Escoge tu dirección:</label>
                                <select id="direccion" class="form-select" name="direccion" onchange="mostrarFormulario()"> 
                                    <?php
                                    // Obtener direcciones del usuario
                                    $conexion = new Database();
                                    $conexion->conectarDB();

                                    $consulta = "CALL verDireccionUsuarioActual('" . $_SESSION['nom_usuario'] . "');";
                                    $menu = $conexion->seleccionar($consulta);

                                    if ($menu) {
                                        foreach($menu as $dire) {
                                            echo "<option value='{$dire->id_direccion}'>{$dire->direccion}</option>";
                                        }
                                        echo "<option value='registrarDireccion'>Registrar nueva dirección</option>";
                                    } else {
                                        echo "<option value='registrarDireccion'>Registrar nueva dirección</option>";
                                    }
                                    ?>
                                </select>

                                <div id="tipo-cita-container" class="col-12">
                                    <label for="tipo" class="form-label">¿Qué podemos hacer por usted?</label>
                                    <select class="form-select" name="tipo" id="tipo">
                                        <option value="medidas">Toma de medidas</option>
                                        <option value="instalacion">Instalación de un producto</option>
                                        <option value="entrega">Solamente entrega</option>
                                        <option value="personalizada">Ninguna de las anteriores</option>
                                    </select>
                                </div>

                                <div id="motivo-container" class="col-12">
                                    <label for="motivo" class="form-label">Cuéntanos el motivo de tu cita</label><br>
                                    <textarea name="motivo" id="motivo" cols="50" rows="5" class="text-motivo" required></textarea>
                                </div>

                                <div id="hora-container" class="col-12 container-select">
                                    <label for="hora" class="form-label">Selecciona el horario de tu preferencia</label>
                                    <select class="form-select form-select-custom custom-scrollbar" id="hora" name="hora" required> 
                                        <!--se imprime en el js porque con php no pude-->
                                    </select>
                                </div>

                                </div>

                                <div class="col-12">
                                    <div class="d-grid">
                                        <!-- <button id="submitAgendar" class="submit-button-register" type="submit">Agendar</button> -->
                                        <button id="submitAgendar" class="submit-button-register" type="submit" 
                                            <?php echo $puedeHacerCitas ? '' : 'disabled'; ?>>
                                            Agendar
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="selectedDate" id="selectedDateInput">
                            </div>
                        </div>
                    </div>
                </div>
                </form>

<!-- form nueva direccion -------------------------------------------------------------------------->

<div class="col-12 col-lg-7 mx-auto">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="row gy-3 overflow-hidden">

<div id="addressForm" class="address-form hidden">
                    <form id="formDireccion" action="../scripts/registrarDireccion.php" method="post">
                        <div class="col-12">
                            <label for="calle" class="form-label">Calle</label>
                            <input type="text" class="form-control" id="calle" name="calle" placeholder="Escriba su calle..." equired>
                        </div>
                        <div class="col-12">
                            <label for="numero" class="form-label">Número</label>
                            <input type="number" inputmode="numeric" class="form-control" id="numero" name="numero" placeholder="Escriba su número..." required>
                        </div>
                        <div class="col-12">
                            <label for="numero_int" class="form-label">Número interior</label>
                            <input type="number" inputmode="numeric" class="form-control" id="numero_int" name="numero_int" placeholder="Escriba su número interior...">
                        </div>
                        <div class="col-12">
                            <label for="colonia" class="form-label">Colonia</label>
                            <input type="text" class="form-control" id="colonia" name="colonia" placeholder="Escriba su colonia..." required>
                        </div>
                        <div class="col-12">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select id="ciudad" class="form-select" name="ciudad" required> 
                                <option>Torreón</option>
                                <option>Gómez Palacio</option>
                                <option>Lerdo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="referencias" class="form-label">Referencias</label>
                            <input type="text" class="form-control" id="referencias" name="referencias" placeholder="Referencias..." required>
                        </div>
                        <div class="d-grid">
                            <button id="formDireccion" type="submit" class="submit-button-register">Guardar Dirección</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    </div>
    </div>
    </div>
    </div>


<!-- Footer -->
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

// para la dirección  ------------------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const formDireccion = document.getElementById('formDireccion');

    formDireccion.addEventListener('submit', function(event) {
        console.log("Formulario de dirección enviado");
    });
});

    document.addEventListener('DOMContentLoaded', function() {
        const selectDireccion = document.getElementById('direccion');
        const addressForm = document.getElementById('addressForm');
        const tipoCitaContainer = document.getElementById('tipo-cita-container');
        const motivoContainer = document.getElementById('motivo-container');
        const horaContainer = document.getElementById('hora-container');
        const boton = document.getElementById('submitAgendar');
        const calen = document.getElementById('calen');

        function mostrarFormulario() {
            if (selectDireccion.value === 'registrarDireccion') {
                addressForm.classList.remove('hidden');
                tipoCitaContainer.classList.add('hidden');
                motivoContainer.classList.add('hidden');
                horaContainer.classList.add('hidden');
                boton.classList.add('hidden');
                calen.classList.add('hidden');
            } else {
                addressForm.classList.add('hidden');
                tipoCitaContainer.classList.remove('hidden');
                motivoContainer.classList.remove('hidden');
                horaContainer.classList.remove('hidden');
                boton.classList.remove('hidden');
                calen.classList.remove('hidden');
            }
        }

        mostrarFormulario();
        selectDireccion.addEventListener('change', mostrarFormulario);
    });


// para el calendario ------------------------------------------------------------------------------------------
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
            
            fetchHorariosDisponibles(selectedDateString);

            if (selectedDayElement) {
                selectedDayElement.classList.remove('selected-day');
            }
            dayElement.classList.add('selected-day');
            selectedDayElement = dayElement;
        }

        function updatePrevButton() {
            prevMonthBtn.disabled = (year === today.getFullYear() && month === today.getMonth());
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

        function fetchHorariosDisponibles(fecha) {
            fetch(`../scripts/getHorariosDisponibles.php?fecha=${fecha}`)
                .then(response => response.json())
                .then(horariosDisponibles => {
                    horaSelect.innerHTML = ''; 

                    horariosDisponibles.forEach(hora => {
                        const option = document.createElement('option');
                        option.value = hora.hora;
                        option.textContent = hora.hora;
                        horaSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        renderCalendar();
    });
</script>

</body>
</html>
