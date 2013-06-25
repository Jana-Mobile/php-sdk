<?php

class Jana {
      const VERSION = 0.1;

      const API_BASE_URI = 'https://api.jana.com/api/';
      const MAX_NONCE = 999999999;
      
      protected $clientId;
      protected $secretKey;
      protected $apiBaseUri;

      public function __construct($clientId, $secretKey, $apiBaseUri = NULL) {
          $this->clientId = $clientId;
          $this->secretKey = $secretKey;
	  if ($apiBaseUri != NULL) {
	      $this->apiBaseUri = $apiBaseUri;
	  } else {
	      $this->apiBaseUri = Jana::API_BASE_URI;
	  }
      }

      public function getJIALink($offerId) {
          $data = $this->baseRequestData();
          $data['method'] = 'jia-request';      
          $data['offer'] = $offerId;
            
          $encoded = $this->encodeData($data);
          $sig = $this->signData($encoded);    

          $response = $this->postToJana($encoded, $sig, 'jia-request');

	  // this may be false for an unknown error, or it may be an array indicating success or failure	  
	  return $response;
      }

      protected function postToJana($request, $sig, $method) {
          $url = $this->apiBaseUri . $method;

	  $data = array('request' => $request, 
	  	        'sig' => $sig);

	  $handle = curl_init();
	  curl_setopt_array($handle, array(CURLOPT_URL => $url,
	  			           CURLOPT_RETURNTRANSFER => 1,
	  			           CURLOPT_POST => 1,
					   CURLOPT_HTTP200ALIASES => array(400, 409),
					   CURLOPT_POSTFIELDS => $data));
	  $response = curl_exec($handle);

	  $info = curl_getinfo($handle);
	  
	  if ($response) {
	      return json_decode($response, true);
	  } else {
	      return false;
	  }
      }

      protected function baseRequestData() {
          return array(
              'algorithm' => 'HMAC-SHA256',
              'timestamp' => time(),
              'nonce' => rand(0, Jana::MAX_NONCE),
              'client_id' => $this->clientId
          );
      }

      protected function encodeData($data) {
          $asJson = json_encode($data);
	  $urlSafe = $this->base64UrlEncode($asJson);
          return $urlSafe;
      }

      protected function signData($data) {
          $sig = hash_hmac('sha256', $data, $this->secretKey, $raw = true);
	  $encodedSig = $this->base64UrlEncode($sig);
          return $encodedSig;
      }

      protected function base64UrlEncode($data) {
          $base64 = base64_encode($data);
	  $urlSafe = strtr(rtrim($base64, '='), '+/', '-_');      	  
	  return $urlSafe;
      }
}