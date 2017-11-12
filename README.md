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

Eso es todo :-)
