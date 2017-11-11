<?php
   class SimpleTW {
      //
      private $consumerKey;         // De la aplicación
      private $consumerSecret;      // De la aplicación
      private $oauthToken;          // De el usuario
      private $oauthTokenSecret;    // De el usuario
      private $ch;                  // conexión CURL
      // definiciones
      public $url;                  // URL Twitter (api)
      public $hash;                 // Datos a procesar
      public $callback;             // URL Callback (de tu sitio web)
      public $base;                 // TW Base
      public $method;               // método a enviar (POST,GET);
      /* constructor {{{ */
      function __construct() {
         $config = func_get_args();
         if(isset($config[0][0]) && isset($config[0][1])) {
            $this->consumerKey    = $config[0][0];
            $this->consumerSecret = $config[0][1];
            if(isset($config[0][2]) && $config[0][3]) {
               $this->oauthToken = $config[0][2];
               $this->oauthTokenSecret = $config[0][3];
            }
            try {
               $this->ch = curl_init();
            } catch(Exception $e) {
               die("Es necesaria la extensión CURL instalada.\n");
            }
            $this->hash();
         } else die("Es necesario pasar por un arreglo CONSUMER_KEY y CONSUMER_SECRET de la APP\n");
      } /* }}} */
      function __destruct() {
         if($this->ch) curl_close($this->ch);
      }
      public function urlGetToken() {
         $this->callback = func_get_arg(0);
         $this->method   = "POST";
         $this->hash = $this->hash(["oauth_callback"=>$this->callback]);
         ksort($this->hash);
         $this->base = $this->base();
         print_r($this->base);
      }
      private function base() {
         $hashstring = null;
         foreach($this->hash AS $k=>$v) $hashstring .= sprintf('%s=%s&',$k,$v);
         $hashstring = substr($hashstring,0,-1);
         return sprintf("%s&%s&%s",$this->method,urlencode($this->url), rawurlencode($hashstring));
      }
      private function hash() {
         try { $args = func_get_args(0); }
         catch (Exception $e) { $args = []; }
         $hash = [
         "oauth_consumer_key"       => $this->consumerKey,
         "oauth_signature_method"   => "HMAC-SHA1",
         "oauth_timestamp"          => time(),
         "oauth_nonce"              => substr(base64_encode(md5(time())),0,32),
         "oauth_version"            => "1.0",
         ];
         return (!empty($args) ? array_merge($hash, $args[0]) : $hash);
      }
   }
   // opcionalmente se puede psar el OAUTH_TOKEN y OAUTH_TOKEN_SECRET generado por el usuario 
   $config = ["CONSUMER_KEY", "CONSUMER_SECRET"];
   $SimpleTW    = new SimpleTW($config);
   $args = new stdclass;
   $urlGetToken = $SimpleTW->urlGetToken("https://tar.mx/foros/?entrar=tw"); //URL Callback como parámetro
   print_r($urlGetToken);
