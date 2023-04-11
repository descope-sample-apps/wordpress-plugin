<?php
    session_start();
    //TODO: Remove userID and username. - DONE
    // TODO: remove the DSR cookie - DONE
    if (isset($_COOKIE['DSR'])) {
        unset($_COOKIE['DSR']);
        setcookie('DSR', '', time() - 3600, '/'); // empty value and old timestamp
    }
    require_once('../../../../wp-load.php'); // include the WordPress core files

    $_SESSION["AUTH_ID"] = null;
    $_SESSION["AUTH_NAME"] = null;
    $_SESSION["SESSION_TOKEN"] = null;

    //TODO: Reset COOKIE - DONE
    if (isset($_COOKIE['user_name'])) {
        unset($_COOKIE['user_name']);
        setcookie('user_name', '', time() - 3600, '/'); // empty value and old timestamp
    }

    session_destroy();
    
    global $wp_db;
    $table_name = $wp_db->prefix . 'descope';
    $login_page_url = $wp_db->get_var("SELECT login_page_url FROM $table_name");
    $base_url = get_site_url();
    $pageUrl = $base_url . '/' . $login_page_url;
    header("location:" . $pageUrl);
?>