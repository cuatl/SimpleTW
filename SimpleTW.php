<?php
   /*
   * SimpleTW - clase simple para interactuar con el API de Twitter 1.1 , OAuth 1.0
   * @ToRo 2017 https://tar.mx/
   */ 
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
         //$run = $this->run();
         //
         /*
         print_r($this->hash);
         print_r($this->base);
         echo "\nkey: ".$this->key."\n";
         echo "signature: ".$this->signature."\n";
         echo "headers: ";
         print_r($this->headers);
         echo "\n";
         */
         $tmp = $this->run();
         parse_str($tmp,$data);
         //print_r($data);
         if(isset($data['oauth_callback_confirmed'])) {
            return "https://api.twitter.com/oauth/authenticate?oauth_token=".$data["oauth_token"];
         } else {
            return "No se pudo obtener el URL con esas credenciales\n";
         }
      }
      private function run() {
         @$args = func_get_arg(0);
         if($this->method == 'POST') {
            curl_setopt($this->ch,CURLOPT_POST,1); 
            if(!empty($args)) {
               curl_setopt($this->ch,CURLOPT_POSTFIELDS,$args);
            }
         } elseif($this->method == 'GET') {
            curl_setopt($this->ch,CURLOPT_HTTPGET,1); 
         }
         curl_setopt($this->ch,CURLOPT_VERBOSE,1); 
         curl_setopt($this->ch,CURLOPT_HEADER,0); 
         curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($this->ch,CURLOPT_HTTPHEADER, $this->headers); 
         curl_setopt($this->ch,CURLOPT_URL, $this->url);
         $tw = curl_exec($this->ch);
         return $tw;
      }
      public function verifica() {
         @$token     = func_get_arg(0);
         @$verifier  = func_get_arg(1);
         if(empty($token) OR empty($verifier)) die("No se puede continuar sin el token y el verificador del servicio");
         // generamos el hash
         $this->url = "https://api.twitter.com/oauth/access_token";
         $this->hash = $this->hash(["oauth_token"=> $token]);
         ksort($this->hash);
         $this->base = $this->base();
         $this->oauthTokenSecret = $token;
         $this->key  = $this->getKey();
         $this->sign();
         $this->headers();
         $post = ['oauth_verifier'=>$verifier];
         $this->method="POST";
         $tmp = $this->run($post);
         parse_str($tmp, $res);
         return $res;
         /*
         echo "<pre>";
         print_r($this->hash); echo "<p>key: "; print_r($this->key); print_r($this->headers);
         echo "</pre>";
         */
      }
      /* headers, genera las cabeceras HTTP {{{ */
      private function headers() {
         $headers = null;
         foreach($this->hash AS $k=>$v) $headers .= sprintf('%s="%s", ',$k,$v);
         $headers = substr($headers,0,-2);
         $this->headers = ["Authorization: OAuth ".$headers];
      } /* }}} */
      /* sign, genera firma {{{ */
      private function sign() {
         $this->signature = urlencode(base64_encode(hash_hmac('sha1', $this->base, $this->key, TRUE)));
         $this->hash['oauth_signature'] = $this->signature;
         ksort($this->hash);
      } /* }}} */
      /* base, genera base {{{ */
      private function base() {
         $string = null;
         foreach($this->hash AS $k=>$v) $string .= sprintf('%s=%s&',$k,$v);
         $string = substr($string,0,-1);
         return sprintf("%s&%s&%s",$this->method,urlencode($this->url), rawurlencode($string));
      } /* }}} */
      /*  genera hash {{{ */
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
      } /* }}} */
      /* getkey, genera la llave {{{ */
      private function getKey() {
         $key = sprintf("%s&%s",rawurlencode($this->consumerSecret),rawurlencode($this->oauthTokenSecret));
         return $key;
      } /* }}} */
   }
   //EOF
