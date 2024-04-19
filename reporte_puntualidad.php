<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Obtener la conexión llamando a la función conectar()
$conexion = conectar();

// Verificar si la conexión se estableció correctamente
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Inicializar las variables de búsqueda
$busqueda = "";
$fecha = "";
$retardos = "";
$id_viaje = ""; // Nueva variable para el filtro de id_viaje

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiar y validar el CURP ingresado por el usuario
    $busqueda = limpiar_entrada($_POST["busqueda"]);
    // Limpiar y validar la fecha ingresada por el usuario
    $fecha = limpiar_entrada($_POST["fecha"]);
    // Limpiar y validar el número de retardos ingresado por el usuario
    $retardos = limpiar_entrada($_POST["retardos"]);
    // Limpiar y validar el ID de viaje ingresado por el usuario
    $id_viaje = limpiar_entrada($_POST["id_viaje"]); // Nuevo campo
}
//
// Construir la consulta SQL con los filtros aplicados
$sql = "SELECT conductor, id_viaje, Fecha, Retardos FROM cap_time_viajes WHERE 1=1";

// Agregar filtro por CURP si se proporcionó
if (!empty($busqueda)) {
    $sql .= " AND conductor LIKE '%$busqueda%'";
}
//g
// Agregar filtro por fecha si se proporcionó
if (!empty($fecha)) {
    $sql .= " AND Fecha = '$fecha'";
}
//
// Agregar filtro por número de retardos si se proporcionó
if (!empty($retardos)) {
    $sql .= " AND Retardos = '$retardos'";
}

// Agregar filtro por ID de viaje si se proporcionó un valor válido
if (!empty($id_viaje) && is_numeric($id_viaje)) {
    $sql .= " AND id_viaje = '$id_viaje'"; // Nuevo campo
}


// Ejecutar la consulta SQL
$resultado = $conexion->query($sql);

// Función para limpiar la entrada de usuario
function limpiar_entrada($entrada) {
    $entrada = trim($entrada);
    $entrada = stripslashes($entrada);
    $entrada = htmlspecialchars($entrada);
    return $entrada;
}

// Calcula el resumen de estadísticas
$sql_stats = "SELECT 
                COUNT(*) as total_registros, 
                AVG(Retardos) as promedio_retardos, 
                MAX(Retardos) as max_retardos, 
                MIN(Retardos) as min_retardos,
                SUM(CASE WHEN Retardos > 3 THEN 1 ELSE 0 END) as retardos_mayores_3
              FROM cap_time_viajes";
$res_stats = $conexion->query($sql_stats);
$row_stats = $res_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Puntualidad de Conductores</title>
    
    <style>

        /* Estilos para el formulario de búsqueda avanzada */
        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-container label {
            font-weight: bold;
            margin-right: 10px;
        }

        .search-container input[type="text"],
        .search-container input[type="date"],
        .search-container input[type="number"],
        .search-container button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        .search-container button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e5e5e5;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <!-- Contenedor principal -->
    <div class="report-container">
        <!-- Encabezado del reporte -->
        <div class="report-header">
            <h1>Reporte de Puntualidad de Conductores</h1>
        </div>
        <!-- Formulario de búsqueda básica y avanzada -->
        <div class="search-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="busqueda">Buscar por CURP:</label>
                <input type="text" id="busqueda" name="busqueda" value="<?php echo $busqueda; ?>" placeholder="Ingrese el ID conductor">
                <label for="fecha">Filtrar por Fecha:</label>
                <input type="date" id="fecha" name="fecha">
                <label for="retardos">Filtrar por Retardos:</label>
                <input type="number" id="retardos" name="retardos">
                <label for="id_viaje">Filtrar por ID de Viaje:</label> <!-- Nuevo campo -->
                <input type="number" id="id_viaje" name="id_viaje"> <!-- Nuevo campo -->
                <button type="submit">Buscar</button>
            </form>
        </div>
        <!-- Tabla de resultados -->
        <table>
            <tr>
                <th>ID Conductor</th>
                <th>ID Viaje</th>
                <th>Fecha</th>
                <th>Retardos</th>
            </tr>
            <!-- Mostrar resultados de la consulta -->
            <?php
            if ($resultado !== false && $resultado->num_rows > 0) {
                // Mostrar los datos en la tabla
                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["conductor"] . "</td>";
                    echo "<td>" . $row["id_viaje"] . "</td>";
                    echo "<td>" . $row["Fecha"] . "</td>";
                    echo "<td>" . $row["Retardos"] . "</td>";
                    echo "</tr>";
                }
            } else {
                // Si no se encuentran datos o hay un error en la consulta
                echo "<tr><td colspan='4'>No se encontraron datos</td></tr>";
            }
            ?>
        </table>
    </div>
            
    <!-- Contenedor para el resumen de estadísticas -->
    <div class="report-container">
        <!-- Resumen de estadísticas -->
        <div class="stats-summary">
            <h2>Resumen de Estadísticas</h2>
            <p>Total de Registros: <?php echo $row_stats['total_registros']; ?></p>
            <p>Promedio de Retardos: <?php echo $row_stats['promedio_retardos']; ?></p>
            <p>Máximo de Retardos: <?php echo $row_stats['max_retardos']; ?></p>
            <p>Mínimo de Retardos: <?php echo $row_stats['min_retardos']; ?></p>
        </div>
    </div>
    <?php include 'piedepagina.php'; ?>
</body>
</html>
