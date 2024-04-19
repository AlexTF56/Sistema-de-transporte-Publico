<!DOCTYPE html>
<html>
<header class="header" role="banner">
    <script src="JS/index.JS"></script>
    <link rel="stylesheet" href="CSS/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    

    <div class="container">
        <div class="header">
            <nav>
                <ul>
                    <li class="active"><a href="#">SISTEMA DE MONITOREO DE TRANSPORTE PUBLICO</a></li>
                    <li><a href="login.php">INICIAR SESION</a></li>
                </ul>
            </nav>
            <img class="logo" src="imagenes/logotipo.jpg" alt="Logo de la página">
        </div>
    </div>
</header>

<body>

</body>
<div class="content container" role="main">
<h1>Sistema de Monitoreo de Transporte Público</h1>

<p><strong>Estado actual:</strong> Todos los servicios de transporte público están operando con normalidad.</p>
<p>Información en tiempo real sobre el estado del transporte público en tu área. Mantente informado para un viaje más fácil y seguro.</p>

<h2>Alertas Recientes</h2>

<p><strong>Ruta tecamac- Central Abastos:</strong> Ruta modificada debido a construcciones en la vía. Se esperan retrasos de 15 minutos.</p>
<p><strong>Ruta Tecamac-Zumpango:</strong> Operando con normalidad.</p>

<h2>Consejos de Viaje</h2>

<ol>
    <li>Planifica tu viaje con antelación, especialmente durante horas pico.</li>
    <li>Consulta las actualizaciones en tiempo real antes de salir.</li>
</ol>

<blockquote>
    <p>¡Viaja con seguridad y comodidad utilizando nuestro sistema de monitoreo de transporte público!</p>
</blockquote>

<h3>Próximos Eventos</h3>

<ul>
    <li>Foro sobre Mejoras en el Transporte Público - 20 de marzo, 18:00 hrs.</li>
    <li>Presentación de Nuevas Rutas y transportes - 25 de marzo, 10:00 hrs.</li>
</ul>

<br>
<br>
<?php include 'piedepagina.php'; ?>


</div>
<style>
    

body {
  background: whitesmoke;
  font-family: "Open Sans", sans-serif;
  color: #353737;
  padding: 8em 0 5em 0;
}

.container {
  width: 100%;

  
  padding: 0 1.25em;
  margin: 0 auto;
}

.header .container {
  height: 100%;
  overflow: hidden;
}

.header {
  color: #949494;
  width: 100%;
  height: 7.5em;
  text-align: center;
  background-color: #1d557a;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
  transition-duration: 0.7s;
  transition-property: height, top;
  box-shadow: rgba(30, 50, 50, 0.25) 0 0.05em 0.5em;
}

.header.scrolling {
  height: 5.5em;
}

.header.hide {
  top: -5.5em;
}

.header nav {
  height: 100%;
  display: table;
  float: right;
}

nav ul {
  list-style: none;
  height: 100%;
  vertical-align: middle;
  display: table-cell;
}

nav ul li {
  float: left;
  display: inline-block;
  overflow: hidden;
  position: relative;
  margin-left: 0.625em;
}

nav ul li a {
  transition-duration: 0.3s;
  transition-delay: 0s;
  transition-property: color, background;
  height: 2.5em;
  line-height: 2.5em;
  display: block;
  padding: 0 0.625em;
 
  text-decoration: none;
  z-index: 1;
}

nav ul li:before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: -1;
  transition-duration: 0.5s;
  transition-property: transform;
  transform: translateY(200%);
}

nav ul li:hover:before {
  background-color: #8c8abf;
  transform: translateY(0%);
}

nav ul li.active:before {
  
  transform: translateY(0%);
}

nav ul li.active a {
  font-weight: bold;
}


nav ul li.active a {
  transition-delay: 0.1s;
  color: #fff;
}

.logo {
  max-width: 5%;    /* Ajusta el ancho máximo al 80% del contenedor */
    height: auto;      /* Permite que la altura se ajuste automáticamente */
    margin-top: 1em;
    vertical-align: middle;
    display: table-cell;
    margin-left: 200px; 
}




</style>
<Script>
    var position = 0;

$(window).scroll(function(e) {
  var $element = $('.header');
  var scrollTop = $(this).scrollTop();
  if( scrollTop <= 0 ) { 
    $element.removeClass('hide').removeClass('scrolling');
  } else if( scrollTop < position ) {
    $element.removeClass('hide');
  } else if( scrollTop > position ) {
    $element.addClass('scrolling');
    if( scrollTop + $(window).height() >=  $(document).height() - $element.height() ){
      $element.removeClass('hide');
    } else if(Math.abs($element.position().top) < $element.height()) {
      $element.addClass('hide');
    }
  }
  position = scrollTop;
})
</Script>
</html>