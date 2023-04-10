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

/** 
 *  @package DescopePlugin
 */
session_start();

$user_id = $_POST["userId"];
$user_name = $_POST["userName"];
$session_token = $_POST["sessionToken"];
$id = $_POST["idDescope"];

// Fetch JWK public key from Descope API
$project_id = 'P2NvW0PnpogrAXjls7Y9btdMv9S3';
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

// If Signature is not valid, destroy session.
if (!$isVerified) {
  session_destroy();
	// return $jws->getPayload();
} else {
  /** Absolute path to the WordPress directory. */
  if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

  $_SESSION["AUTH_ID"] = $user_id;
  $_SESSION["AUTH_NAME"] = $user_name;
  $_SESSION["SESSION_TOKEN"] = $session_token;
}
?>