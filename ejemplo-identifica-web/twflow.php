<?php
   //código
   function __($t) { return addslashes(strip_tags($t)); }
   require_once(__DIR__."/SimpleTW/SimpleTW.php");
   /*
   * la variable $config está en el archivo config.php (se anexa config-sample.php)
   * donde deberás poner tus credenciales de la aplicación de Twitter que vas a usar
   * para este ejemplo. Si no tienes una puedes hacerla en https://apps.twitter.com/
   */
   if(!isset($config) OR empty($config) OR !is_array($config)) die("Debes establecer config.php");
   $SimpleTW    = new SimpleTW($config);
   //
   if(!isset($_GET['entrar'])) {
      //paso 1
   ?>
   <p>
   Ejemplo de la clase <a href="https://github.com/cuatl/SimpleTW">SimpleTW</a> para 
   identificarse con el servicio de <strong>Twitter</strong>.
   El código está disponible en github.
   </p>
   <h1>Paso 1: iniciar el proceso de login</h1>
   <p class="text-center">
   <a href="https://tar.mx/apps/twitter/?entrar=1" class="btn btn-primary btn-lg">Generar token para login</a>
   </p>
   <p>
   Creamos una instancia de SimpleTW, luego obtenemos el URL con el Token
   para continuar con el flujo de identificación con Twitter. 
   </p>
   <pre>
      &lt;?php
      require_once("SimpleTW/SimpleTW.php");
      $config = ["CONSUMER_KEY", "CONSUMER_SECRET"];
      $SimpleTW    = new SimpleTW($config);
      $urlGetToken = $SimpleTW-&gt;urlGetToken("https://tar.mx/apps/twitter/?entrar=1"); 
      //
   </pre>
   <?php
   } elseif(isset($_GET['entrar']) && !isset($_GET['oauth_verifier'])) {
      //paso 2, obtenemos el url del token con nuestra página donde vamos a continuar le proceso una vez identificado con TW
      $urlGetToken = $SimpleTW->urlGetToken("https://tar.mx/apps/twitter/?entrar=1");
      if(preg_match("/^https:\/\/api\.twitter\.com/",$urlGetToken)) {
         //echo "Ok, continuamos con ".$urlGetToken;
      } else die("ERROR: ".$urlGetToken);
   ?>
   <h1>Paso 2: obtenemos URL del servicio</h1>
   <p class="text-center">
   <a href="<?php echo $urlGetToken;?>" class="btn btn-primary btn-lg">Entrar con twitter</a>
   </p>
   <p>
   Vamos a ir a la siguiente liga generada por el servicio, 
   <kbd><?php echo $urlGetToken;?></kbd>
   desde la cual Twitter nos pedirá nuestras credenciales para continuar. En el paso
   anterior pusimos la dirección del callback URL (esta página) que es la que va a 
   gestionar en el caso de que nos haya regresado resultados.
   </p>
   <pre>
      // es un url válida de twitter?
      if(preg_match("/^https:\/\/api\.twitter\.com/",$urlGetToken)) {
         echo "Ok, continuamos con ".$urlGetToken;
      }
      //
   </pre>
   <?php
   } elseif(isset($_GET['oauth_verifier']) && !isset($_GET['verificar'])) {
      //paso 3, obtuvimos un token temporal del usuario (ya está identificado!)
   ?>
   <h1>Paso 3: credenciales temporales</h1>
   <p>
   Twitter nos ha generado <kbd>oauth_token</kbd> y <kbd>oauth_verifier</kbd>, con lo cual
   podemos generar ahora si las credenciales del usuario, en este paso también verificamos las credenciales:
   </p>
   <pre>
      <?php
         echo "\n";
         printf("oauth_token    = %s\n",$_GET['oauth_token']);
         printf("oauth_verifier = %s\n",$_GET['oauth_verifier']);
      ?>
   </pre>
   <h1>Paso 4: verificamos las credenciales</h1>
   <p>
   Verificamos con los datos anteriores, que el usuario esté identificado https://api.twitter.com/oauth/access_token
   </p>
   Resultando:
   <pre>
   <?php
      //ahora, verificamos que con el token y verifier, podamos identificar al usuario
      $res = $SimpleTW->verifica($_GET['oauth_token'],$_GET['oauth_verifier']);
      echo "\n";
      print_r($res);
      //almacenamos los datos de la sesión
      if(isset($res['oauth_token']) && isset($res['oauth_token_secret'])) {
         $_SESSION['twitter'] = $res;
      } else unset($_SESSION['twitter']);
   ?>
</pre>
<p>
En caso de que haya regresado <kbd>oauth_token</kbd> y <kbd>oauth_token_secret</kbd> 
estamos listos para usar esas credenciales (de acuerdo a las políticas de Twitter),
para interactuar con el usuario, o simplemente para identificarlo.
</p>
<p>
Lo único que nos faltaría del proceso de login, es obtener los datos 
de la persona que acaba de entrar, eso lo podemos hacer ya <a href="<?php echo $sitio;?>">en la portada</a>
ya que tenemos en sesión los datos.
</p>
   <?php
   }
?>
