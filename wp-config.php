<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'descope_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'tasPyn>K5p0f)os~FlTMGG$C*WwAqVvQASgu$^2};r|;/f=eSTBK#oa$+h8Yi}rY' );
define( 'SECURE_AUTH_KEY',  'd($[148;lQD~:.5~}o``hti_lODC_e33?F9p%wy;7hx2.S[;%1gvY%vn/u4+uW)t' );
define( 'LOGGED_IN_KEY',    '/7^n*[5@D/C%,O&8/pkDRSpu{py9MJH {n9$/+U#ZL^xH}dG*xg3(0.C*,Qu{)Yt' );
define( 'NONCE_KEY',        'h%^t jK$Bdj#=#.WLt]fmR,URp>^lV#C@z49a>-i`_f):?ILD6oAe #+D&WfTQq~' );
define( 'AUTH_SALT',        '@Pgum7ugJEp;xxjujhnUu&E5jI7XdqqR2/|zOe)fk56)vk*$<_FF5.QPl1k!={U$' );
define( 'SECURE_AUTH_SALT', 'Go56_xb`~`]~Y(};1!q/A*37+yRs3mei33VlO)?Ch&^CN){<5{^+u)5FQPFwJs3{' );
define( 'LOGGED_IN_SALT',   'xlPlmmWQ3 p!dQ1nxilL3z<s_rvS-11~:bz_x*Wim0.]#sQPl*Xs<)05$^f6r~E%' );
define( 'NONCE_SALT',       'F+6=]i#s.e4Z>zj-IL<^$Z.OJy.Mn3(mqK*Gx}>G3?E}[f=.Bq{cL`e]?}#112ni' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
