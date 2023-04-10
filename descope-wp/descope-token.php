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
?>