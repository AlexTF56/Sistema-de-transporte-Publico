<?php
session_start();

// Verificar si se ha iniciado sesión y se ha proporcionado el ID de la estación
if (isset($_SESSION['usuario']['CURP']) && isset($_POST['registrar_viaje'])) {
    // Obtener el ID de la estación desde el botón "registrar_viaje"
    $id_estacion = $_POST['registrar_viaje'];

    // Realizar la conexión a la base de datos
    include 'conexion.php';
    $conexion = conectar();

    // Obtener el CURP del conductor actual
    $curp_conductor = $_SESSION['usuario']['CURP'];

    // Obtener los IDs de los viajes para los que se registrarán los tiempos
    $query_viajes = "SELECT id_viaje FROM creacion_viajes WHERE conductor = '$curp_conductor'";
    $resultado_viajes = $conexion->query($query_viajes);

    if ($resultado_viajes->num_rows > 0) {
        while ($row_viaje = $resultado_viajes->fetch_assoc()) {
            $id_viaje = $row_viaje['id_viaje'];

            // Obtener el ID de la ruta correspondiente al ID del viaje actual
            $query_ruta_viaje = "SELECT id_ruta FROM creacion_viajes WHERE id_viaje = $id_viaje";
            $resultado_ruta_viaje = $conexion->query($query_ruta_viaje);

            if ($resultado_ruta_viaje->num_rows > 0) {
                $row_ruta_viaje = $resultado_ruta_viaje->fetch_assoc();
                $id_ruta = $row_ruta_viaje['id_ruta'];

                // Obtener todas las estaciones de la ruta del viaje actual
                $query_estaciones = "SELECT id_estacion FROM estaciones WHERE id_rutas = $id_ruta";
                $resultado_estaciones = $conexion->query($query_estaciones);

                // Crear un array para almacenar los IDs de las estaciones de la ruta del viaje
                $estaciones_ruta = array();
                while ($row_estacion = $resultado_estaciones->fetch_assoc()) {
                    $estaciones_ruta[] = $row_estacion['id_estacion'];
                }

                // Verificar si el ID de la estación está en la lista de estaciones de la ruta del viaje
                if (in_array($id_estacion, $estaciones_ruta)) {
                    // Determinar el índice de la estación en el arreglo de estaciones de la ruta del viaje
                    $indice_estacion = array_search($id_estacion, $estaciones_ruta);

                    // Determinar el campo de tiempo correspondiente a la estación
                    $campo_tiempo = 'T' . ($indice_estacion % 8 + 1); // Se cambia 4 por 8 para manejar hasta T8

                    // Actualizar el tiempo de la estación en la tabla cap_time_viajes correspondiente al viaje actual
                    $sql_actualizar_tiempo = "UPDATE cap_time_viajes SET $campo_tiempo = CURRENT_TIME(), Estatus = 'Progreso' WHERE id_viaje = $id_viaje";

                    if ($conexion->query($sql_actualizar_tiempo) === TRUE) {
                        // No es necesario imprimir ningún mensaje aquí

                        
                    } else {
                        // No es necesario imprimir ningún mensaje aquí
                    }
                }
            }
        }
    } else {
        // No es necesario imprimir ningún mensaje aquí
    }
    echo "Estación registrada correctamente.";
    // Cerrar la conexión a la base de datos
    $conexion->close();
}
?>
