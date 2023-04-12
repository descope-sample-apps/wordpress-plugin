<?php
  require 'vendor/autoload.php';
  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Psr7\Request;
  use Jose\Component\Core\AlgorithmManager;
  use Jose\Component\Core\JWKSet;
  use Jose\Component\Signature\Algorithm\RS256;
  use Jose\Component\Signature\JWSVerifier;
  use Jose\Component\Signature\Serializer\CompactSerializer;
  use Jose\Component\Signature\Serializer\JWSSerializerManager;

  echo "ALL packages are imported";

  /** 
   *  @package DescopePlugin
   */
  session_start();

  $user_id = $_POST["userId"];
  $user_name = $_POST["userName"];
  $session_token = $_POST["sessionToken"];
  $refresh_token = $_POST["refreshToken"];
  $id = $_POST["idDescope"];
  $project_id = $_POST["projectId"];

  // Code to set cookie
  // setcookie('user_name', $user_name, time() + (86400 * 30), '/');
  echo "Starting to get from the API";
  // Fetch JWK public key from Descope API
  $url = 'https://api.descope.com/v2/keys/' . $project_id;
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', $url);
  echo "WE MADE IT GUYS";
  $jwk_keys = json_decode($res->getBody(), true);

  
  // Perform Validation Logic for Signature
  $jwk_set = JWKSet::createFromKeyData($jwk_keys);
  $jwsVerifier = new JWSVerifier(
    new AlgorithmManager([
      new RS256(),
    ])
  );
  $serializerManager = new JWSSerializerManager([
    new CompactSerializer(),
  ]);

  $jws = $serializerManager->unserialize($session_token);
  $isVerified = $jwsVerifier->verifyWithKeySet($jws, $jwk_set, 0);
  echo "VALIDATED SIGNATURE";
  // If Signature is not valid, destroy session.
  if (!$isVerified) {
    echo 'Invalid Signature';
    session_destroy();
  } else {
    /** Absolute path to the WordPress directory. */
    if (!defined('ABSPATH'))
      define('ABSPATH', dirname(__FILE__) . '/');

    $_SESSION["AUTH_ID"] = $user_id;
    $_SESSION["AUTH_NAME"] = $user_name;
    $_SESSION["SESSION_TOKEN"] = $session_token;
    $_SESSION["SESSION_EXPIRY"] = json_decode($jws->getPayload(), true)["exp"];
    $_SESSION["REFRESH_TOKEN"] = $refresh_token;
    echo 'Login Successful';
  }
