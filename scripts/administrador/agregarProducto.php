
        <?php
        include '../../class/database.php';
        $db = new Database();
        $db-> conectarDB();
        extract($_POST);

        $cadena = "call crear_producto_por_nombre_categoria('$categoria', '$nombre', '$descripcion', '$precio','$estatus');";
        $db->ejecuta($cadena);
        $db->desconectarDB();

        ?>
        <!DOCTYPE html>
         <html lang="es">
         <head>
             <meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">
             <title>Producto Agregado</title>
             <meta http-equiv="refresh" content="3;url=../../views/administrador/vista_admin_productos.php">
             <link rel="stylesheet" href="../../css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
             <link rel="stylesheet" href="../../css/styles.css">
             <style>
                 body {
                     background: linear-gradient(180deg, rgba(19, 38, 68, 0.45) 100%, rgba(19, 38, 68, 0.45) 100%), url(../../img/index/background.jpeg) center/cover no-repeat;
                     background-size: cover;
                     background-position: center;
                     display: flex;
                     justify-content: center;
                     align-items: center;
                     height: 100vh;
                     font-family: Arial, sans-serif;
                 }
                 .confirmation-container {
                     background-color: rgba(255, 255, 255);
                     padding: 20px;
                     padding-bottom: 20px !important;
                     border-radius: 10px;
                     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                     text-align: center;
                     max-width: 500px;
                     width: 100%;
                 }
                 .confirmation-container h1 {
                     font-family: 'Montserrat';
                     color: #132644;
                     font-size: 2.5em;
                     font-weight: 800;
                     margin-bottom: 15px;
                 }
                 .confirmation-container p {
                     font-family: 'Montserrat';
                     font-size: .9em;
                     margin-bottom: 15px;
                 }
                 .confirmation-container .btn:hover {
                     background-color: #0056b3;
                 }
                 .button-cita-ex{

background: #132644;
border: 1.5px solid #132644;
border-radius: 30px;
font-family: Inter;
font-size: .9em;
font-weight: 400;
color: #fff;
cursor: pointer;
padding: 8px 18px;
text-decoration: none;
                 }

             </style>
         </head>
         <body>
             <div class="confirmation-container">
                 <img src="../../img/index/GLASS.png" alt="Glass Store" class="mb-4" style="width: 100px; margin-top:1em;">
                 <h1>¡Producto Agregado!</h1>
                 <p style="margin-bottom:2em;">Continue personalizando su producto, ¡Agregue imagenes y diseños!</p>
                 <a href="../../views/administrador/vista_admin_productos.php" class="button-cita-ex">Continuar</a>
                 <br><br>
             </div>
         </body>
         </html>
         <?php
        header("refresh:2;../../views/administrador/vista_admin_productos.php");
        ?>
