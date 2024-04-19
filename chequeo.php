<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        .estaciones-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .estacion {
            flex: 0 0 calc(33.33% - 20px);
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .estacion:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .estacion-imagen {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .estacion-nombre {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-registrar-viaje {
            display: block;
            margin-top: 10px;
        }

        .form-registrar-viaje button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-registrar-viaje button:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .estacion {
                flex-basis: calc(50% - 20px);
            }
        }

        @media screen and (max-width: 480px) {
            .estacion {
                flex-basis: calc(100% - 20px);
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    
<?php
// Verifica si se ha proporcionado un ID de ruta válido en la solicitud
if (isset($_GET['id_ruta']) && is_numeric($_GET['id_ruta'])) {
    $id_ruta = $_GET['id_ruta'];
    // Realiza la conexión a la base de datos
    include 'conexion.php';
    $conexion = conectar();
    // Consulta SQL para obtener las estaciones de la ruta en orden original
    $query_estaciones_original = "SELECT * FROM estaciones WHERE id_rutas = $id_ruta";
    $resultado_estaciones_original = $conexion->query($query_estaciones_original);
    // Muestra la tabla de estaciones en orden original
    if ($resultado_estaciones_original->num_rows > 0) {
        echo '<h2>Estaciones</h2>';
        echo '<div class="estaciones-container">';
        while ($estacion = $resultado_estaciones_original->fetch_assoc()) {
            echo '<div class="estacion" id="estacion_' . $estacion['id_estacion'] . '">';
            echo '<img src="' . $estacion['imagen'] . '" alt="' . $estacion['nombre'] . '" class="estacion-imagen">';
            echo '<p class="estacion-nombre">' . $estacion['nombre'] . '</p>';
            echo '<div id="mensaje_estacion_' . $estacion['id_estacion'] . '"></div>'; // Div para mostrar mensajes de registro
            echo '<form method="post" class="form-registrar-viaje">';
            echo '<button type="button" name="registrar_viaje" value="' . $estacion['id_estacion'] . '" onclick="registrarEstacion(' . $estacion['id_estacion'] . ')">Registrar Estacion</button>';
            echo '</form>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo 'No se encontraron estaciones para esta ruta.';
    }
    // Cierra la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se proporcionó un ID de ruta válido, muestra un mensaje de error
    echo 'Error: ID de ruta no válido.';
}
?>
<!-- Formulario para finalizar el viaje -->
<form method="post" action="finalizar_viaje.php">
    <button type="submit" name="finalizar_viaje">Finalizar Viaje</button>
</form>
</div>
<script>
    function registrarEstacion(idEstacion) {
        var mensajeEstacion = document.getElementById('mensaje_estacion_' + idEstacion);
        var estacion = document.getElementById('estacion_' + idEstacion);
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Mostrar el mensaje de respuesta en el div correspondiente
                mensajeEstacion.innerHTML = this.responseText;
                // Ocultar la estación después de 3 segundos
                setTimeout(function() {
                    estacion.style.display = 'none';
                }, 3000);
            }
        };
        xhttp.open("POST", "actualizar_hora.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("registrar_viaje=" + idEstacion);
    }
</script>
<?php include 'piedepagina.php'; ?>
</body>
</html>
