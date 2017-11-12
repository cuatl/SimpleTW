<?php
   /* 
   * ejemplo de login con Twitter (implementar login con TW en página web)
   * https://tar.mx
   */
   require_once(__DIR__."/SimpleTW.php");
   $config = ["CONSUMER_KEY", "CONSUMER_SECRET"];
   require_once("../twconfig.php"); //aquí pongo un archivo con la variable $config, se puede eliminar
   $SimpleTW    = new SimpleTW($config);
   // obtenemos el URL para que el usuario entre con sus datos
   $urlGetToken = $SimpleTW->urlGetToken("https://tar.mx/foros/?entrar=tw"); //URL Callback como parámetro
   printf("URL Get Token: %s\n",$urlGetToken); 
   //si aquí devolvió un URL entonces probar pegando esa dirección en el navegador
