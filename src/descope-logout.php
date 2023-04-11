<?php
session_start();
//TODO: Remove userID and username. 
// TODO: remove the DSR cookie

$_SESSION["SESSION_TOKEN"] = null;
session_destroy();
global $wp, $wpdb;
// $page_id = $wpdb->get_var('SELECT post_name FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[descope-wc%" and post_status = "publish"');
$table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
$session_redirect_url = $wpdb->get_var("SELECT session_redirect_url FROM $table_name");
$base_url = get_site_url();
$pageUrl = $base_url . '/' . $session_redirect_url;
header("location:" . $pageUrl);
?>