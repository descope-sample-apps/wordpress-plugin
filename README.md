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
2. `Redirect URL` - the slug of the page you would like to redirect to, if the login is **unsuccessful**. You can find the slug of the page you would like to redirect to, by going to the `Pages` menu in WordPress and selecting **Quick Edit** underneath the page you want.

---

Now that you've set everything up in the background, let's integrate the plugin features in your actual website. To add any of these tags, you'll need to use shortcode blocks. If you're unfamiliar with WordPress, you can add a shortcode block by following these steps [here](https://wordpress.com/support/wordpress-editor/blocks/shortcode-block/).

### Add Descope Flows to your WP Pages

First, you're going to want to add the Descope flows tag to one of your pages (this will be where the user signs in). To add the Descope flow page to your website, just add a shortcode block to the main text area of any page, and add the shortcode `[descope-wc id="login" redirect_url="about" flow_id="sign-up-or-in" /]`.

1. The **id** is the HTML component tag for the Descope login flows screen.
2. The **redirect_url** is where you would like Descope to redirect the client, after the login is **successful**.
3. The **flow_id** is the id of the Descope flow that you want to implement in your page. You can edit your flows [here](https://app.descope.com/flows), as well as fetch its ID.

### Protecting Pages

The second and final step to adding Descope to any of your website pages, is to add the session tag to the top of each page.
Simply add the shortcode `[descope-session]` to a shortcode block in any of the pages you wish to require authentication for, and Descope will require authentication for any user that visits the page.

**One caveat**: You will need to make sure that your WordPress hosting service is not page caching and preventing the plugin from being able to read the `DS_SESSION` cookie from the browser's localStorage.

The cookie itself is:

```
httponly = true
secure = true
samesite = Strict
```

However, if you're using a hosting service like [WPEngine](https://wpengine.com/), you will need to contact their support team to disable page caching when detecting a cookie with the name `DS_SESSION` so that it can be read by the PHP code. After that, this plugin should work seamlessly.

### Logout

There are two ways you can implement logout, depending on how you want it to look in your website:

1. If you would like to create an entirely new page for logout, all you have to do is create a new page in your WordPress admin page, create a new shortcode block in the page body, and then add the `[descope-logout]` tag. This page doesn't need to contain anything else, as the user will automatically be redirected to the login page after the authentication cookie is unset.

2. If you would like to contain logout in clickable text or a button somewhere within a page, you will have to embed the HTML link `https://<your wordpress website base url>/wp-content/plugins/wordpress-plugin/src/descope-logout.php` into the text or button. This will also redirect you to the login page, after the unset the authentication cookie.

---

And you're done! You can refer to the step-by-step installation/usage tutorial [here]() if you are having trouble with any of the installation steps.

If you have any questions about Descope, feel free to [reach out](https://docs.descope.com/support/)!
