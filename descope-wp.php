<?php
/**
 *  @package DescopePlugin
 */




/**
 * * TODO: REview this. 
 * Plugin Name: Login by Descope
 * Description: Onboard your users like it’s 2023. Add passwordless authentication and user management to your app with a few lines of code. Choose from our drag-and-drop workflows, SDKs, or APIs.
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
    // Create table when the plugin activates
    global $wpdb;
    $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query to create table
    // TODO: Mayur, please change to login_page_url - DONE
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        project_id VARCHAR(255) NOT NULL,
        login_page_url VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
) $charset_collate;";


    // executing the query to create table
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
    $sql = "DROP TABLE IF EXISTS $table_name;";

    // executing the query to drop table
    $wpdb->query($sql);

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
    // Extract the project ID and flow ID from the shortcode attributes
    global $wpdb;
    global $var_dc;

    $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
    // Storing the value
    $idFromTable = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");
    // echo 
    $projectID = $idFromTable;
    $flowId = $atts['flow_id'];
    // $redirectUrl =$atts['redirectUrl'];
    $id = $atts['id'];
    $redirectUrl = $atts['redirect_url'];

    $var_dc = $projectID;
    $html = '<descope-wc id=' . $id . ' project-id=' . $projectID . ' flow-id=' . $flowId . ' redirect_url=' . $redirectUrl . '></descope-wc>';
    // Return the HTML
    return $html;

}

add_shortcode('descope-wc', 'descope_wc_shortcode');


function descope_session_shortcode($atts, $content = null)
{
    // TODO: Should this be where we start the session? 
    session_start();
    /**
     */
    // $_SESSION['SESSION_TOKEN'] = null;
    $base_url = get_site_url();
    if (!isset($_SESSION['SESSION_TOKEN'])) {
        //TODO: If there is an refresh token on the cookie. 
        //TODO: cookie name is DSR. 

        //TODO: we can set/unset COOKIE
        // if (isset($_COOKIE['user_name'])) {
        //     unset($_COOKIE['user_name']);
        //     setcookie('user_name', '', time() - 3600, '/'); // empty value and old timestamp
        // }

        global $wp, $wpdb;
        // $page_id = $wpdb->get_var('SELECT post_name FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[descope-wc%"');
        $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
        $login_page_url = $wpdb->get_var("SELECT login_page_url FROM $table_name");
        $pageUrl = $base_url . '/' . $login_page_url;


        // $_SESSION['REDIRECTED'] = true;
        header("Location: " . $pageUrl);
        exit;

    } else {
        //TODO: check the expiry date from _session
        // TODO: If expired, refresh with session token with refresh token
        // TODO: If not expired, then continue. 
    }



}

add_shortcode('descope-session', 'descope_session_shortcode');


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
            $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
    
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

        // query to get the project id from the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
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