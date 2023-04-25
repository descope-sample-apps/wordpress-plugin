# <a title="WordPress, GPL &lt;http://www.gnu.org/licenses/gpl.html&gt;, via Wikimedia Commons" href="https://wordpress.org/"><img width="64" alt="WordPress blue logo" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/WordPress_blue_logo.svg/64px-WordPress_blue_logo.svg.png"></a> by Descope

## WordPress Plugin for [Descope](https://www.descope.com/) Authentication

[![License: GPL v2](https://img.shields.io/badge/License-GPL_v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

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

> **NOTE**: Before adding shortcodes, make sure that you're using `Post Names` (or slugs) for your website's permalink structure. You can find instructions on how to change your permalink structures [here](https://yoast.com/help/how-do-i-change-the-permalink-structure/). If this is not configured, you might see this error message when adding shortcode: ![image](https://www.zeninvader.com/static/805659149ef98f3ef91aadb66457a92f/86bd0/wordpress-publishing-failed-valid-json.jpg)

First, you're going to want to add the Descope flows tag to one of your pages (this will be where the user signs in). To add the Descope flow page to your website, just add a shortcode block to the main text area of any page, and add the shortcode `[descope-wc id="login" redirect_url="about" flow_id="sign-up-or-in" /]`.

1. The **id** is the HTML component tag for the Descope login flows screen.
2. The **redirect_url** is where you would like Descope to redirect the client, after the login is **successful**.
3. The **flow_id** is the id of the Descope flow that you want to implement in your page. You can edit your flows [here](https://app.descope.com/flows), as well as fetch its ID.

### Protecting Pages

The final step to adding Descope to any of your website pages, is to add the session tag to the top of each page. The Descope WordPress plugin supports RBAC authorization for each of the pages, as well specific elements within a page.

Simply add the shortcode `[descope-session]` to a shortcode block in any of the pages you wish to require authentication for, and Descope will require authentication for any user that visits the page.

If you wish to protect the page with a specific role, you can just add that role after descope-session, like this:

`[descope-session role="admin"]`

> **NOTE**: You will need to make sure that your WordPress hosting service is not page caching and preventing the plugin from being able to read the `DS_SESSION` cookie from the browser's localStorage. If you're using a hosting service like [WPEngine](https://wpengine.com/), you will need to contact their support team to disable page caching when detecting a cookie with the name `DS_SESSION` so that it can be read by the PHP code. After that, this plugin should work seamlessly.

The cookie itself is:

```
httponly = true
secure = true
samesite = Strict
```

### Logout

You will notice a new logout page that is created when you activate the plugin in your WordPress website. This page will automatically clear all cookies and invalid your refresh tokens using the WebJS SDK.

From that page, you can link any button or text to that page (`https://<your wordpress website base url>/descope-logout`) and it will automatically log your user out and invalidate all of the cookies.

---

And you're done! You can refer to the step-by-step installation/usage tutorial [here]() if you are having trouble with any of the installation steps.

If you have any questions about Descope, feel free to [reach out](https://docs.descope.com/support/)!
