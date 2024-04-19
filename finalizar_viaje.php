<?php
session_start();

if (isset($_SESSION['usuario']['CURP']) && isset($_POST['finalizar_viaje'])) {
    // Obtener el CURP del conductor de la sesión
    $curp_conductor = $_SESSION['usuario']['CURP'];

    // Realizar la conexión a la base de datos
    include 'conexion.php';
    $conexion = conectar();

    // Consulta SQL para verificar si hay algún registro en progreso para el conductor actual
    $sql_check = "SELECT * FROM cap_time_viajes WHERE conductor = '$curp_conductor' AND Estatus = 'progreso'";
    $result_check = $conexion->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Si hay un viaje en progreso, se procede a finalizarlo
        $row_check = $result_check->fetch_assoc();
        $id_horario = $row_check['id_horario'];
        $id_viaje = $row_check['id_viaje']; // Obtener el id_viaje del registro

        // Consulta SQL para obtener los datos de cap_time_viajes y horario
        $sql = "SELECT c.id_horario, 
                       c.T1, c.T2, c.T3, c.T4, c.T5, c.T6, c.T7, c.T8, 
                       h.T1 AS h_T1, h.T2 AS h_T2, h.T3 AS h_T3, h.T4 AS h_T4, h.T5 AS h_T5, h.T6 AS h_T6, h.T7 AS h_T7, h.T8 AS h_T8
                FROM cap_time_viajes c
                INNER JOIN horario h ON c.id_horario = h.id
                WHERE c.conductor = '$curp_conductor'
                AND c.id_horario = $id_horario
                AND c.id_viaje = $id_viaje"; // Agregar condición para el id_viaje específico

        // Ejecutar la consulta
        $result = $conexion->query($sql);

        if ($result->num_rows > 0) {
            // Recorrer los resultados
            while ($row = $result->fetch_assoc()) {
                // Inicializar contador de retardos
                $retardos = 0;

                // Comparar cada campo T1-T8
                // ... (código para calcular los retardos) ...
                  // Comparar cada campo T1-T8
                  for ($i = 1; $i <= 8; $i++) {
                    // Verificar si el valor del horario no es nulo antes de calcular el límite de tiempo
                    if (!is_null($row['h_T'.$i])) {
                        // Calcular el tiempo límite sumando 5 minutos al valor del horario
                        if(!is_null($row['T'.$i])) {
                            $limite_tiempo = strtotime($row['h_T'.$i]) + 300; // 300 segundos = 5 minutos
                            $tiempo_viaje = strtotime($row['T'.$i]);

                            // Verificar si el tiempo registrado es mayor al límite
                            if ($tiempo_viaje > $limite_tiempo) {
                                // Incrementar contador de retardos
                                $retardos++;
                            }
                        }
                    }
                }

                // Verificar si hay retardos y si son 5 o más
                if ($retardos >= 5) {
                    // Insertar datos en la tabla lista_retardos
                    $fecha_viaje = date("Y-m-d"); // Obtener la fecha actual
                    $sql_insert_retardos = "INSERT INTO lista_retardos (id_conductor, id_viaje, Fecha) 
                                            VALUES ('$curp_conductor', '$id_viaje', '$fecha_viaje')";

                    if ($conexion->query($sql_insert_retardos) === TRUE) {
                        echo "Se insertaron los datos en la tabla lista_retardos.";
                    } else {
                        echo "Error al insertar los datos en la tabla lista_retardos: " . $conexion->error;
                    }
                }

                // Actualizar el campo Retardos en cap_time_viajes
                $sql_update = "UPDATE cap_time_viajes
                               SET Retardos = $retardos, Estatus = 'Finalizado'
                               WHERE id_viaje = '$id_viaje'"; // Actualizar solo el registro con el id_viaje específico

                if ($conexion->query($sql_update) === TRUE) {
                    // Mostrar una alerta temporal
                    echo "<script>
                            setTimeout(function() {
                                alert('Se registraron $retardos retardos y el viaje ha sido finalizado.');
                                window.location.href = 'conductor.php'; // Redirige a conductor.php
                            }, 2000); // 2000 milisegundos = 2 segundos
                          </script>";
                } else {
                    echo "Error al actualizar la tabla cap_time_viajes: " . $conexion->error;
                }
            }
        } else {
            echo "No se encontraron registros para comparar.";
        }
    } else {
        echo "No hay ningún viaje en progreso para finalizar.";
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    echo "Error: No se proporcionó la CURP del conductor o el botón de finalizar viaje.";
}
?>
