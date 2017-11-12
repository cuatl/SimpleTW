<?php
   if(!isset($_SESSION['twitter'])) {
      die("aún no estamos identificados :(");
   } else {
      //instancia de la clase
      require_once(__DIR__."/SimpleTW/SimpleTW.php");
      $SimpleTW    = new SimpleTW($config);
      $tw =& $_SESSION['twitter'];  //datos de sesión
      $SimpleTW->oauthToken       = $tw['oauth_token'];
      $SimpleTW->oauthTokenSecret = $tw['oauth_token_secret'];
   }
   //1. obtener datos
   if(isset($_POST['obtenerDatos'])) {
      // enviamos un GET a  /1.1/account/verify_credentials.json "genérico"
      // teóricamente ya tenemos las llaves del usuario identificado.
      //
      $metodo   = "GET";  //metodo a enviar
      $url      = "https://api.twitter.com/1.1/account/verify_credentials.json";
      // lista de argumentos a enviar (get, o post)
      // en este caso, si tu aplicación tiene permisos de obtener el correo se puede así solicitar
      // en caso de que no, dejarlo como $args = []; //array vacío, sin argumentos
      $args = ["include_email" => "true", "include_entities" => "false", "skip_status" => "true"];
      //
   ?>
   <p class="lead">Datos obtenidos:</p>
   <p>
   Si todo salió bien, abajo tendremos los datos de usuario como nombre, correo, etcétera. 
   Para el ejemplo mostramos todos, pero almacenaremos unos cuantos en sesión.
   -- <a href="<?php echo $sitio;?>">continuar en portada</a>
   </p>
   <pre>
      <?php
         $data = $SimpleTW->api($metodo, $url, $args);
         if(preg_match("/\.json$/",$url)) {
            $data = json_decode($data); //convertimos JSON a objeto PHP
            $tmp = [
            "ciudad"        => $data->location,
            "seguidores"    => $data->followers_count,
            "seguidos"      => $data->friends_count,
            "cuentacreada"  => $data->created_at,
            "tuits"         => $data->statuses_count,
            "imagen"        => $data->profile_image_url_https,
            "correo"        => $data->email,
            "nombre"        => $data->name,
            ];
            $tw['data'] = $tmp;
            print_r($data);
         } else {
            print_r($data);
         }
         echo "</pre>";
   ?>
</pre>
<?php
      return;
   }
   //0. aún no ha obtenido datos.
   if(!isset($tw['data'])) {
   ?>
   <p>
   Ya estamos identificados con Twitter, sin embago
   aún no tenemos sus datos generales que servirían
   para almacenar en un supuesto que utilicemos el
   servicio como una forma de identificación para nuestra
   aplicación.
   </p>
   <h3>Obtener datos</h3>
   <p>
   Vamos a obtener sus generales, para eso utilizamos nuestra clase
   para hacer una petición a <kbd>/1.1/account/verify_credentials.json</kbd>
   </p>
   <form method="post">
      <p class="text-center">
      <button name="obtenerDatos" class="btn btn-primary btn-lg">Obtener datos de <?php echo $tw['screen_name'];?></button>
      </p>
   </form>
   <pre>
      require_once(__DIR__."/SimpleTW/SimpleTW.php");
      $SimpleTW    = new SimpleTW($config);
      //añadimos oauth_token y oauth_token_secret del usuario a la configuración
      $tw =& $_SESSION['twitter'];     // datos de sesión
      $SimpleTW-&gt;oauthToken       = $tw['oauth_token'];
      $SimpleTW-&gt;oauthTokenSecret = $tw['oauth_token_secret'];
      $metodo   = "GET";  //metodo a enviar
      $url      = "https://api.twitter.com/1.1/account/verify_credentials.json";
      $args = ["include_email" =&gt; "true"];
      $data = $SimpleTW-&gt;api($metodo, $url, $args);
      print_r($data);
   </pre>
   <?php
   }
?>
