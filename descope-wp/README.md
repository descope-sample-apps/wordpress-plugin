![WordPress by Descope]

WordPress Plugin for [Descope](https://www.descope.com/) Authentication

[![License](https://raw.githubusercontent.com/descope/descope-js/main/LICENSE)](https://doge.mit-license.org/)

:rocket: [Getting Started](#getting-started) - :computer: [SDK Usage](#sdk-usage) - 📆 [Support Policy](#support-policy) - :speech_balloon: [Feedback](#feedback)

## Getting Started

### Requirements

- PHP 8.0+
- [Most recent version of WordPress](https://wordpress.org/news/category/releases/)
- Database credentials with table creation permissions

> Please review our [support policy](#support-policy) on specific PHP and WordPress versions and when they may exit support in the future.

### Installation

<!-- // Disabled while we complete this distribution configuration
#### Release Package
Releases are available from the GitHub repository [github.com/auth0/wordpress/releases](https://github.com/auth0/wordpress/releases), packaged as ZIP archives. Every release has an accompanying signature file for verification if desired.

<details>
<summary><b>Verify a release signature with OpenSSL (recommended)</b></summary>

1. Download the public signing key from this repository
2. Put the repository's public signing key, the release's ZIP archive, and the release's signature file (ending in `.sign`) in the same directory.
3. Run the following command, substituting `RELEASE` with the filename of the release you downloaded:

```bash
openssl dgst -verify signing.key.pub -keyform PEM -sha256 -signature RELEASE.zip.sign -binary RELEASE.zip
```

'Verified OK' should be returned. If this is not the case, do not proceed with the installation.
</details>

1. Open your WordPress Dashboard, then click 'Plugins', and then 'Add New'.
2. Find the 'Upload Plugin' function at the top of the page, and use it to upload the release package you downloaded.

> **Note** Alternatively, you can extract the release package to your WordPress installation's `wp-content/plugins` directory.
-->

#### Composer

The plugin supports installation through [Composer](https://getcomposer.org/), and is [WPackagist](https://wpackagist.org/) compatible. This approach is preferred when using [Bedrock](https://roots.io/bedrock/) or [WordPress Core](https://github.com/johnpbloch/wordpress-core-installer), but will work with virtually any WordPress installation.

When using Composer-based WordPress configurations like Bedrock, you'll usually run this command from the root WordPress installation directory. Still, it's advisable to check the documentation the project's maintainers provided for the best guidance. This command can be run from the `wp-content/plugins` sub-directory for standard WordPress installations.

```
composer require symfony/http-client nyholm/psr7 auth0/wordpress:^5.0
```

<p><details>
<summary><b>Note on Composer Dependencies</b></summary>

When installed with Composer, the plugin depends on the presence of [PSR-18](https://packagist.org/providers/psr/http-client-implementation) and [PSR-17](https://packagist.org/providers/psr/http-factory-implementation) library implementations. The `require` command above includes two such libraries (`symfony/http-client` and `nyholm/psr7`) that satisfy these requirements, but you can use any other compatible libraries that you prefer. Visit Packagist for a list of [PSR-18](https://packagist.org/providers/psr/http-client-implementation) and [PSR-17](https://packagist.org/providers/psr/http-factory-implementation) providers.

If you are using Bedrock or another Composer-based configuration, you can try installing `auth0/wordpress` without any other dependencies, as the implementations may be satisfied by other already installed packages.

> **Note** PHP Standards Recommendations (PSRs) are standards for PHP libraries and applications that enable greater interoperability and choice. You can learn more about them and the PHP-FIG organization that maintains them [here](https://www.php-fig.org/).

</details></p>

<!-- // Disabled while we complete this distribution configuration
#### WordPress Dashboard

Installation from your WordPress dashboard is also supported. This approach first installs a small setup script that will verify that your host environment is compatible. Afterward, the latest plugin release will be downloaded from the GitHub repository, have its file signature verified, and ultimately installed.

- Open your WordPress Dashboard.
- Click 'Plugins", then 'Add New,' and search for 'Auth0'.
- Choose 'Install Now' to install the plugin.
-->

### Activation

After installation, you must activate the plugin within your WordPress site:

1. Open your WordPress Dashboard.
2. Select 'Plugins' from the sidebar, and then 'Installed Plugins.'
3. Choose 'Activate' underneath the plugin's name.

### Configure Auth0
