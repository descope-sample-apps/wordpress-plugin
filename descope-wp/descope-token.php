<?php
/** 
 *  @package DescopePlugin
 */
session_start();

$user_id = $_POST["userId"];
$user_name = $_POST["userName"];
$session_token = $_POST["sessionToken"];
$id = $_POST["idDescope"];

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
  define('ABSPATH', dirname(__FILE__) . '/');

$_SESSION["AUTH_ID"] = $user_id;
$_SESSION["AUTH_NAME"] = $user_name;
$_SESSION["SESSION_TOKEN"] = $session_token;


echo 'Session auth token:' . $_SESSION["SESSION_TOKEN"];

$project_id = 'P2NvW0PnpogrAXjls7Y9btdMv9S3';
$url = 'https://api.descope.com/v2/keys/' . $project_id;
$client = new GuzzleHttp\Client();
$res = $client->request('GET', $url);
$jwk_keys = json_decode($res->getBody(), true);
$jwk_set = JWKSet::createFromKeyData($jwk_keys);
			
$jwsVerifier = new JWSVerifier(
  new AlgorithmManager([
    new RS256(),
  ])
);

$serializerManager = new JWSSerializerManager([
	new CompactSerializer(),
]);

$jws = $serializerManager->unserialize($session);

$isVerified = $jwsVerifier->verifyWithKeySet($jws, $jwk_set, 0);
if (!$isVerified) {
  session_destroy();
	// return $jws->getPayload();
}
?>