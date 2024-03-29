<?php
  require __DIR__ . '/../vendor/autoload.php';
  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Psr7\Request;
  use Jose\Component\Core\AlgorithmManager;
  use Jose\Component\Core\JWKSet;
  use Jose\Component\Signature\Algorithm\RS256;
  use Jose\Component\Signature\JWSVerifier;
  use Jose\Component\Signature\Serializer\CompactSerializer;
  use Jose\Component\Signature\Serializer\JWSSerializerManager;
  
  /** 
   *  @package DescopePlugin
   */

  $user_id = $_POST["userId"];
  $user_name = $_POST["userName"];
  $session_token = $_POST["sessionToken"];
  $project_id = $_POST["projectId"];

  // Fetch JWK public key from Descope API
  $url = 'https://api.descope.com/v2/keys/' . $project_id;
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', $url);
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
  
  // If Signature is not valid, destroy session and invalidate cookie.
  if (!$isVerified) {
    // Unset cookie
    unset($_COOKIE['DS_SESSION']);
    setcookie('DS_SESSION', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
      ]);
  } else {
    /** Absolute path to the WordPress directory. */
    if (!defined('ABSPATH'))
      define('ABSPATH', dirname(__FILE__) . '/');

    $session_expiry = json_decode($jws->getPayload(), true)["exp"];
    // $refresh_expiry = strtotime(json_decode($jws->getPayload(), true)["rexp"]);

    $cookie_data = array(
      "id" => $user_id,
      "name" => $user_name,
      "token" => $session_token,
    );

    $cookieSet = setcookie('DS_SESSION', json_encode($cookie_data), [
      'expires' => $session_expiry,
      'path' => '/',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
  }
?>
