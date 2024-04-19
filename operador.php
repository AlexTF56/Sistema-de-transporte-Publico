<?php
session_start();

if(!isset($_SESSION['puesto']) || $_SESSION['puesto'] != 2){
    header('location: login.php');
    exit(); // Detiene la ejecución del script
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operador</title>
    <style>
        .container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-top: 0;
        }

        .container img.button {
            cursor: pointer;
            margin-top: 20px;
            border-radius: 5px;
            width: 150px; /* Tamaño de la imagen */
        }
        .button {
    cursor: pointer;
}

.button:hover {
    opacity: 0.7; /* Cambia la opacidad al pasar el ratón por encima */
}

    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Bienvenido, Operador</h1>
        <p>Desde esta página puedes acceder a las siguientes funciones:</p>
        <br>
      
       <!-- Imagen como botón con texto debajo -->
<div style="text-align: center;">
    <div>
        <img src="imagenes/descarga.png" class="button" onclick="window.location.href='/SMTP/Crud-operador/read.php'" alt="Viajes en curso">
    </div>
    <div>Lista de Viajes</div>
</div>

<!-- Imagen como botón con texto debajo -->
<div style="text-align: center;">
    <div>
        <img src="imagenes/seguimiento.png" class="button" onclick="window.location.href='/SMTP/Crud-operador/captura_time.php'" alt="Nuevo Viaje">
    </div>
    <div>Seguimiento</div>
</div>

<div style="text-align: center;">
    <div>
        <img src="imagenes/seguimiento.png" class="button" onclick="window.location.href='/SMTP/reporte_puntualidad.php'" alt="Nuevo Viaje">
    </div>
    <div>Gestion de Reporte</div>
</div>
    </div>
</body>

<?php include 'piedepagina.php'; ?>

</html>
