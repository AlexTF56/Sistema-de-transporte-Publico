


<?php
session_start();

if(!isset($_SESSION['puesto']) || $_SESSION['puesto'] != 3){
    header('location: login.php');
    exit(); // Detiene la ejecución del script
}
?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Conductor</title>
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-top: 0;
            text-align: center;
        }

        .container table {
            width: 100%;
            margin: auto;
            border-collapse: collapse;
        }

        .container th, .container td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        .container th {
            background-color: #007bff;
            color: white;
        }

        .container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .container button:hover {
            background-color: #0056b3;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
        }

        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
        }

        .info-box {
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
        }

        #mensaje {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999;
        }
    </style>
    
  
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h1>Viaje Asignado para Hoy</h1>
    <?php
    // Realiza la conexión a la base de datos
    $conexion = conectar();

    // Obtiene la CURP del conductor actual
    $curp_conductor = $_SESSION['usuario']['CURP'];

    // Consulta SQL para obtener la información del viaje asignado al conductor actual
    $query_viaje = "SELECT creacion_viajes.id_viaje, creacion_viajes.Horario, creacion_viajes.id_vehiculo, 
                        creacion_viajes.id_ruta, vehiculos.marca AS modelo,
                        rutas.origen, rutas.destino, horario.horario, creacion_viajes.Fecha
                FROM creacion_viajes
                JOIN vehiculos ON creacion_viajes.id_vehiculo = vehiculos.id_vehiculos
                JOIN rutas ON creacion_viajes.id_ruta = rutas.id_rutas
                JOIN horario ON creacion_viajes.horario = horario.id
                WHERE creacion_viajes.conductor = '$curp_conductor'
                AND (creacion_viajes.Estatus IS NULL OR creacion_viajes.Estatus != 'Finalizado')";


    // Ejecuta la consulta del viaje asignado al conductor actual
    $resultado_viaje = $conexion->query($query_viaje);

    // Muestra la información del viaje asignado y las estaciones de la ruta
    if ($resultado_viaje->num_rows > 0) {
        while ($viaje = $resultado_viaje->fetch_assoc()) {
            echo '<div class="info-box">';
            echo '<h2>Resumen del Viaje</h2>';
            echo '<p><strong>Horario:</strong> ' . $viaje['horario'] . '</p>';
            echo '<p><strong>Origen:</strong> ' . $viaje['origen'] . '</p>';
            echo '<p><strong>Destino:</strong> ' . $viaje['destino'] . '</p>';
            echo '<p><strong>Vehículo:</strong> ' . $viaje['modelo'] . '</p>';
            echo '<p><strong>Fecha:</strong> ' . $viaje['Fecha'] . '</p>';
            echo '<button onclick="iniciarViaje(' . $viaje['id_ruta'] . ')">Iniciar Viaje</button>';
            echo '</div>';
        }
    } else {
        echo 'No se encontraron viajes asignados.';
    }

    // Cierra la conexión a la base de datos
    $conexion->close();
    ?>

    <!-- Incluye el script de Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Función para redirigir a la página de chequeo.php al iniciar el viaje
        function iniciarViaje(idRuta) {
            if (confirm('¿Estás seguro de que deseas iniciar el viaje?')) {
                // Redirige a la página de chequeo.php
                window.location.href = "chequeo.php?id_ruta=" + idRuta;
            }
        }
    </script>
    
</div>
<?php include 'piedepagina.php'; ?>
</body>
</html>
