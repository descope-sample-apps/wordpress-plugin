<?php
    session_start();
    //TODO: Remove userID and username. - DONE
    // TODO: remove the DSR cookie
    require_once('../../../../wp-load.php'); // include the WordPress core files


    $_SESSION["AUTH_ID"] = null;
    $_SESSION["AUTH_NAME"] = null;
    $_SESSION["SESSION_TOKEN"] = null;

    //TODO: Reset COOKIE
    // if (isset($_COOKIE['user_name'])) {
    //     unset($_COOKIE['user_name']);
    //     setcookie('user_name', '', time() - 3600, '/'); // empty value and old timestamp
    // }

    session_destroy();
    global $wp, $wpdb;
    // $page_id = $wpdb->get_var('SELECT post_name FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[descope-wc%" and post_status = "publish"');
    $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
    $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    $base_url = get_site_url();
    $pageUrl = $base_url . '/' . $login_page_url;
    header("location:" . $pageUrl);
?>