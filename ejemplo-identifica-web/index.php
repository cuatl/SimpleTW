<?php
   session_start();
   setlocale(LC_ALL,'es_MX.UTF-8');
   include_once(__DIR__."/config.php");
   $sitio = "/apps/twitter/";
   if(isset($_GET['salir'])) { session_destroy(); header("Location: ".$sitio); exit();
   }
?>
<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="Ejemplo de SimpleTW">
      <meta name="author" content="Jorge Martínez M @toro">
      <title>Ejemplo de uso de la clase SimpleTW - tar.mx</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
   </head>
   <body>
      <div class="container">
         <div class="masthead">
            <h3><a href="<?php echo $sitio;?>" class="text-muted">SimpleTW</a></h3>
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-3">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
               <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                  <a class="nav-link" href="<?php echo $sitio;?>">Inicio <span class="sr-only">(current)</span></a></li>
                  <li class="nav-item"><a class="nav-link" href="/">tar.mx</a></li>
               </ul>
               <?php
                  //imagen usuario identificado
                  if(isset($_SESSION['twitter']['data'])) {
                     printf('<span class="navbar-text float-right">%s <img src="%s" height="30" alt="imagen" /></span>',$_SESSION['twitter']['screen_name'],$_SESSION['twitter']['data']['imagen']);
                  }
               ?>
            </div>
            </nav>
         </div>
         <!-- contenido -->
         <div class="row">
            <div class="col-sm-8">
               <?php
                  if(!isset($_SESSION['twitter'])) {
                     //aún no estamos identificados con twitter?
                     include_once("twflow.php");
                  } elseif(!isset($_SESSION['twitter']['data'])) {
                     //está identificado, pero aún no conocemos sus datos.
                     include_once("datos.php");
                  } elseif(isset($_GET['cmd'])) {
                     include_once("cmd/index.php");
                  } else {
                     //ya sabemos quien es... :-)
                     include_once("portada.php");
                  }
               ?>
            </div>
            <div class="col-sm-4">
               En el ejemplo podemos obtener:
               <ol>
                  <li>URL para identificarnos con TW</li>
                  <li>Token y token_secret del usuario identificado</li>
                  <li>Datos del usuario generales</li>
                  <li>Enviar un post de ejemplo :-)</li>
               </ol>
               <p>
               <a href="https://github.com/cuatl/SimpleTW">Código en github</a>
               </p>
               Otros ejemplos
               <ul>
                  <li><a href="<?php echo $sitio;?>?cmd=status">enviar un post</a> desde línea de comandos</li>
               </ul>
            </div>
         </div>
         <!-- Site footer -->
         <hr />
         <footer class="footer">
         <p class="text-center">
         &copy; tar.mx 
         <?php 
            echo date('Y');
            if(isset($_SESSION['twitter'])) {
               printf(' / <a href="%s?salir=1">salir</a>',$sitio);
            } else {
               printf(' / <a href="%s?entrar=1">entrar</a>',$sitio);
            }
         ?>
         /
         <a href="/foros/">Foro</a>
         </p>
         </footer>
      </div> <!-- /container -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
   </body>
</html>
<pre>
   <?php
      if(isset($_SESSION['twitter'])) {
         echo "\nDatos de sesión de Twitter\n\n";
         print_r($_SESSION['twitter']);
      } else {
         echo "Aún no está identificado";
      }
   ?>
</pre>
