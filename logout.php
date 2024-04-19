<?php
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Borra la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finalmente, destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header('Location: login.php');
exit;
?>
