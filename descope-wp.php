<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

/**
 *  @package DescopePlugin
 */

/**
 * Plugin Name: Login by Descope
 * Description: Onboard your users, like it’s 2023. Add password-less authentication and user management to your app with a few lines of code. Choose from our drag-and-drop workflows, SDKs, or APIs.
 * Version: 1.0
 * Author: Descope
 * Author URI: https://www.descope.com/
 * License: GPL2
 */

// Define your plugin functionality here

defined('ABSPATH') or die('You cannot access this plugin');

// Activation hook
register_activation_hook(__FILE__, 'my_plugin_activate');


function my_plugin_activate()
{
    session_start();
    global $wpdb;

    // Create table when the plugin activates
    $table_name = $wpdb->prefix . 'descope';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query to create table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        project_id VARCHAR(255) NOT NULL,
        login_page_url VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


// Deactivation hook
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');

function my_plugin_deactivate()
{
    session_destroy();
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name

    // SQL query to drop table
    $query = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($query);
}


function enqueue_descope_scripts()
{
    wp_enqueue_script('descope-web-component', 'https://unpkg.com/@descope/web-component@latest/dist/index.js', array(), '1.0.0', true);
    wp_enqueue_script('descope-web-sdk', 'https://unpkg.com/@descope/web-js-sdk@1.0.0/dist/index.umd.js', array(), '1.0.0', true);
    wp_enqueue_script('descope-api-call', plugins_url('/src/descope-api-call.js', __FILE__), array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_descope_scripts');


function descope_wc_shortcode($atts)
{   
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';

    // Extract the redirect url, flow ID, and html tag id from the shortcode attributes
    $flowId = $atts['flow_id'];
    $id = $atts['id'];
    $redirectUrl = $atts['redirect_url'];

    // Extract projectId from table
    $projectID = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");

    // Return html
    $html = '<descope-wc id=' . $id . ' project-id=' . $projectID . ' flow-id=' . $flowId . ' redirect_url=' . $redirectUrl . '></descope-wc>';
    return $html;
}
add_shortcode('descope-wc', 'descope_wc_shortcode');


function descope_session_shortcode($atts, $content = null)
{
    session_start();

    $base_url = get_site_url();
    // If session_token info is present
    if (!isset($_SESSION['SESSION_TOKEN'])) {
        echo "No session token present";
        // If DSR cookie is set
        if (isset($_COOKIE['DSR']) && refresh_token($_COOKIE['DSR'])) {
            echo "Session token was successfully refreshed, with DSR cookie";
        } else {
            echo "DSR Cookie was not set or refresh failed";
            logout_redirect();
        }
    } else {
        echo "Session token present";
        // If session_token expiry is in the future
        if (isset($_SESSION['SESSION_EXPIRY']) && time() < $_SESSION['SESSION_EXPIRY']) {
            echo "Session was not expired so we're good";
        // If refresh_token is present, attempt refresh with refresh_token
        } else if (isset($_SESSION['REFRESH_TOKEN']) && refresh_token($_SESSION['REFRESH_TOKEN'])) {
            echo "Session token was successfully refreshed";
        } else {
            login_redirect();
        }
    }
}
add_shortcode('descope-session', 'descope_session_shortcode');


function refresh_token($refresh_token) 
{
    // Attempt to refresh the session token, with the refresh_token
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';

    $client = new GuzzleHttp\Client();
    $projectId = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");
    $auth_token = $projectId . ':' . $refresh_token;
    $res = $client->request(
        'POST',
        'https://api.descope.com/v1/auth/refresh',
        ['headers' => 
            [
            'Authorization' => "Bearer {$auth_token}",
            'Content-Type' => "application/json",
            ]
        ]
    );

    // If session token was refreshed successfully, reset $_SESSION info
    if ($res->getStatusCode() == 200) {
        $_SESSION["SESSION_TOKEN"] = json_decode($res->getBody(), true)["sessionJwt"];
        $_SESSION["SESSION_EXPIRY"] = json_decode($res->getBody(), true)["cookieExpiration"];
        return true;
    }
    return false;
}

function logout_redirect() 
{
    session_destroy();
    
    // Unset cookie
    unset($_COOKIE['DSR']);
    setcookie('user_name', '', time() - 3600, '/');

    global $wpdb;
    $table_name = $wpdb->prefix . 'descope';

    // Get redirect URL from DB
    $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    $base_url = get_site_url();
    $pageUrl = $base_url . '/' . $login_page_url;
    header("location:" . $pageUrl);
    exit;
}

// function get_db_info()
// {   
//     // Get all attributes from DB
//     $table_name = $wpdb->prefix . 'descope';
//     $project_id = $wpdb->get_var("SELECT project_id FROM $table_name");
//     $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
    
//     // Return JSON of DB information
//     $db_info = array('table_name' => $table_name, 'project_id' => $project_id, 'login_page_url' => $login_page_url);
//     return json_encode($db_info);
// }


function descope_plugin_add_menu_item()
{
    add_menu_page(
        'Descope-plugin',
        // Page title
        'Descope Config',
        // Menu title
        'manage_options',
        // Capability required to access this page
        'descope-plugin',
        // Menu slug
        'descope_plugin_display_page' // Function to display the contents of this page
    );
}
add_action('admin_menu', 'descope_plugin_add_menu_item');


// Display the contents of your new page
function descope_plugin_display_page()
{
    ?>
    <div class="wrap">
        <h1>Descope Configuration</h1>
        <?php
        // Query after the input values are submitted
        if (isset($_POST['submit'])) {
            // Sanitize input fields
            $new_project_id = isset($_POST['descope_input']) ? sanitize_text_field($_POST['descope_input']) : '';
            $new_redirect_url = isset($_POST['login_page_url']) ? sanitize_text_field($_POST['login_page_url']) : '';

            global $wpdb;
            $table_name = $wpdb->prefix . 'descope';
            
            // Check if there is an existing row in the table
            $existing_row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

            if ($existing_row !== null) {
                // An existing row is found, update both fields
                $wpdb->update(
                    $table_name,
                    array('project_id' => $new_project_id, 'login_page_url' => $new_redirect_url),
                    array('id' => $existing_row->id),
                    array('%s', '%s'),
                    array('%d')
                );
            } else {
                // No existing row found, insert a new row
                $wpdb->query("ALTER TABLE {$wpdb->prefix}descope AUTO_INCREMENT = 1");

                $wpdb->insert(
                    $table_name,
                    array('project_id' => $new_project_id, 'login_page_url' => $new_redirect_url),
                    array('%s', '%s')
                );
            }
        }

        global $wpdb;
        // Query to get info from DB
        $table_name = $wpdb->prefix . 'descope';
        $project_id = $wpdb->get_var("SELECT project_id FROM $table_name");
        $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
        
        ?>

        <head>
            <style>
                .projectid-but {
                    background: #1B769C;
                    border: none;
                    color: white;
                    padding: 5px 20px;
                    border-radius: 6px;
                    cursor: pointer;
                }

                .projectid-but:disabled {
                    background: white;
                    color: black;
                    border: 1px solid black;
                    cursor: not-allowed;
                }
            </style>
        </head>

        <form method="post">
            <div class="input-boxes-descope">
                <!-- Input box for projectID -->
                <label for="descope_input">Enter your Project ID:</label>
                <input type="text" id="descope_input" name="descope_input" onkeyup="validateInput()"
                    value="<?php echo $project_id; ?>"><br /><br />
                <!-- Input box for redirect url if session token does not exist -->
                <label for="descope_input">Enter redirect url if session token does not exist:</label>
                <input type="text" id="login_page_url" name="login_page_url" onkeyup="validateInput()"
                    value="<?php echo $login_page_url; ?>"><br /><br /><br />
                <input class="projectid-but" type="submit" id="submit-btn" name="submit" value="Submit" disabled>
            </div>
        </form>
    </div>
    <script>
        // Function to enable the button only when input length of both the input boxes is greater than 0
        function validateInput() {
            var descopeInput = document.getElementById('descope_input');
            var sessionRedirectUrlInput = document.getElementById('login_page_url');
            var submitBtn = document.getElementById('submit-btn');
            if (descopeInput.value.length > 0 && sessionRedirectUrlInput.value.length > 0) {
                submitBtn.disabled = false;
            } else { submitBtn.disabled = true; }
        }

    </script>
    <?php
}