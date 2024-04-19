<?php
include_once 'conexion.php';

session_start();

// Función de redireccionamiento
function redireccionar($puesto) {
    switch($puesto) {
        case 1:
            header('Location: admin.php');
            break;
        case 2:
            header('Location: operador.php');
            break;
        case 3:
            header('Location: conductor.php');
            break;
        default:
            // Maneja un caso por defecto si es necesario
    }
    exit(); // Importante salir del script después de la redirección
}

// Cierre de sesión
if (isset($_GET['cerrar_sesion'])){
    session_unset();
    session_destroy();
}

// Si ya hay una sesión activa, redirige al usuario según su puesto
if(isset($_SESSION['puesto'])) {
    redireccionar($_SESSION['puesto']);
}

// Si se envió un formulario de inicio de sesión
if(isset($_POST['CURP']) && isset($_POST['clave'])) {
    $usuario = $_POST['CURP'];
    $password = $_POST['clave'];

    $db = conectar();
    
    // Verifica si el usuario está bloqueado
    $query = "SELECT * FROM conductores_bloqueados WHERE id_conductor = '$usuario'";
    $result = $db->query($query);
    
    if ($result->num_rows > 0) {
        // Usuario bloqueado, mostrar advertencia
        $error = "¡Lo sentimos! Su cuenta ha sido bloqueada por el sistema.";
    } else {
        // Si el usuario no está bloqueado, procede con la verificación de inicio de sesión
        $query = "SELECT * FROM usuarios WHERE CURP = '$usuario' AND clave = '$password'";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc(); // Obtén los datos del usuario
            $_SESSION['puesto'] = $usuario['puesto']; // Establece el puesto en la sesión
            $_SESSION['usuario'] = $usuario; // Guarda los datos del usuario en la sesión
            redireccionar($usuario['puesto']); // Redirige al usuario según su puesto
        } else {
            $error = "El usuario o la contraseña son incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Estilos para el encabezado */
        header {
            background-color: #001D3D;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        /* Estilos para el pie de página */
        footer {
            background-color: #001D3D;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Estilos para el formulario */
        form {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form label {
            display: block;
            margin-bottom: 10px;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form button[type="submit"] {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button[type="submit"]:hover {
            background-color: #555;
        }

        /* Estilos para la imagen */
        .image-container {
            width: 300px;
            margin-left: 20px;
        }

        .image-container img {
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sistema de Inicio de Sesión</h1>
    </header>

    <?php if(isset($error)) : ?>
        <p style="color: red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="#" method="POST">
        <table>
            <tr>
                <td>Usuario:</td>
                <td><input type="text" name="CURP"></td>
            </tr>
            <tr>
                <td>Contraseña:</td>
                <td><input type="password" name="clave"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;"><button type="submit">Iniciar sesión</button></td>
            </tr>
        </table>
    </form>

     

    <?php include 'piedepagina.php'; ?>

</body>
</html>
