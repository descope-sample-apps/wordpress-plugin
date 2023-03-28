<?php
    session_start();
 /* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
//   if ( !defined('ABSPATH') )
//     define('ABSPATH', dirname(__FILE__) . '/');

//     require(dirname(__FILE__) . '/wp-load.php');
//     function set_user(){
    //   $user_id = $_POST["userId"];
    //   $user_name = $_POST["userName"];
    //   $session_token = $_POST["sessionToken"];

    //   global $current_user;
    //   $ID = $current_user->ID;
    //   unset($current_user);
    //   wp_set_current_user($ID, 'mayurk');
    //   wp_set_auth_cookie($ID);
    //   do_action( 'set_current_user' );
    //   do_action( 'wp_login', 'mayurk' );
    //   $current_user = wp_get_current_user();
    //   echo  $current_user->name;
      
    // }
    // add_action('init','set_user');
    // do_action('init');

    $user_id = $_POST["userId"];
    $user_name = $_POST["userName"];
    $session_token = $_POST["sessionToken"];
 
    $_SESSION["AUTH_ID"] = $user_id;
    $_SESSION["AUTH_NAME"] = $user_name;
    $_SESSION["SESSION_TOKEN"] = $session_token;

    echo ' Session auth id:' . $_SESSION["AUTH_ID"];
    echo ' Session auth name:' . $_SESSION["AUTH_NAME"];

//   if(isset($_SESSION["AUTH_ID"])){
//     echo "session started";
//   }

?>