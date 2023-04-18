<?php
    if (isset($_COOKIE['DS_SESSION'])) {
        unset($_COOKIE['DS_SESSION']);
        setcookie('DS_SESSION', '', time() - 3600, '/');
    }
    require_once('../../../../wp-load.php'); // include the WordPress core files

    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';
    $project_id = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");

    $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    $base_url = get_site_url();
    $pageUrl = "$base_url/$login_page_url";
    
    $html = "<script> logout('$project_id', '$pageUrl'); </script>";
    return $html;
?>