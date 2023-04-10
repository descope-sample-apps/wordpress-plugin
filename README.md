# WordPress by Descope

## WordPress Plugin for [Descope](https://www.descope.com/) Authentication

[![License](https://img.shields.io/packagist/l/auth0/auth0-php)](https://doge.mit-license.org/)

## Getting Started

### Requirements

- PHP 8.0.2+
- [Most recent version of WordPress](https://wordpress.org/news/category/releases/)
- Database credentials with table creation permissions

### Installation

Installation is very straight-forward. Since the app uses Composer to manage the packages, it will handle the entire build process for you.

Either search for the Descope plugin in the Wordpress Plugin Store, or zip up this entire repo and then import it into Wordpress on your Wordpress Dashboard. You can follow the instructions [here](https://www.wpbeginner.com/beginners-guide/step-by-step-guide-to-install-a-wordpress-plugin-for-beginners/).

### Activation

After installation, you must activate the plugin within your WordPress site:

1. Open your WordPress Dashboard.
2. Select 'Plugins' from the sidebar, and then 'Installed Plugins.'
3. Choose 'Activate' underneath the plugin's name.

### Configure Descope

Once you have installed and activated your plugin, you must go to the new `Descope Auth` menu in the left-hand sidebar and input your Descope Project ID and redirect URL for if the login is **unsuccessful**.

### Add Descope Flows to your WP Pages

To add the Descope flow page to your website, just add the tag `[descope-wc id="login" redirect_url="about" flow_id="sign-up-or-in"]` to the main text area of any page.

1. The **id** is the HTML component tag for the Descope login flows screen.
2. The **redirect_url** is where you would like Descope to redirect the client, after the login is **successful**.
3. The **flow_id** is the id of the Descope flow that you want to implement in your page. You can edit your flows [here](https://app.descope.com/flows), as well as fetch its ID.
