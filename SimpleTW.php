<?php
   /*
   * SimpleTW - clase simple para interactuar con el API de Twitter 1.1 , OAuth 1.0
   * @ToRo 2017 https://tar.mx/
   */ 
   class SimpleTW {
      // definiciones
      private $consumerKey;         // De la aplicación
      private $consumerSecret;      //
      private $ch;                  // conexión CURL
      private $urladd;              // caso get, parámetros extras
      private $base;                // TW Base
      private $key;                 // TW key
      private $signature;           // TW Signature
      private $headers;             // TW headers request
      private $hash;                // Datos a procesar
      private $version = "1.0";
      //
      public  $oauthToken;          // De el usuario identificado
      public  $oauthTokenSecret;    // 
      public  $method;              // método a enviar (POST,GET);
      public  $url;                 // URL Twitter (api)
      public  $callback;            // URL callback (nuestro sitio)
      public  $debug = false;       // modo debug, imprime algunas variables
      /* constructor $config[CONSUMER_KEY, CONSUMER_SECRET, TOKEN, TOKEN_SECRET] {{{ */
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
            //$this->hash();
         } else die("Es necesario pasar por un arreglo CONSUMER_KEY y CONSUMER_SECRET de la APP\n");
      } /* }}} */
      /* envía las peticiones al twitter api(método, url, [args]) {{{ */
      public function api() {
         $this->method  = func_get_arg(0);
         $this->url = func_get_arg(1);
         $args = func_get_arg(2);
         $hargs = [];
         if(!empty($this->oauthToken)) $hargs = ["oauth_token"=>$this->oauthToken];
         if(!empty($this->callback)) $hargs = ["oauth_callback" => urlencode($this->callback)];
         if(!empty($args) && $this->method == 'GET') { $hargs = array_merge($hargs,$args); }
         //
         $this->prepare($hargs);
         // parámetros extras a GET
         if(!empty($args) && $this->method == 'GET') { $this->urladd = $args; }
         //
         $post = ($this->method == 'POST' && !empty($args)) ? $args : [];
         $da = $this->run($post);
         /*
         print_r($da); print_r($this->hash); echo "base:\n"; print_r($this->base); 
         echo "\n\nkey:\n"; print_r($this->key); echo "\nheaders:\n"; print_r($this->headers);
         */
         return $da;
      } /* }}} */
      /* prepara las cadenas necesarias antes de enviar {{{ */
      private function prepare($args) {
         $this->hash($args); ksort($this->hash); 
         $this->base(); $this->getKey(); $this->sign(); $this->headers();
      } /* }}} */
      /* envía las peticiones por CURL run([args]) {{{ */
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
         curl_setopt($this->ch,CURLOPT_VERBOSE,(($this->debug)?1:0)); 
         curl_setopt($this->ch,CURLOPT_HEADER,0); 
         curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($this->ch,CURLOPT_HTTPHEADER, $this->headers); 
         curl_setopt($this->ch,CURLOPT_USERAGENT,'SimpleTW '.$this->version.' https://tar.mx/apps/twitter/');

         $url = $this->url;
         if(!empty($this->urladd)) $url .= "?".http_build_query($this->urladd);
         curl_setopt($this->ch,CURLOPT_URL, $url);
         $tw = curl_exec($this->ch);
         if($this->debug) {
            printf("<pre>URL: %s %s\nHeaders: \n",$this->method,$url); print_r($this->headers); echo "ARGS: \n"; print_r($args); echo "hash:\n";print_r($this->hash);echo "RESULTADO: \n"; print_r($tw); echo "\n</pre>\n";
         }
         return $tw;
      } /* }}} */
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
         $this->base = sprintf("%s&%s&%s",$this->method,urlencode($this->url), rawurlencode($string));
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
         $this->hash = (!empty($args) ? array_merge($hash, $args[0]) : $hash);
      } /* }}} */
      /* getkey, genera la llave {{{ */
      private function getKey() {
         $this->key = sprintf("%s&%s",rawurlencode($this->consumerSecret),rawurlencode($this->oauthTokenSecret));
         //return $key;
      } /* }}} */
      /* destructor {{{ */
      function __destruct() {
         if($this->ch) curl_close($this->ch);
      } /* }}} */
   }
   //EOF
