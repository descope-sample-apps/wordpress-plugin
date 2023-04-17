<?php
    session_start();
    if (isset($_COOKIE['DS_SESSION'])) {
        unset($_COOKIE['DS_SESSION']);
        setcookie('DS_SESSION', '', time() - 3600, '/');
    }
    require_once('../../../../wp-load.php'); // include the WordPress core files

    $_SESSION["AUTH_ID"] = null;
    $_SESSION["AUTH_NAME"] = null;
    $_SESSION["SESSION_TOKEN"] = null;

    session_destroy();
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';
    $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    $project_id = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");

    $html .= "<script>logout('$project_id', '$login_page_url')</script>";
    return $html;
?>