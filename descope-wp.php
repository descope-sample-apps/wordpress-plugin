<?php

require __DIR__ . '/vendor/autoload.php';
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
    wp_enqueue_script('descope-web-component', 'https://unpkg.com/@descope/web-component@latest/dist/index.js', array(), '1.0.0');
    wp_enqueue_script('descope-web-sdk', 'https://unpkg.com/@descope/web-js-sdk@1.0.0/dist/index.umd.js', array(), '1.0.0');
    wp_enqueue_script('descope-api-call', plugins_url('/src/descope-api-call.js', __FILE__), array(), '1.0.0');
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
    $html = '<div id="descope_flow_div"></div>';
    $html .= "<script>inject_flow('$projectID', '$flowId', '$redirectUrl')</script>";

    return $html;
}
add_shortcode('descope-wc', 'descope_wc_shortcode');

function descope_wc_pre_post_update($post_ID, $data)
{
    // Check if the post contains the descope-wc shortcode
    $post_content = $data['post_content'];
    if (strpos($post_content, '[descope-wc') !== false) {
        // Extract the shortcode attributes
        $shortcode_attributes = shortcode_parse_atts($post_content);

        // Extract the project_id and flow_id from the shortcode attributes
        global $wpdb;

        $table_name = $wpdb->prefix . 'descope'; // adding default prefix to table name
        // Storing the value
        $project_id = $wpdb->get_var("SELECT project_id FROM $table_name WHERE id = 1");
        $id = $shortcode_attributes['id'];
        $redirectUrl = $shortcode_attributes['redirect_url'];
        $flow_id = $shortcode_attributes['flow_id'];

        // Check if project ID  are set
        if (empty($project_id)) {
            $error_message = 'Please enter project id and redirect URL under "Descope Config" from navigation panel.';
            add_action('admin_notices', function () use ($error_message) {
                echo '<div class="error"><p>' . $error_message . '</p></div>';
            });
            wp_die(__($error_message));
        }

        // Check if id, flow ID, and redirect URL are set
        if (empty($id) || empty($flow_id) || empty($redirectUrl)) {
            $missing_attributes = array();

            if (empty($id)) {
                $missing_attributes[] = 'id';
            }
            if (empty($flow_id)) {
                $missing_attributes[] = 'flow_id';
            }
            if (empty($redirectUrl)) {
                $missing_attributes[] = 'redirect_url';
            }

            $error_message = 'Please enter the following attributes in the "descope-wc" shortcode: ' . implode(', ', $missing_attributes) . '.';
            add_action('admin_notices', function () use ($error_message) {
                echo '<div class="error"><p>' . $error_message . '</p></div>';
            });
            wp_die(__($error_message));

        }
    }
}

add_action('pre_post_update', 'descope_wc_pre_post_update', 10, 2);

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
        [
            'headers' =>
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
                    background: #2271b1;
                    border-color: #2271b1;
                    width: 100px;
                    height: 34px;
                    border: none;
                    color: white;
                    padding: 5px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }


                .projectid-but:disabled {
                    background: white;
                    color: black;
                    border: 1px solid black;
                    cursor: not-allowed;
                }

                .input-boxes-descope {
                    margin-top: 24px;
                }

                .descope-custom-input {
                    margin-left: 46px;
                    border: 1px solid black;
                    width: 245px;
                    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
                    height: 36px;
                }

                label.input-box-label {
                    font-weight: 600;
                }

                .td-padding {
                    padding: 20px;
                }
            </style>
        </head>

        <form id="my-form" method="post">
            <div class="input-boxes-descope">
                <!-- Input box for projectID -->
                <table>
                    <tr>
                        <td class="td-padding">
                            <label class="input-box-label" for="descope_input">Enter your Project ID:</label>
                        </td>
                        <td class="td-padding">
                            <input type="text" class="descope-custom-input" id="descope_input" name="descope_input"
                                onkeyup="validateInput()" required value="<?php echo $project_id; ?>">
                        </td>
                        <!-- Input box for redirect url if session token does not exist -->
                    <tr class="td-padding">
                        <td class="td-padding">
                            <label class="input-box-label" for="descope_input">Enter redirect url (slug) if session<br />
                                token
                                does not
                                exist:</label>
                        </td>
                        <td class="td-padding">
                            <input type="text" required class="descope-custom-input" id="login_page_url"
                                name="login_page_url" onkeyup="validateInput()" value="<?php echo $login_page_url; ?>">
                        </td>
                    </tr>
                </table>

                <div class="td-padding">
                    <input class="projectid-but" type="submit" id="submit-btn" name="submit" value="Submit" disabled>
                </div>

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