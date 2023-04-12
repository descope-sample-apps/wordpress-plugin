# <a title="WordPress, GPL &lt;http://www.gnu.org/licenses/gpl.html&gt;, via Wikimedia Commons" href="https://wordpress.org/"><img width="64" alt="WordPress blue logo" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/WordPress_blue_logo.svg/64px-WordPress_blue_logo.svg.png"></a> by Descope

## WordPress Plugin for [Descope](https://www.descope.com/) Authentication

[![License](https://img.shields.io/packagist/l/auth0/auth0-php)](https://doge.mit-license.org/)

## Getting Started

### Requirements

- PHP 8.0.2+
- [Most recent version of WordPress](https://wordpress.org/news/category/releases/)
- Database credentials with table creation permissions

### Installation

Installation is very straight-forward. Since the app uses Composer to manage the packages, it will handle the entire build process for you.

Either search for the Descope plugin in the WordPress Plugin Store, or zip up this entire repo and then import it into WordPress on your WordPress Dashboard. You can follow the instructions [here](https://www.wpbeginner.com/beginners-guide/step-by-step-guide-to-install-a-wordpress-plugin-for-beginners/).

### Activation

After installation, you must activate the plugin within your WordPress site:

1. Open your WordPress Dashboard.
2. Select **Plugins** from the sidebar, and then **Installed Plugins.**
3. Choose **Activate** underneath the plugin's name.

### Configuring Descope

Once you have installed and activated your plugin, you must go to the new `Descope Config` menu in the left-hand sidebar and input the following:

1. `Project ID` - this is your Descope Project ID you can get from the settings page [here](https://app.descope.com/settings/project).
2. `Redirect URL` - redirect URL if the login is **unsuccessful**.

---

Now that you've set everything up in the background, let's integrate the plugin features in your actual website.

### Add Descope Flows to your WP Pages

First, you're going to want to add the Descope flows tag to one of your pages (this will be where the user signs in). To add the Descope flow page to your website, just add the tag `[descope-wc id="login" redirect_url="about" flow_id="sign-up-or-in"]` to the main text area of any page.

1. The **id** is the HTML component tag for the Descope login flows screen.
2. The **redirect_url** is where you would like Descope to redirect the client, after the login is **successful**.
3. The **flow_id** is the id of the Descope flow that you want to implement in your page. You can edit your flows [here](https://app.descope.com/flows), as well as fetch its ID.

### Protecting Pages

The second and final step to adding Descope to any of your website pages, is to add the session tag to the top of each page.
Simply add the tag `[descope-session]` to the top of the body in any of the pages you wish to require authentication for, and Descope will require authentication for any user that visits the page.

And you're done! You can refer to the step-by-step installation/usage tutorial [here]() if you are having trouble with any of the installation steps.

If you have any questions about Descope, feel free to [reach out](https://docs.descope.com/support/)!
