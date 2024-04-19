

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Códigos QR</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
}

.qr-codes {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.qr-codes img {
    width: 200px;
    margin: 10px;
}

</style>
<body>
    <div class="container">
        <h1>Generador de Códigos QR</h1>
        <div class="qr-codes">
        <?php
// Incluir la biblioteca PHP QR Code
include 'phpqrcode/qrlib.php';

// Conexión a la base de datos (debes completar con tus datos de conexión)
$conexion = new mysqli("localhost", "root", "", "smtp");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Carpeta donde se guardarán las imágenes de los códigos QR
$directorio_qr = "QR_estaciones/";

// Consulta para obtener todas las estaciones
$sql = "SELECT id_estacion, Nombre FROM estaciones";
$resultado = $conexion->query($sql);

// Verificar si hay resultados
if ($resultado->num_rows > 0) {
    // Recorrer los resultados y generar los códigos QR
    while ($row = $resultado->fetch_assoc()) {
        $id_estacion = $row['id_estacion'];
        $nombre_estacion = $row['Nombre'];

        // Generar el contenido del QR (en este caso, solo el nombre de la estación)
        $contenido = "$nombre_estacion";

        // Nombre del archivo QR
        $archivo_qr = $directorio_qr . "qr_estacion_$id_estacion.png";

        // Generar el código QR y guardar en el directorio destino
        QRcode::png($contenido, $archivo_qr);

        // Actualizar la ubicación del QR en la tabla de estaciones
        $sql_update = "UPDATE estaciones SET QR = '$archivo_qr' WHERE id_estacion = $id_estacion";
        if ($conexion->query($sql_update) !== TRUE) {
            echo "Error al actualizar la ubicación del QR para la estación $nombre_estacion: " . $conexion->error;
        } else {
            echo "Se ha generado y actualizado el código QR para la estación: $nombre_estacion<br>";
            echo "<img src='$archivo_qr'><br><br>";
        }
    }
} else {
    echo "No se encontraron estaciones.";
}

// Cerrar la conexión
$conexion->close();
?>


        </div>
    </div>
</body>
</html>
