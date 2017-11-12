# SimpleTW
Clase para interactuar con el API de Twitter

Demo de la aplicación funcionando:

https://tar.mx/apps/twitter/

Enviar un tuit:

```php
<?php
   require_once("SimpleTW.php");
   $config["CONSUMER_KEY","CONSUMER_SECRET","OAUTH_TOKEN","OAUTH_TOKEN_SECRET"];
   $SimpleTW = new SimpleTW($config);
   $url      = "https://api.twitter.com/1.1/statuses/update.json";
   $data     = $SimpleTW->api("POST", "", ["status" => "hola mundo"]); //post al api
   $data     = json_decode($data);
   print_r($data);
```

Obtener el token para identificarse (login con Twitter)

```php
   $url = "https://api.twitter.com/oauth/request_token";
   $args = ["oauth_callback" => "https://tar.mx/apps/twitter/?entrar=1"]; //login en nuestro sitio
   $data = $SimpleTW->api("POST", $url, $args);
   parse_str($data,$res);
   if(isset($res["oauth_token"]) && isset($res["oauth_callback_confirmed"])) {
      $urltoken = "https://api.twitter.com/oauth/authenticate?oauth_token=".$res["oauth_token"];
   }
   // con $urltoken entonces nos identificamos con la cuenta de twitter para obtnener el token y token_secret
```

Esta clase funciona con PHP 5.5 (probablemente con anteriores teniendo CURL y cambiando [] por Array() donde se use) y con el API de Twitter 1.1 y OAuth 1.0. Soporta GET y POST.
