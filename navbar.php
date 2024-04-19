<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Document</title>
    <style>
/* Basic styles */
body {
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif; /* Set a default font */
}

/* Navigation bar styles */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background-color:  #001D3D; /* Light gray background */
  color: white;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo-class {
  width: 100px; /* Adjust width as needed */
  height: auto;
}

h2 {
  margin-left: 10px;
  font-weight: normal; /* Adjust font weight if desired */
}

.cont-ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.cont-ul li {
  display: inline-block;
  margin-right: 20px;
}

.cont-ul li a {
  text-decoration: none;
  color: white; /* Dark gray text */
  font-weight: bold;
}

/* User button and dropdown styles */
.user-button {
  background-color: transparent;
  border: none;
  cursor: pointer;
  padding: 5px 10px;
  border-radius: 5px; /* Rounded corners */
  position: relative; /* Added */
  color: white;
  font-weight: bold;
}

.user-button:hover {
  background-color: white; /* Light gray hover effect */
}
/* Menú desplegable */
.develop {
    position: relative;
}

.ul-second {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 150px; /* Ancho del menú desplegable */
    background-color: #003d7e;
    border-radius: 5px;
}

.ul-second li {
    padding: 10px;
    text-align: center;
}

/* Efecto hover en el menú desplegable */
.ul-second li:hover {
    background-color: rgb(0, 85, 177);
}

.develop:hover > .ul-second {
    display: block;
}

    </style>
</head>
<body>
<div>
    <nav class="navbar">
        <div class="logo-container">
            <img src="/SMTP/imagenes/logotipo.jpg" alt="Logo" class="logo-class">
            <h2>Sistema de Monitoreo de Transporte Público</h2>
        </div>
        <ul class="cont-ul">
            <li><a href="/SMTP/conductor.php">Inicio</a></li>
            <li class="develop">
                <button class="user-button">USUARIO</button>
                <ul class="ul-second">
                    <li class="back"><a href="/SMTP/cuenta.php">Cuenta</a></li>
                    <?php
                    // Verifica si hay una sesión de usuario iniciada
                    if (isset($_SESSION['usuario']['CURP'])) {
                        echo '<li class="back"><a href="/SMTP/logout.php">Cerrar Sesión</a></li>';
                    }
                    ?>
                </ul>
            </li>
        </ul>
    </nav>
</div>
</body>
</html>
