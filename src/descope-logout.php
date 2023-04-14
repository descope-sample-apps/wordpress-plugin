<?php
    session_start();
    if (isset($_COOKIE['DS_SESSION'])) {
        unset($_COOKIE['DS_SESSION']);
        setcookie('DS_SESSION', '', time() - 3600, '/'); // empty value and old timestamp
    }
    require_once('../../../../wp-load.php'); // include the WordPress core files

    $_SESSION["AUTH_ID"] = null;
    $_SESSION["AUTH_NAME"] = null;
    $_SESSION["SESSION_TOKEN"] = null;

    session_destroy();
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';
    $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    $base_url = get_site_url();
    $pageUrl = $base_url . '/' . $login_page_url;
    header("location:" . $pageUrl);
?>