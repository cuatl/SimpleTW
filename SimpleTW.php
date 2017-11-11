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
      public $key;                  // TW key
      public $signature;            // TW Signature
      public $headers;              // TW headers request
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
      /* destructor {{{ */
      function __destruct() {
         if($this->ch) curl_close($this->ch);
      } /* }}} */
      public function urlGetToken() {
         $this->callback = rawurlencode(func_get_arg(0));
         $this->method   = "POST";
         $this->oauthTokenSecret = null;
         $this->url      = "https://api.twitter.com/oauth/request_token";
         $this->hash = $this->hash(["oauth_callback"=> $this->callback]);
         ksort($this->hash);
         $this->base = $this->base();
         $this->key  = $this->getKey();
         $this->sign();
         $this->headers();
         //
         $run = $this->run();
         //
         print_r($this->hash);
         print_r($this->base);
         echo "\nkey: ".$this->key."\n";
         echo "signature: ".$this->signature."\n";
         echo "headers: ";
         print_r($this->headers);
         echo "\n";
      }
      private function run() {
         if($this->method == 'POST') {
            curl_setopt($this->ch,CURLOPT_POST,1); 
         } elseif($this->method == 'GET') {
            curl_setopt($this->ch,CURLOPT_HTTPGET,1); 
         }
         curl_setopt($this->ch,CURLOPT_HEADER,1); 
         curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($this->ch,CURLOPT_HTTPHEADER, $this->headers); 
         curl_setopt($this->ch,CURLOPT_URL, $this->url);
         $tw= curl_exec($this->ch);
         print_r($tw);
      }
      private function headers() {
         $headers = null;
         foreach($this->hash AS $k=>$v) $headers .= sprintf('%s="%s", ',$k,$v);
         $headers = substr($headers,0,-2);
         $this->headers = ["Authorization: OAuth ".$headers];
      }
      private function sign() {
         $this->signature = base64_encode(hash_hmac('sha1', $this->base, $this->key, TRUE));
         $this->hash['oauth_signature'] = $this->signature;
         ksort($this->hash);
      }
      private function base() {
         $string = null;
         foreach($this->hash AS $k=>$v) $string .= sprintf('%s=%s&',$k,$v);
         $string = substr($string,0,-1);
         print_r($string." <-- \n\n");
         return sprintf("%s&%s&%s",$this->method,urlencode($this->url), rawurlencode($string));
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
      private function getkey() {
         $key = sprintf("%s&%s",rawurlencode($this->consumerSecret),rawurlencode($this->oauthTokenSecret));
         return $key;
      }
   }
   // opcionalmente se puede psar el OAUTH_TOKEN y OAUTH_TOKEN_SECRET generado por el usuario 
   $config = ["CONSUMER_KEY", "CONSUMER_SECRET"];
   $SimpleTW    = new SimpleTW($config);
   $args = new stdclass;
   $urlGetToken = $SimpleTW->urlGetToken("https://tar.mx/foros/?entrar=tw"); //URL Callback como parámetro
   print_r($urlGetToken);
