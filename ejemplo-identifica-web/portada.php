<?php
   $tw =& $_SESSION['twitter'];
   if(!isset($_SESSION['twitter']['data'])) die("aún no te conozco :(");
   // vamos a enviar un tuit de este ejemplo, solo si el usuario da click en el botón.
   if(isset($_POST['promotuit'])) {
      print('<h3>Enviando post...</h3>');
      // estrictamente éstas serían todas las líneas mínimas para usar la clase
      require_once(__DIR__."/SimpleTW/SimpleTW.php");
      $SimpleTW    = new SimpleTW($config);
      $tw =& $_SESSION['twitter'];  //datos de sesión
      $SimpleTW->oauthToken       = $tw['oauth_token'];
      $SimpleTW->oauthTokenSecret = $tw['oauth_token_secret'];
      $url      = "https://api.twitter.com/1.1/statuses/update.json";
      $mensaje  = "Código de ejemplo en PHP para interactuar con el API de Twitter https://tar.mx/apps/twitter/ #SimpleTW @toro";
      $args = ["status" => $mensaje];
      $data = $SimpleTW->api("POST", $url, $args);
      @$data = json_decode($data);
      if(isset($data->id)) {
      ?>
      <p>
      <strong>Gracias por compartir</strong>, tu post
      quedó publicado en <a href="https://twitter.com/<?php echo $tw['screen_name'];?>/status/<?php echo $data->id;?>" target="_blank">tu cuenta @<?php echo $tw['screen_name'];?></a>
      </p>
      <?php
      } else {
         echo "<p class=\"text-danger lead\"><strong<Ups, algo debió salir mal</strong></p>";
      }
   ?>
   <p>Resultado del post:</p>
   <pre>
      <?php print_r($data);?>
   </pre>
   <?php
      return;
   }
?>
<p class="text-center">
<?php
   // tiene imagen? obtenemos la original
   if(!empty($tw['data']['imagen'])) {
      printf('<img src="%s" width="300" />',str_replace("_normal",null,$tw['data']['imagen']));
   }
?>
</p>
<p>
Hola <?php echo $tw['data']['nombre'];?> (<strong><?php echo $tw['screen_name'];?></strong>).
Tu cuenta fue creada el <?php echo strftime("%A %d de %B de %Y a las %R",strtotime($tw['data']['cuentacreada'])); ?>
</p>

<form method="post">
   <p class="text-center">
   <button class="btn btn-secondary" name="promotuit">Enviar mensaje de ejemplo a Twitter</button>
   <br />
   <br />
   <a href="<?php echo $sitio;?>?salir=1">Salir de mi cuenta</a>
   </p>
</form>

<p>
Desde que se obtienen los datos se pueden almacenar en una base de datos
o lo que creas conveniente, siempre y cuando se sigan las políticas sobre
el uso de los datos de Twitter. Esto es todo del demo,
el código lo puedes encontrar en mi github,
<a href="https://github.com/cuatl/SimpleTW"> SimpleTW</a>, también puedes entrar a mi página
donde hay otros ejemplos.
</p>
<p>
Este sitio no almacena tus credenciales, si sólo la utilizaste de ejemplo
puedes desde tu cuenta de twitter en tu cuenta - configuración y privacidad - 
aplicaciones - <strong>revocar acceso</strong> a esta aplicación (tar.mx).
</p>
